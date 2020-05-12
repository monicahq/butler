<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\LogContactAction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogContactAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The audit log instance.
     *
     * @var array
     */
    public array $auditLog;

    /**
     * Create a new job instance.
     * @param array $auditLog
     */
    public function __construct(array $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        (new LogContactAction)->execute([
            'account_id' => $this->auditLog['account_id'],
            'contact_id' => $this->auditLog['contact_id'],
            'author_id' => $this->auditLog['author_id'],
            'author_name' => $this->auditLog['author_name'],
            'action' => $this->auditLog['action'],
            'objects' => $this->auditLog['objects'],
        ]);
    }
}
