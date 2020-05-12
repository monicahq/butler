<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\CreateAccount;
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

        $this->assertDatabaseHas('templates', [
            'account_id' => $user->account_id,
            'name' => trans('app.default_template_name'),
        ]);

        // genders
        $this->assertDatabaseHas('attributes', [
            'account_id' => $user->account_id,
            'type' => 'dropdown',
            'name' => trans('app.default_gender_attribute_name'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_man'),
        ]);
        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_woman'),
        ]);
        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_other'),
        ]);

        // birthdate
        $this->assertDatabaseHas('attributes', [
            'account_id' => $user->account_id,
            'type' => 'date',
            'name' => trans('app.default_birthdate_attribute'),
        ]);

        $this->assertInstanceOf(
            User::class,
            $user
        );
    }
}
