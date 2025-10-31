<?php

return [
    'project_id' => env('GOOGLE_PROJECT_ID'),
    'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
    'dataset' => env('BQ_DATASET', 'crm_data'),
    'table' => env('BQ_TABLE', 'leads'),
];

