<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Models\Attribute;
use App\Jobs\LogAccountAudit;
use Illuminate\Support\Facades\Queue;
use App\Services\AssociateAttributeToTemplate;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateAttributeToTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_associates_an_attribute_to_a_template(): void
    {
        $michael = $this->createUser();
        $attribute = factory(Attribute::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $template = factory(Template::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $attribute, $template);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Male',
        ];

        $this->expectException(ValidationException::class);
        (new AssociateAttributeToTemplate)->execute($request);
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
        $template = factory(Template::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $account, $attribute, $template);
    }

    /** @test */
    public function it_fails_if_attribute_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $attribute = factory(Attribute::class)->create();
        $attribute = factory(Attribute::class)->create([]);
        $template = factory(Template::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $attribute, $template);
    }

    /** @test */
    public function it_fails_if_template_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $attribute = factory(Attribute::class)->create();
        $attribute = factory(Attribute::class)->create([
            'account_id' => $michael->account_id,
        ]);
        $template = factory(Template::class)->create([]);
        $this->executeService($michael, $michael->account, $attribute, $template);
    }

    private function executeService(User $author, Account $account, Attribute $attribute, Template $template): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'attribute_id' => $attribute->id,
            'template_id' => $template->id,
            'position' => 3,
        ];

        $template = (new AssociateAttributeToTemplate)->execute($request);

        $this->assertDatabaseHas('attribute_template', [
            'attribute_id' => $attribute->id,
            'template_id' => $template->id,
            'position' => 3,
        ]);

        $this->assertInstanceOf(
            Template::class,
            $template
        );

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($attribute, $template, $author) {
            return $job->auditLog['action'] === 'attribute_associated_to_template' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'template_id' => $template->id,
                    'template_name' => $template->name,
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                ]);
        });
    }
}
