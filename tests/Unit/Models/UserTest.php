<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $regis = User::factory()->create();

        $this->assertTrue($regis->account()->exists());
    }

    /** @test */
    public function it_returns_the_name_attribute(): void
    {
        $dwight = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
        ]);

        $this->assertEquals(
            'Dwight Schrute',
            $dwight->name
        );
    }
}
