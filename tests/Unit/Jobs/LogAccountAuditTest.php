<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\LogAccountAudit;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogAccountAuditTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_logs_an_account_audit(): void
    {
        $ross = User::factory()->create();

        $request = [
            'account_id' => $ross->account_id,
            'author_id' => $ross->id,
            'author_name' => $ross->name,
            'action' => 'status_created',
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ];

        LogAccountAudit::dispatch($request);

        $this->assertDatabaseHas('audit_logs', [
            'account_id' => $ross->account_id,
            'action' => 'status_created',
            'author_id' => $ross->id,
            'author_name' => $ross->name,
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ]);
    }
}
