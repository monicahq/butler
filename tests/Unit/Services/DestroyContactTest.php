<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\LogAccountAudit;
use App\Services\DestroyContact;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_contact(): void
    {
        $michael = $this->createUser();
        $contact = $this->createContactWithAccount($michael->account);
        $this->executeService($michael, $michael->account, $contact);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyContact)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $contact = $this->createContactWithAccount($michael->account);
        $this->executeService($michael, $account, $contact);
    }

    /** @test */
    public function it_fails_if_contact_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = Account::factory()->create();
        $contact = $this->createContactWithAccount($account);
        $this->executeService($michael, $michael->account, $contact);
    }

    private function executeService(User $author, Account $account, Contact $contact): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'contact_id' => $contact->id,
        ];

        (new DestroyContact)->execute($request);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($contact, $author) {
            return $job->auditLog['action'] === 'contact_destroyed' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'contact_name' => $contact->name,
                ]);
        });
    }

    private function createContactWithAccount(Account $account): Contact
    {
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
        ]);

        return $contact;
    }
}
