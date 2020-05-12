<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Jobs\LogAccountAudit;
use App\Services\CreateTemplate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_template(): void
    {
        $michael = $this->createUser();
        $this->executeService($michael, $michael->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateTemplate)->execute($request);
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
            'name' => 'Business',
        ];

        $template = (new CreateTemplate)->execute($request);

        $this->assertDatabaseHas('templates', [
            'id' => $template->id,
            'account_id' => $account->id,
            'name' => 'Business',
        ]);

        $this->assertInstanceOf(
            Template::class,
            $template
        );

        Queue::assertPushed(LogAccountAudit::class, function ($job) use ($template, $author) {
            return $job->auditLog['action'] === 'template_created' &&
                $job->auditLog['author_id'] === $author->id &&
                $job->auditLog['objects'] === json_encode([
                    'template_id' => $template->id,
                    'template_name' => $template->name,
                ]);
        });
    }
}
