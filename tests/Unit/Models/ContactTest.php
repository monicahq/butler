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
        $ross = Contact::factory()->create();

        $this->assertTrue($ross->account()->exists());
    }

    /** @test */
    public function it_gets_the_full_name_of_the_contact()
    {
        $ross = Contact::factory()->create([
            'first_name' => 'ross',
            'middle_name' => null,
            'last_name' => null,
        ]);

        $this->assertEquals(
            'ross',
            $ross->name
        );

        $ross = Contact::factory()->create([
            'first_name' => 'ross',
            'last_name' => 'geller',
            'middle_name' => null,
        ]);

        $this->assertEquals(
            'ross geller',
            $ross->name
        );

        $ross = Contact::factory()->create([
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
