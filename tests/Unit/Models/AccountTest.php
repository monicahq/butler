<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Models\Information;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_many_users()
    {
        $account = factory(Account::class)->create();
        factory(User::class, 2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->users()->exists());
    }

    /** @test */
    public function it_has_many_templates()
    {
        $account = factory(Account::class)->create();
        factory(Template::class, 2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->templates()->exists());
    }

    /** @test */
    public function it_has_many_informations()
    {
        $account = factory(Account::class)->create();
        factory(Information::class, 2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->informations()->exists());
    }
}
