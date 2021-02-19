<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\LogAccountAudit;
use App\Services\CreateContact;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_contact(): void
    {
        $ross = $this->createUser();
        $this->executeService($ross, $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateContact)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createUser();
        $account = $this->createAccount();
        $this->executeService($ross, $account);
    }

    private function executeService(User $author, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'first_name' => 'Ross',
        ];

        $contact = (new CreateContact)->execute($request);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'account_id' => $account->id,
            'first_name' => 'Ross',
        ]);

        $this->assertInstanceOf(
            Contact::class,
            $contact
        );

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($contact, $author) {
            return $job->auditLog['action'] === 'contact_created' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'contact_id' => $contact->id,
                    'contact_name' => $contact->name,
                ]);
        });
    }
}
