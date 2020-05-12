<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\SetupAccount;
use App\Services\CreateAccount;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateAccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_account(): void
    {
        $this->executeService();
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateAccount)->execute($request);
    }

    private function executeService(): void
    {
        Queue::fake();

        $request = [
            'first_name' => 'john',
            'last_name' => 'john',
            'email' => 'john@email.com',
            'password' => 'john',
        ];

        $user = (new CreateAccount)->execute($request);

        $this->assertDatabaseHas('accounts', [
            'id' => $user->account->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'account_id' => $user->account_id,
            'first_name' => 'john',
            'last_name' => 'john',
            'email' => 'john@email.com',
        ]);

        $this->assertInstanceOf(
            User::class,
            $user
        );

        Queue::assertPushed(SetupAccount::class, function ($job) use ($user) {
            return $job->user === $user;
        });

        // $this->assertDatabaseHas('templates', [
        //     'account_id' => $user->account_id,
        //     'name' => trans('app.default_template_name'),
        // ]);

        // // genders
        // $this->assertDatabaseHas('attributes', [
        //     'account_id' => $user->account_id,
        //     'type' => 'dropdown',
        //     'name' => trans('app.default_gender_information_name'),
        // ]);

        // $this->assertDatabaseHas('attribute_default_values', [
        //     'value' => trans('app.default_gender_man'),
        // ]);
        // $this->assertDatabaseHas('attribute_default_values', [
        //     'value' => trans('app.default_gender_woman'),
        // ]);
        // $this->assertDatabaseHas('attribute_default_values', [
        //     'value' => trans('app.default_gender_other'),
        // ]);

        // // birthdate
        // $this->assertDatabaseHas('attributes', [
        //     'account_id' => $user->account_id,
        //     'type' => 'date',
        //     'name' => trans('app.default_birthdate_attribute'),
        // ]);
    }
}
