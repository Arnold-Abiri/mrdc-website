<?php

namespace App\Domain\Operations;

use App\Models\ErrorEvent;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorMonitor
{
    private static bool $capturing = false;

    public function capture(Throwable $exception): ?ErrorEvent
    {
        if (self::$capturing) {
            return null;
        }
        if ($exception instanceof ValidationException
            || $exception instanceof AuthenticationException
            || $exception instanceof AuthorizationException) {
            return null;
        }
        if ($exception instanceof HttpException && $exception->getStatusCode() < 500) {
            return null;
        }
        self::$capturing = true;
        try {
            $route = request()->route();
            $routeName = $route instanceof Route ? (string) $route->getName() : null;

            return ErrorEvent::query()->create([
                'exception_class' => $exception::class,
                'summary' => mb_substr($this->sanitize($exception->getMessage()), 0, 500),
                'route' => $routeName,
                'correlation_id' => (string) Str::uuid(),
            ]);
        } catch (Throwable $failure) {
            // Never re-report: the error pipeline itself may be broken (e.g. database down).
            error_log('ErrorMonitor capture failed: '.$failure->getMessage());

            return null;
        } finally {
            self::$capturing = false;
        }
    }

    public function sanitize(string $message): string
    {
        $message = (string) preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[redacted-email]', $message);
        $message = (string) preg_replace('/(password|passwd|secret|token|api[-_]?key)\s*[:=]\s*\S+/i', '$1=[redacted]', $message);

        return trim($message) === '' ? '(no message)' : trim($message);
    }
}
