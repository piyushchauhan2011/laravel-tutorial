<?php

namespace App\Jobs;

use App\Models\ExportReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;
use Throwable;

class GenerateExportReportJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [5, 30, 120];

    public int $timeout = 60;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public int $exportReportId,
        public array $payload
    ) {}

    public function handle(): void
    {
        $report = ExportReport::query()->findOrFail($this->exportReportId);

        $report->update([
            'status' => 'processing',
            'attempt_count' => $report->attempt_count + 1,
            'error_message' => null,
        ]);

        $shouldFail = (bool) ($this->payload['should_fail'] ?? false);
        if ($shouldFail) {
            throw new RuntimeException('Simulated export failure for retry demonstration.');
        }

        $summary = sprintf(
            'Report "%s" processed at %s using queue payload.',
            $report->topic,
            now()->toDateTimeString()
        );

        $report->update([
            'status' => 'completed',
            'summary' => $summary,
            'processed_at' => now(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        ExportReport::query()
            ->where('id', $this->exportReportId)
            ->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'processed_at' => null,
            ]);
    }
}
