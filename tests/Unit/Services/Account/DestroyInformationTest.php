<?php

namespace Tests\Unit\Services\Account;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Information;
use App\Jobs\LogAccountAudit;
use Illuminate\Support\Facades\Queue;
use App\Services\Account\DestroyInformation;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyInformationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_an_information(): void
    {
        $ross = $this->createUser();
        $information = Information::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $ross->account, $information);
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

        $ross = $this->createUser();
        $account = $this->createAccount();
        $information = Information::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $account, $information);
    }

    /** @test */
    public function it_fails_if_information_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createUser();
        $information = Information::factory()->create();
        $this->executeService($ross, $ross->account, $information);
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
