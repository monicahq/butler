<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Attribute;
use App\Jobs\LogAccountAudit;
use App\Services\CreateAttribute;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateAttributeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_attribute(): void
    {
        $michael = $this->createUser();
        $this->executeService($michael, $michael->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Assistant to the regional manager',
        ];

        $this->expectException(ValidationException::class);
        (new CreateAttribute)->execute($request);
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
            'name' => 'Assistant to the regional manager',
            'type' => 'text',
        ];

        $attribute = (new CreateAttribute)->execute($request);

        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
            'account_id' => $account->id,
            'name' => 'Assistant to the regional manager',
            'type' => 'text',
            'unit' => null,
            'unit_placement_after' => false,
        ]);

        $this->assertInstanceOf(
            Attribute::class,
            $attribute
        );

        Queue::assertPushed(LogAccountAudit::class);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($attribute, $author) {
            return $job->auditLog['action'] === 'attribute_created' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                ]);
        });
    }
}
