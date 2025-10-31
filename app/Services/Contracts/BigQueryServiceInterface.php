<?php

namespace App\Services\Contracts;

interface BigQueryServiceInterface
{
    /**
     * Return counts for current filters and required-field toggles.
     * Example keys: total, with_phone, with_email, with_company_email.
     *
     * @param array $filters  e.g., ['industry' => 'oil', 'company_industry' => 'oil', 'location' => 'texas']
     * @param array $required e.g., ['phone' => true, 'email' => false, 'company_email' => true]
     */
    public function getCounts(array $filters, array $required = []): array;

    /**
     * Return a paginated preview of leads for the given filters.
     * Should return an array with 'data' => [], 'total' => int.
     */
    public function getLeads(array $filters, array $required, int $page, int $perPage = 100): array;

    /**
     * Iterate over all leads for a pull, yielding arrays of rows up to the provided batch size.
     * The implementation should enforce any max rows cap.
     */
    public function iterateForPull(array $filters, array $required, int $batchSize, int $maxRows, callable $onBatch): void;
}

