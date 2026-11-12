<?php

return [
    'max_upload_kb' => (int) env('CMS_MAX_UPLOAD_KB', 10240),
    'media_disk' => env('CMS_MEDIA_DISK', 'local'),
    'enquiries_sla_days' => (int) env('CMS_ENQUIRIES_SLA_DAYS', 5),
];
