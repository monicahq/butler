<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Information;
use App\Jobs\LogAccountAudit;
use App\Services\CreateInformation;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateInformationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_information(): void
    {
        $michael = $this->createUser();
        $this->executeService($michael, $michael->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Gender',
        ];

        $this->expectException(ValidationException::class);
        (new CreateInformation)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $this->executeService($michael, $account);
    }

    private function executeService(User $author, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'name' => 'Gender',
            'allows_multiple_entries' => true,
        ];

        $information = (new CreateInformation)->execute($request);

        $this->assertDatabaseHas('information', [
            'id' => $information->id,
            'account_id' => $account->id,
            'name' => 'Gender',
            'allows_multiple_entries' => true,
        ]);

        $this->assertInstanceOf(
            Information::class,
            $information
        );

        Queue::assertPushed(LogAccountAudit::class);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($information, $author) {
            return $job->auditLog['action'] === 'information_created' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'information_id' => $information->id,
                    'information_name' => $information->name,
                ]);
        });
    }
}
