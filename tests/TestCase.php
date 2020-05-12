<?php

namespace Tests;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create an User in an account.
     *
     * @return User
     */
    public function createUser(): User
    {
        return factory(User::class)->create();
    }

    /**
     * Create an account.
     *
     * @return Account
     */
    public function createAccount(): Account
    {
        return factory(Account::class)->create();
    }
}
