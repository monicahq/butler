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
        $michael = factory(User::class)->create();

        $request = [
            'account_id' => $michael->account_id,
            'author_id' => $michael->id,
            'author_name' => $michael->name,
            'action' => 'status_created',
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ];

        LogAccountAudit::dispatch($request);

        $this->assertDatabaseHas('audit_logs', [
            'account_id' => $michael->account_id,
            'action' => 'status_created',
            'author_id' => $michael->id,
            'author_name' => $michael->name,
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ]);
    }
}
