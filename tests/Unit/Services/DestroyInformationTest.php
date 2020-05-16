<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Information;
use App\Jobs\LogAccountAudit;
use App\Services\DestroyInformation;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyInformationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_an_information(): void
    {
        $michael = $this->createUser();
        $information = factory(Information::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $information);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyInformation)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $information = factory(Information::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $account, $information);
    }

    /** @test */
    public function it_fails_if_information_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $information = factory(Information::class)->create();
        $this->executeService($michael, $michael->account, $information);
    }

    private function executeService(User $author, Account $account, Information $information): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'information_id' => $information->id,
        ];

        (new DestroyInformation)->execute($request);

        $this->assertDatabaseMissing('information', [
            'id' => $information->id,
        ]);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($information, $author) {
            return $job->auditLog['action'] === 'information_destroyed' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'information_name' => $information->name,
                ]);
        });
    }
}
