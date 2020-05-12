<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Attribute;
use App\Jobs\LogAccountAudit;
use Illuminate\Support\Facades\Queue;
use App\Services\AddDefaultValueToAttribute;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddDefaultValueToAttributeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_default_value_to_attribute(): void
    {
        $michael = $this->createUser();
        $attribute = factory(Attribute::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $attribute);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Male',
        ];

        $this->expectException(ValidationException::class);
        (new AddDefaultValueToAttribute)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $attribute = factory(Attribute::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $account, $attribute);
    }

    /** @test */
    public function it_fails_if_attribute_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $attribute = factory(Attribute::class)->create();
        $this->executeService($michael, $michael->account, $attribute);
    }

    private function executeService(User $author, Account $account, Attribute $attribute): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'attribute_id' => $attribute->id,
            'value' => 'Male',
            'type' => 'text',
        ];

        $attribute = (new AddDefaultValueToAttribute)->execute($request);

        $this->assertDatabaseHas('attribute_default_values', [
            'attribute_id' => $attribute->id,
            'value' => 'Male',
        ]);

        $this->assertInstanceOf(
            Attribute::class,
            $attribute
        );

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($attribute, $author) {
            return $job->auditLog['action'] === 'default_value_to_attribute_added' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                ]);
        });
    }
}
