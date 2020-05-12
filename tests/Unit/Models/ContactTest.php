<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $ross = factory(Contact::class)->create();

        $this->assertTrue($ross->account()->exists());
    }

    /** @test */
    public function it_gets_the_full_name_of_the_contact()
    {
        $ross = factory(Contact::class)->create([
            'first_name' => 'ross',
        ]);

        $this->assertEquals(
            'ross',
            $ross->name
        );

        $ross = factory(Contact::class)->create([
            'first_name' => 'ross',
            'last_name' => 'geller',
        ]);

        $this->assertEquals(
            'ross geller',
            $ross->name
        );

        $ross = factory(Contact::class)->create([
            'first_name' => 'ross',
            'last_name' => 'geller',
            'middle_name' => 'junior',
        ]);

        $this->assertEquals(
            'ross junior geller',
            $ross->name
        );
    }
}
