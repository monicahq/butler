<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ContactLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactLogTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_contact(): void
    {
        $log = factory(ContactLog::class)->create([]);
        $this->assertTrue($log->contact()->exists());
    }

    /** @test */
    public function it_belongs_to_a_user(): void
    {
        $log = factory(ContactLog::class)->create([]);
        $this->assertTrue($log->author()->exists());
    }

    /** @test */
    public function it_returns_the_object_attribute(): void
    {
        $log = factory(ContactLog::class)->create([]);
        $this->assertEquals(
            1,
            $log->object->{'user'}
        );
    }
}
