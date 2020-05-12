<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\AuditLog;
use App\Services\LogAccountAction;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogAccountActionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_logs_an_action(): void
    {
        $account = factory(Account::class)->create([]);
        $michael = factory(User::class)->create([
            'account_id' => $account->id,
        ]);

        $this->executeService($michael, $account);
    }

    /** @test */
    public function it_fails_if_the_author_is_not_in_the_account(): void
    {
        $account = factory(Account::class)->create([]);
        $michael = factory(User::class)->create([]);

        $this->expectException(ModelNotFoundException::class);
        $this->executeService($michael, $account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'action' => 'account_created',
        ];

        $this->expectException(ValidationException::class);
        (new LogAccountAction)->execute($request);
    }

    private function executeService(User $michael, Account $account): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $michael->id,
            'author_name' => $michael->name,
            'action' => 'account_created',
            'objects' => '{"user": 1}',
        ];

        $auditLog = (new LogAccountAction)->execute($request);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $auditLog->id,
            'account_id' => $account->id,
            'author_id' => $michael->id,
            'author_name' => $michael->name,
            'action' => 'account_created',
            'objects' => '{"user": 1}',
        ]);

        $this->assertInstanceOf(
            AuditLog::class,
            $auditLog
        );
    }
}
