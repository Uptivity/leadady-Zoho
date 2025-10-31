<?php

namespace App\Services;

class LeadTransformer
{
    protected array $mapping;

    public function __construct()
    {
        $this->mapping = config('lead_mapping', []);
    }

    /**
     * Transform a BigQuery row (assoc array) to the destination payload schema.
     * If no mapping is defined, returns the original row.
     */
    public function transform(array $row): array
    {
        if (empty($this->mapping)) {
            return $row;
        }

        $out = [];
        foreach ($this->mapping as $destField => $spec) {
            // Spec can be:
            // - string: single BigQuery column name
            // - array of strings: multiple columns, join with ". "
            // - associative array: ['columns' => [...], 'join' => '. ', 'note' => '...']
            if (is_string($spec)) {
                $out[$destField] = $this->normalize($row[$spec] ?? null);
                continue;
            }

            if (is_array($spec)) {
                $columns = $spec['columns'] ?? $spec; // allow shorthand array
                $joiner = isset($spec['join']) ? (string) $spec['join'] : '. ';
                $values = [];
                foreach ((array) $columns as $col) {
                    $val = $this->normalize($row[$col] ?? null);
                    if ($val !== null && $val !== '') {
                        $values[] = $val;
                    }
                }
                // De-duplicate consecutive duplicates after normalization
                $values = array_values(array_unique($values));
                $out[$destField] = $this->normalize(implode($joiner, $values));
                continue;
            }

            // Unknown spec; skip
        }

        return $out;
    }

    /**
     * Transform an array of rows.
     */
    public function transformMany(array $rows): array
    {
        return array_map(fn ($r) => $this->transform($r), $rows);
    }

    protected function normalize($value): ?string
    {
        if ($value === null) return null;
        if (is_array($value)) $value = implode(', ', array_filter(array_map('strval', $value)));
        $s = trim((string) $value);
        // Collapse internal whitespace
        $s = preg_replace('/\s+/u', ' ', $s ?? '') ?? '';
        // Remove extra trailing punctuation spacing
        $s = preg_replace('/\s*([\.,;:])\s*/u', '$1 ', $s);
        // Clean up trailing joiners/punctuation
        $s = rtrim($s, " \t\n\r\0\x0B");
        return $s;
    }
}
