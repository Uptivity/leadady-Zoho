<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DestinationApiClient
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('destination.base_url'), '/');
        $this->apiKey = (string) config('destination.api_key');
    }

    /**
     * Send a batch of records to the destination API.
     * Returns an array with 'success' => int, 'failed' => int, 'errors' => []
     */
    public function sendBatch(array $records): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return ['success' => 0, 'failed' => count($records), 'errors' => ['Destination API not configured']];
        }

        // TODO: Define the final endpoint path with the destination team.
        $endpoint = $this->baseUrl . '/api/leads/import';

        $resp = Http::asJson()
            ->timeout(60)
            ->withToken($this->apiKey)
            ->post($endpoint, ['records' => $records]);

        if ($resp->successful()) {
            $json = $resp->json() ?: [];
            return [
                'success' => (int) ($json['success'] ?? count($records)),
                'failed' => (int) ($json['failed'] ?? 0),
                'errors' => (array) ($json['errors'] ?? []),
            ];
        }

        return ['success' => 0, 'failed' => count($records), 'errors' => [$resp->body()]];
    }
}

