<?php

namespace App\Services\Mail;

use App\Mail\SmtpTestMail;
use App\Models\MailConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Throwable;

class SmtpDiagnosticsService
{
    /**
     * Standard allowable SMTP ports.
     */
    public const ALLOWED_PORTS = [25, 465, 587, 2525];

    /**
     * Validate the provided host and port against SSRF and network isolation rules.
     *
     * @return array{valid: bool, error: ?string}
     */
    public function validateHostAndPort(string $host, int $port): array
    {
        $host = trim($host);

        if (empty($host)) {
            return ['valid' => false, 'error' => 'SMTP hostname cannot be empty.'];
        }

        if (! in_array($port, self::ALLOWED_PORTS, true)) {
            return ['valid' => false, 'error' => "Port {$port} is not an authorized SMTP port (allowed: 25, 465, 587, 2525)."];
        }

        // SSRF protection: reject local and internal IP ranges in non-local environments
        $ips = @dns_get_record($host, DNS_A + DNS_AAAA);
        $resolvedIps = [];

        if (is_array($ips)) {
            foreach ($ips as $record) {
                if (isset($record['ip'])) {
                    $resolvedIps[] = $record['ip'];
                } elseif (isset($record['ipv6'])) {
                    $resolvedIps[] = $record['ipv6'];
                }
            }
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $resolvedIps[] = $host;
        }

        if (app()->environment('production', 'staging')) {
            foreach ($resolvedIps as $ip) {
                if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return [
                        'valid' => false,
                        'error' => "Security violation: Host {$host} resolves to private/internal network address ({$ip}), which is blocked to prevent SSRF.",
                    ];
                }
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Inspect and test the low-level TCP socket and TLS certificate verification.
     * Crucial: never disables TLS verification, and explicitly inspects peer certificate
     * to diagnose certificate CN / SAN mismatches (e.g. hz4.vps.webdevworld.com vs smtp.gmail.com).
     *
     * @return array{
     *     success: bool,
     *     banner: ?string,
     *     tls_verified: bool,
     *     peer_certificate: ?array<string, mixed>,
     *     error: ?string
     * }
     */
    public function testSocketAndTls(string $host, int $port, string $encryption, int $timeoutSeconds = 8): array
    {
        $validation = $this->validateHostAndPort($host, $port);
        if (! $validation['valid']) {
            return [
                'success' => false,
                'banner' => null,
                'tls_verified' => false,
                'peer_certificate' => null,
                'error' => $validation['error'],
            ];
        }

        $scheme = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
                'capture_peer_cert' => true,
            ],
        ]);

        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            $scheme.$host.':'.$port,
            $errno,
            $errstr,
            $timeoutSeconds,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $socket) {
            return [
                'success' => false,
                'banner' => null,
                'tls_verified' => false,
                'peer_certificate' => null,
                'error' => "Could not open connection to {$host}:{$port}. ".$this->sanitizeError($errstr." ({$errno})"),
            ];
        }

        stream_set_timeout($socket, $timeoutSeconds);
        $banner = fgets($socket);

        // Read peer certificate details if captured
        $certDetails = null;
        $params = stream_context_get_params($socket);
        if (isset($params['options']['ssl']['peer_certificate'])) {
            $parsed = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            if (is_array($parsed)) {
                $certDetails = [
                    'subject_cn' => $parsed['subject']['CN'] ?? null,
                    'issuer' => $parsed['issuer']['O'] ?? ($parsed['issuer']['CN'] ?? null),
                    'valid_to' => isset($parsed['validTo_time_t']) ? date('Y-m-d H:i:s', $parsed['validTo_time_t']) : null,
                    'san' => $parsed['extensions']['subjectAltName'] ?? null,
                ];
            }
        }

        // If STARTTLS is expected on tcp socket
        $tlsVerified = $encryption === 'ssl';
        if ($encryption === 'tls' && ! $tlsVerified) {
            // Initiate EHLO to announce STARTTLS support
            fwrite($socket, "EHLO mutokordc.org.zw\r\n");
            $response = '';
            while ($line = fgets($socket)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') {
                    break;
                }
            }

            if (str_contains($response, 'STARTTLS')) {
                fwrite($socket, "STARTTLS\r\n");
                $starttlsReply = fgets($socket);
                if (str_starts_with((string) $starttlsReply, '220')) {
                    $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
                    if ($crypto === true) {
                        $tlsVerified = true;
                        $params = stream_context_get_params($socket);
                        if (isset($params['options']['ssl']['peer_certificate'])) {
                            $parsed = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                            if (is_array($parsed)) {
                                $certDetails = [
                                    'subject_cn' => $parsed['subject']['CN'] ?? null,
                                    'issuer' => $parsed['issuer']['O'] ?? ($parsed['issuer']['CN'] ?? null),
                                    'valid_to' => isset($parsed['validTo_time_t']) ? date('Y-m-d H:i:s', $parsed['validTo_time_t']) : null,
                                    'san' => $parsed['extensions']['subjectAltName'] ?? null,
                                ];
                            }
                        }
                    } else {
                        fclose($socket);

                        return [
                            'success' => false,
                            'banner' => trim((string) $banner),
                            'tls_verified' => false,
                            'peer_certificate' => $certDetails,
                            'error' => "STARTTLS handshake failed. The remote server presented a certificate that could not be verified for '{$host}'. Check hostname/SAN certificate alignment.",
                        ];
                    }
                }
            }
        }

        fclose($socket);

        return [
            'success' => true,
            'banner' => trim((string) $banner),
            'tls_verified' => $tlsVerified,
            'peer_certificate' => $certDetails,
            'error' => null,
        ];
    }

    /**
     * Test full SMTP authentication using Symfony Mailer EsmtpTransport.
     *
     * @return array{success: bool, error: ?string}
     */
    public function testAuthentication(
        string $host,
        int $port,
        string $encryption,
        ?string $username,
        ?string $password
    ): array {
        try {
            $scheme = match ($encryption) {
                'ssl' => 'smtps',
                default => 'smtp',
            };

            $userPart = ! empty($username) ? urlencode($username) : '';
            $passPart = ! empty($password) ? ':'.urlencode($password) : '';
            $authPart = $userPart !== '' ? "{$userPart}{$passPart}@" : '';

            $dsnString = "{$scheme}://{$authPart}{$host}:{$port}";
            $dsn = Dsn::fromString($dsnString);

            $factory = new EsmtpTransportFactory;
            $transport = $factory->create($dsn);

            // Symfony EsmtpTransport start() method connects, performs EHLO and AUTH
            if ($transport instanceof EsmtpTransport) {
                $transport->start();
                $transport->stop();
            }

            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $this->sanitizeError($e->getMessage()),
            ];
        }
    }

    /**
     * Dispatch an end-to-end test email to an authorized recipient using the specified configuration.
     *
     * @return array{success: bool, error: ?string}
     */
    public function sendTestEmail(
        MailConfiguration $config,
        string $recipientEmail,
        ?User $actor = null
    ): array {
        try {
            // Apply configuration parameters to dynamic transport
            app(MailConfigurationService::class)->applyRuntimeConfig($config);

            $summary = [
                'provider' => $config->provider,
                'host' => $config->host,
                'port' => $config->port,
                'encryption' => $config->encryption,
                'from_address' => $config->from_address,
            ];

            $actorName = $actor ? $actor->name : 'Administrator';
            Mail::to($recipientEmail)->send(new SmtpTestMail($summary, $actorName));

            $config->update([
                'last_tested_at' => now(),
                'last_tested_status' => 'success',
                'last_tested_error' => null,
            ]);

            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            $sanitized = $this->sanitizeError($e->getMessage());

            $config->update([
                'last_tested_at' => now(),
                'last_tested_status' => 'failed',
                'last_tested_error' => $sanitized,
            ]);

            return ['success' => false, 'error' => $sanitized];
        }
    }

    /**
     * Remove sensitive authentication secrets, passwords, or tokens from error strings.
     */
    public function sanitizeError(string $message): string
    {
        // Redact potential passwords, authorization credentials, and base64 hashes
        $message = preg_replace('/password[:=]\s*[^\s,]+/i', 'password=***', $message);
        $message = preg_replace('/AUTH (?:PLAIN|LOGIN) [A-Za-z0-9+\/=]+/i', 'AUTH [REDACTED]', $message);
        $message = preg_replace('/\b[A-Za-z0-9_-]{20,}\b/', '***', $message);

        return trim($message);
    }
}
