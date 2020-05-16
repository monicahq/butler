<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Jobs\LogAccountAudit;
use App\Services\DestroyTemplate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_template(): void
    {
        $michael = $this->createUser();
        $template = $this->createTemplate($michael->account);
        $this->executeService($michael, $michael->account, $template);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyTemplate)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = $this->createAccount();
        $template = $this->createTemplate($michael->account);
        $this->executeService($michael, $account, $template);
    }

    /** @test */
    public function it_fails_if_template_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $michael = $this->createUser();
        $account = factory(Account::class)->create();
        $template = $this->createTemplate($account);
        $this->executeService($michael, $michael->account, $template);
    }

    private function executeService(User $author, Account $account, Template $template): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'template_id' => $template->id,
        ];

        (new DestroyTemplate)->execute($request);

        $this->assertDatabaseMissing('templates', [
            'id' => $template->id,
        ]);

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($template, $author) {
            return $job->auditLog['action'] === 'template_destroyed' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'template_name' => $template->name,
                ]);
        });
    }

    private function createTemplate(Account $account): Template
    {
        $template = factory(Template::class)->create([
            'account_id' => $account->id,
        ]);

        return $template;
    }
}
