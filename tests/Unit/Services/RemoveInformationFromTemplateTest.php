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
use App\Services\RemoveInformationFromTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RemoveInformationFromTemplateTest extends TestCase
{
    use DatabaseTransactions;

    private Template $template;
    private Information $information;

    /** @test */
    public function it_removes_an_information_from_a_template(): void
    {
        $michael = $this->createUser();
        $this->associateTemplateAndInformation($michael->account, $michael->account);
        $this->executeService($michael, $michael->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Male',
        ];

        $this->expectException(ValidationException::class);
        (new RemoveInformationFromTemplate)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $this->associateTemplateAndInformation($account, $account);
        $this->executeService($michael, $account);
    }

    /** @test */
    public function it_fails_if_information_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $this->associateTemplateAndInformation($michael->account, $account);
        $this->executeService($michael, $michael->account);
    }

    /** @test */
    public function it_fails_if_template_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $this->associateTemplateAndInformation($account, $michael->account);
        $this->executeService($michael, $michael->account);
    }

    private function executeService(User $author, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'information_id' => $this->information->id,
            'template_id' => $this->template->id,
        ];

        (new RemoveInformationFromTemplate)->execute($request);

        $this->assertDatabaseMissing('information_template', [
            'information_id' => $this->information->id,
            'template_id' => $this->template->id,
        ]);

        $information = $this->information;
        $template = $this->template;

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($information, $template, $author) {
            return $job->auditLog['action'] === 'information_removed_from_template' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'template_id' => $template->id,
                    'template_name' => $template->name,
                    'information_id' => $information->id,
                    'information_name' => $information->name,
                ]);
        });
    }

    private function associateTemplateAndInformation(Account $accountA, Account $accountB): void
    {
        $this->template = factory(Template::class)->create([
            'account_id' => $accountA->id,
        ]);
        $this->information = factory(Information::class)->create([
            'account_id' => $accountB->id,
        ]);

        $this->template->informations()->syncWithoutDetaching([
            $this->information->id => ['position' => 1],
        ]);
    }
}
