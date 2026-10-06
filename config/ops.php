<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Operations alert recipients
    |--------------------------------------------------------------------------
    | Email addresses notified about recorded website incidents. Configured
    | via OPS_ALERT_RECIPIENTS (comma-separated). Production values are a
    | Stage 8 deployment responsibility; empty means no alerts are sent.
    */
    'alert_recipients' => array_values(array_filter(array_map(
        fn ($address): string => trim((string) $address),
        explode(',', (string) env('OPS_ALERT_RECIPIENTS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Analytics retention
    |--------------------------------------------------------------------------
    | Raw analytics events older than this many days are pruned by the
    | analytics:prune command. Audit logs are never pruned by this command.
    */
    'analytics_retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 365),
];
