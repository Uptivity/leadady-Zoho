<?php

namespace App\Services\BigQuery;

use App\Services\Contracts\BigQueryServiceInterface;

class BigQueryService implements BigQueryServiceInterface
{
    protected ?object $client = null; // Google\Cloud\BigQuery\BigQueryClient when available

    protected string $project;
    protected string $dataset;
    protected string $table;

    public function __construct()
    {
        // Defer actual client initialization to first use to keep scaffolding light.
        $this->project = (string) config('bigquery.project_id');
        $this->dataset = (string) config('bigquery.dataset', 'crm_data');
        $this->table = (string) config('bigquery.table', 'leads');
    }

    public function getCounts(array $filters, array $required = []): array
    {
        // Build SQL for counts using only the base filters (required toggles are for refining preview/pulls).
        [$where, $params] = $this->buildWhere($filters, []);
        $fqtn = $this->fqtn();
        $sql = "WITH base AS (\n  SELECT\n    SAFE.LENGTH(TRIM(Mobile)) > 0 OR SAFE.LENGTH(TRIM(Phone_numbers)) > 0 AS has_phone,\n    SAFE.LENGTH(TRIM(Emails)) > 0 AS has_email,\n    SAFE.LENGTH(TRIM(Company_Website)) > 0 AND SAFE.LENGTH(TRIM(Emails)) > 0 AS has_company_email\n  FROM $fqtn\n  $where\n)\nSELECT\n  COUNTIF(TRUE) AS total,\n  COUNTIF(has_phone) AS with_phone,\n  COUNTIF(has_email) AS with_email,\n  COUNTIF(has_company_email) AS with_company_email\n";

        $rows = $this->runQuery($sql, $params);
        if (!$rows) {
            return [
                'total' => 0,
                'with_phone' => 0,
                'with_email' => 0,
                'with_company_email' => 0,
            ];
        }
        $r = $rows[0];
        return [
            'total' => (int) ($r['total'] ?? 0),
            'with_phone' => (int) ($r['with_phone'] ?? 0),
            'with_email' => (int) ($r['with_email'] ?? 0),
            'with_company_email' => (int) ($r['with_company_email'] ?? 0),
        ];
    }

    public function getLeads(array $filters, array $required, int $page, int $perPage = 100): array
    {
        [$where, $params] = $this->buildWhere($filters, $required);
        $fqtn = $this->fqtn();
        // Total count for current filters + required toggles
        $countSql = "SELECT COUNT(1) AS total FROM $fqtn $where";
        $countRows = $this->runQuery($countSql, $params);
        $total = (int) (($countRows[0]['total'] ?? 0) ?? 0);

        // Page data
        $offset = max(0, ($page - 1) * $perPage);
        $selectSql = "SELECT * FROM $fqtn $where LIMIT @limit OFFSET @offset";
        $selectParams = array_merge($params, [
            'limit' => $perPage,
            'offset' => $offset,
        ]);
        $data = $this->runQuery($selectSql, $selectParams);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function iterateForPull(array $filters, array $required, int $batchSize, int $maxRows, callable $onBatch): void
    {
        [$where, $params] = $this->buildWhere($filters, $required);
        $fqtn = $this->fqtn();

        $sent = 0;
        $page = 1;
        while ($sent < $maxRows) {
            $limit = min($batchSize, $maxRows - $sent);
            if ($limit <= 0) { break; }
            $offset = ($page - 1) * $batchSize;
            $sql = "SELECT * FROM $fqtn $where LIMIT @limit OFFSET @offset";
            $batch = $this->runQuery($sql, array_merge($params, ['limit' => $limit, 'offset' => $offset]));
            if (empty($batch)) { break; }
            $onBatch($batch);
            $count = count($batch);
            $sent += $count;
            if ($count < $limit) { break; }
            $page++;
        }
    }

    protected function fqtn(): string
    {
        // Fully qualified table name: `project`.`dataset`.`table`
        $p = trim($this->project);
        $d = trim($this->dataset);
        $t = trim($this->table);
        if ($p !== '') {
            return sprintf('`%s`.`%s`.`%s`', $p, $d, $t);
        }
        // Project-less reference (uses default project from credentials)
        return sprintf('`%s`.`%s`', $d, $t);
    }

    protected function buildWhere(array $filters, array $required): array
    {
        $clauses = [];
        $params = [];
        if (!empty($filters['industry'])) {
            $clauses[] = 'LOWER(Industry) LIKE @industry';
            $params['industry'] = '%' . strtolower((string) $filters['industry']) . '%';
        }
        if (!empty($filters['company_industry'])) {
            $clauses[] = 'LOWER(Company_Industry) LIKE @company_industry';
            $params['company_industry'] = '%' . strtolower((string) $filters['company_industry']) . '%';
        }
        if (!empty($filters['location'])) {
            $clauses[] = 'LOWER(Location) LIKE @location';
            $params['location'] = '%' . strtolower((string) $filters['location']) . '%';
        }

        if (!empty($required['phone'])) {
            $clauses[] = '(SAFE.LENGTH(TRIM(Mobile)) > 0 OR SAFE.LENGTH(TRIM(Phone_numbers)) > 0)';
        }
        if (!empty($required['email'])) {
            $clauses[] = 'SAFE.LENGTH(TRIM(Emails)) > 0';
        }
        if (!empty($required['company_email'])) {
            $clauses[] = '(SAFE.LENGTH(TRIM(Company_Website)) > 0 AND SAFE.LENGTH(TRIM(Emails)) > 0)';
        }

        $where = empty($clauses) ? '' : ('WHERE ' . implode(' AND ', $clauses));
        return [$where, $params];
    }

    protected function runQuery(string $sql, array $params = []): array
    {
        $client = $this->getClient();
        if (!$client) {
            return [];
        }
        $query = $client->query($sql)->parameters($params);
        $result = $client->runQuery($query);
        $rows = [];
        foreach ($result as $row) {
            $rows[] = (array) $row;
        }
        return $rows;
    }

    protected function getClient(): ?object
    {
        if ($this->client !== null) {
            return $this->client;
        }
        if (!class_exists('Google\\Cloud\\BigQuery\\BigQueryClient')) {
            return $this->client = null;
        }
        $config = [
            'projectId' => $this->project ?: null,
        ];
        $creds = (string) config('bigquery.credentials');
        if ($creds) {
            $config['keyFilePath'] = $creds;
        }
        $this->client = new \Google\Cloud\BigQuery\BigQueryClient($config);
        return $this->client;
    }
}

