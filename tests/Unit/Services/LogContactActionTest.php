<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ContactLog;
use App\Services\LogContactAction;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LogContactActionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_logs_an_action(): void
    {
        $account = Account::factory()->create([]);
        $ross = User::factory()->create([
            'account_id' => $account->id,
        ]);
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
        ]);

        $this->executeService($ross, $account, $contact);
    }

    /** @test */
    public function it_fails_if_the_author_is_not_in_the_account(): void
    {
        $account = Account::factory()->create([]);
        $ross = User::factory()->create([]);
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->executeService($ross, $account, $contact);
    }

    /** @test */
    public function it_fails_if_the_contact_is_not_in_the_account(): void
    {
        $account = Account::factory()->create([]);
        $ross = User::factory()->create([
            'account_id' => $account->id,
        ]);
        $contact = Contact::factory()->create([]);

        $this->expectException(ModelNotFoundException::class);
        $this->executeService($ross, $account, $contact);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'action' => 'account_created',
        ];

        $this->expectException(ValidationException::class);
        (new LogContactAction)->execute($request);
    }

    private function executeService(User $ross, Account $account, Contact $contact): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $ross->id,
            'contact_id' => $contact->id,
            'author_name' => $ross->name,
            'action' => 'account_created',
            'objects' => '{"user": 1}',
        ];

        $auditLog = (new LogContactAction)->execute($request);

        $this->assertDatabaseHas('contact_logs', [
            'id' => $auditLog->id,
            'contact_id' => $contact->id,
            'author_id' => $ross->id,
            'author_name' => $ross->name,
            'action' => 'account_created',
            'objects' => '{"user": 1}',
        ]);

        $this->assertInstanceOf(
            ContactLog::class,
            $auditLog
        );
    }
}
