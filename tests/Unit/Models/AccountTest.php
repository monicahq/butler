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
        $account = Account::factory()->create();
        User::factory()->count(2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->users()->exists());
    }

    /** @test */
    public function it_has_many_templates()
    {
        $account = Account::factory()->create();
        Template::factory(2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->templates()->exists());
    }

    /** @test */
    public function it_has_many_informations()
    {
        $account = Account::factory()->create();
        Information::factory(2)->create([
            'account_id' => $account->id,
        ]);

        $this->assertTrue($account->informations()->exists());
    }
}
