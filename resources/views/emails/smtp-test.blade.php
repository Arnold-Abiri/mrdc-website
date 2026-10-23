<x-mail::message>
# SMTP Configuration Test Email

Hello {{ $recipientName }},

This email confirms that the **Mutoko Rural District Council** website is successfully configured to dispatch emails using the SMTP credentials managed in your administration panel.

<x-mail::table>
| Configuration Parameter | Value |
| :--- | :--- |
| **Provider** | {{ ucfirst($configSummary['provider'] ?? 'Custom') }} |
| **Host** | `{{ $configSummary['host'] ?? 'N/A' }}` |
| **Port** | `{{ $configSummary['port'] ?? 587 }}` |
| **Transport Security** | `{{ strtoupper($configSummary['encryption'] ?? 'TLS') }}` |
| **Sender Email** | `{{ $configSummary['from_address'] ?? 'N/A' }}` |
| **Dispatched At** | {{ now()->toRfc2822String() }} |
</x-mail::table>

<x-mail::panel>
**Security Note:**  
This test was initiated by an authenticated System Administrator from the Mutoko RDC administration panel.
</x-mail::panel>

Regards,<br>
**{{ config('mail.from.name', 'Mutoko Rural District Council') }}**
</x-mail::message>
