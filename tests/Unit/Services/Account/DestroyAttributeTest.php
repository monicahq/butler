<?php

namespace Tests\Unit\Services\Account;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Attribute;
use App\Models\Information;
use App\Jobs\LogAccountAudit;
use Illuminate\Support\Facades\Queue;
use App\Services\Account\DestroyAttribute;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyAttributeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_an_attribute(): void
    {
        $ross = $this->createUser();
        $attribute = $this->createAttributeLinkedToAccount($ross->account);
        $this->executeService($ross, $ross->account, $attribute);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyAttribute)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createUser();
        $account = $this->createAccount();
        $attribute = $this->createAttributeLinkedToAccount($ross->account);
        $this->executeService($ross, $account, $attribute);
    }

    /** @test */
    public function it_fails_if_attribute_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createUser();
        $account = Account::factory()->create();
        $attribute = $this->createAttributeLinkedToAccount($account);
        $this->executeService($ross, $ross->account, $attribute);
    }

    private function executeService(User $author, Account $account, Attribute $attribute): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'attribute_id' => $attribute->id,
        ];

        (new DestroyAttribute)->execute($request);

        $this->assertDatabaseMissing('attributes', [
            'id' => $attribute->id,
        ]);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($attribute, $author) {
            return $job->auditLog['action'] === 'attribute_destroyed' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'attribute_name' => $attribute->name,
                ]);
        });
    }

    private function createAttributeLinkedToAccount(Account $account): Attribute
    {
        $information = Information::factory()->create([
            'account_id' => $account->id,
        ]);
        $attribute = Attribute::factory()->create([
            'information_id' => $information->id,
        ]);

        return $attribute;
    }
}
