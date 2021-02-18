<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Jobs\LogAccountAudit;
use App\Jobs\LogContactAudit;
use App\Services\BaseService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_an_empty_log_array(): void
    {
        $stub = $this->getMockForAbstractClass(BaseService::class);

        $this->assertIsArray(
            $stub->logs()
        );
    }

    /** @test */
    public function it_returns_an_empty_rule_array(): void
    {
        $stub = $this->getMockForAbstractClass(BaseService::class);

        $this->assertIsArray(
            $stub->rules()
        );
    }

    /** @test */
    public function it_validates_rules(): void
    {
        $rules = [
            'street' => 'nullable|string|max:255',
        ];

        $stub = $this->getMockForAbstractClass(BaseService::class);
        $stub->rules([$rules]);

        $this->assertTrue(
            $stub->validateRules([
                'street' => 'la rue du bonheur',
            ])
        );
    }

    /** @test */
    public function it_validates_that_the_user_belongs_to_the_account(): void
    {
        $regis = User::factory()->create();

        $data = [
            'account_id' => $regis->account_id,
            'author_id' => $regis->id,
        ];

        $stub = $this->getMockForAbstractClass(BaseService::class);

        $user = $stub->validateAuthorBelongsToAccount($data);

        $this->assertInstanceOf(
            User::class,
            $user
        );
    }

    /** @test */
    public function it_raises_an_exception_if_the_user_doesnt_belong_to_the_account(): void
    {
        $regis = User::factory()->create();
        $john = User::factory()->create();

        $data = [
            'account_id' => $regis->account_id,
            'author_id' => $john->id,
        ];

        $stub = $this->getMockForAbstractClass(BaseService::class);

        $this->expectException(ModelNotFoundException::class);
        $stub->validateAuthorBelongsToAccount($data);
    }

    /** @test */
    public function it_validates_that_the_contact_belongs_to_the_account(): void
    {
        $regis = Contact::factory()->create();

        $data = [
            'account_id' => $regis->account_id,
            'contact_id' => $regis->id,
        ];

        $stub = $this->getMockForAbstractClass(BaseService::class);

        $account = $stub->validateContactBelongsToAccount($data);

        $this->assertInstanceOf(
            Contact::class,
            $account
        );
    }

    /** @test */
    public function it_raises_an_exception_if_the_contact_doesnt_belong_to_the_account(): void
    {
        $regis = Contact::factory()->create();
        $john = Contact::factory()->create();

        $data = [
            'account_id' => $regis->account_id,
            'contact_id' => $john->id,
        ];

        $stub = $this->getMockForAbstractClass(BaseService::class);

        $this->expectException(ModelNotFoundException::class);
        $stub->validateContactBelongsToAccount($data);
    }

    /** @test */
    public function it_dispatches_an_audit_log_job(): void
    {
        Bus::fake();
        $stub = $this->getMockForAbstractClass(BaseService::class);

        $stub->createAuditLog([]);
        Bus::assertDispatched(LogAccountAudit::class);
    }

    /** @test */
    public function it_dispatches_a_contact_log_job(): void
    {
        Bus::fake();
        $stub = $this->getMockForAbstractClass(BaseService::class);

        $stub->createContactLog([]);
        Bus::assertDispatched(LogContactAudit::class);
    }

    /** @test */
    public function it_returns_the_default_value_or_the_given_value(): void
    {
        $stub = $this->getMockForAbstractClass(BaseService::class);
        $array = [
            'value' => true,
        ];

        $this->assertTrue(
            $stub->valueOrFalse($array, 'value')
        );

        $array = [
            'value' => false,
        ];

        $this->assertFalse(
            $stub->valueOrFalse($array, 'value')
        );
    }

    /** @test */
    public function it_returns_null_or_the_actual_value(): void
    {
        $stub = $this->getMockForAbstractClass(BaseService::class);
        $array = [
            'value' => 'this',
        ];

        $this->assertEquals(
            'this',
            $stub->valueOrNull($array, 'value')
        );

        $array = [
            'otherValue' => '',
        ];

        $this->assertNull(
            $stub->valueOrNull($array, 'otherValue')
        );

        $array = [];

        $this->assertNull(
            $stub->valueOrNull($array, 'value')
        );
    }
}
