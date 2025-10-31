<?php

namespace App\Jobs;

use App\Models\PullJob;
use App\Services\Contracts\BigQueryServiceInterface;
use App\Services\DestinationApiClient;
use App\Services\LeadTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PullJob $pullJob) {}

    public function handle(BigQueryServiceInterface $bq, DestinationApiClient $dest, LeadTransformer $transformer): void
    {
        $this->pullJob->update(['status' => 'running']);

        $batchSize = (int) config('destination.batch_size', 200);
        $maxRows = (int) config('destination.pull_max_rows', 50000);
        $filters = $this->pullJob->filters ?? [];
        $required = $this->pullJob->required ?? [];

        $processed = 0;
        $failed = 0;
        $total = 0;

        // Iterate BigQuery results and send to destination in batches
        $bq->iterateForPull($filters, $required, $batchSize, $maxRows, function (array $rows) use (&$processed, &$failed, &$total, $dest, $transformer) {
            $total += count($rows);
            // Apply mapping from BigQuery columns to destination fields.
            $payload = $transformer->transformMany($rows);
            $res = $dest->sendBatch($payload);
            $processed += (int) ($res['success'] ?? 0);
            $failed += (int) ($res['failed'] ?? 0);
            $this->pullJob->update([
                'processed' => $processed,
                'failed' => $failed,
                'total' => $total,
            ]);
        });

        $this->pullJob->update(['status' => 'completed']);
    }

    public function failed(\Throwable $e): void
    {
        $this->pullJob->update(['status' => 'failed', 'error' => $e->getMessage()]);
    }
}
