<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class hahaha_job_queue_probe implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $probe_id_) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Intentionally empty: this job is used to verify queue workers can pull and complete jobs.
    }
}
