<?php

return [
    'base_url' => env('DEST_API_BASE_URL'),
    'api_key' => env('DEST_API_KEY'),
    'batch_size' => (int) env('DEST_BATCH_SIZE', 200),
    'pull_max_rows' => (int) env('PULL_MAX_ROWS', 50000),
];

