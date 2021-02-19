<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Jobs\LogContactAudit;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogContactAuditTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_logs_a_contact_audit(): void
    {
        $ross = Contact::factory()->create();
        $regis = User::factory()->create([
            'account_id' => $ross->account_id,
        ]);

        $request = [
            'account_id' => $ross->account_id,
            'contact_id' => $ross->id,
            'author_id' => $regis->id,
            'author_name' => $regis->name,
            'action' => 'status_created',
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ];

        LogContactAudit::dispatch($request);

        $this->assertDatabaseHas('contact_logs', [
            'contact_id' => $ross->id,
            'action' => 'status_created',
            'author_id' => $regis->id,
            'author_name' => $regis->name,
            'objects' => json_encode([
                'company_name' => 'John',
            ]),
        ]);
    }
}
