<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Models\Information;
use App\Jobs\LogAccountAudit;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Services\AssociateInformationToTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateInformationToTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_associates_an_information_to_a_template(): void
    {
        $michael = $this->createUser();
        $information = Information::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $template = Template::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $information, $template);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Male',
        ];

        $this->expectException(ValidationException::class);
        (new AssociateInformationToTemplate)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $information = Information::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $template = Template::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $account, $information, $template);
    }

    /** @test */
    public function it_fails_if_information_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $information = Information::factory()->create();
        $template = Template::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $this->executeService($michael, $michael->account, $information, $template);
    }

    /** @test */
    public function it_fails_if_template_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $information = Information::factory()->create([
            'account_id' => $michael->account_id,
        ]);
        $template = Template::factory()->create([]);
        $this->executeService($michael, $michael->account, $information, $template);
    }

    private function executeService(User $author, Account $account, Information $information, Template $template): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'information_id' => $information->id,
            'template_id' => $template->id,
            'position' => 3,
        ];

        $template = (new AssociateInformationToTemplate)->execute($request);

        $this->assertDatabaseHas('information_template', [
            'information_id' => $information->id,
            'template_id' => $template->id,
            'position' => 3,
        ]);

        $this->assertInstanceOf(
            Template::class,
            $template
        );

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($information, $template, $author) {
            return $job->auditLog['action'] === 'information_associated_to_template' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'template_id' => $template->id,
                    'template_name' => $template->name,
                    'information_id' => $information->id,
                    'information_name' => $information->name,
                ]);
        });
    }
}
