<?php

return [
    // Define mappings from BigQuery columns (columns.txt) to destination fields.
    // Supported forms:
    // 1) 'DestField' => 'BigQueryColumn'
    // 2) 'DestField' => ['BigQueryColA', 'BigQueryColB'] // joined with ". "
    // 3) 'DestField' => ['columns' => ['A','B'], 'join' => '. ', 'note' => 'nearest dropdown option']
    // Notes are informational; transformation uses columns + join.

    // Examples (replace with real mapping from the PDF):
    // 'full_name' => 'Full_name',
    // 'company' => 'Company_Name',
    // 'phones' => ['Mobile', 'Phone_numbers'],
    // 'online_presence' => ['columns' => ['Company_Website','Company_Linkedin_Url','Company_Twitter_Url'], 'join' => '. ', 'note' => 'append with full stop + space'],
];
