<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuditLogTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_an_account(): void
    {
        $log = factory(AuditLog::class)->create([]);
        $this->assertTrue($log->account()->exists());
    }

    /** @test */
    public function it_belongs_to_a_user(): void
    {
        $log = factory(AuditLog::class)->create([]);
        $this->assertTrue($log->author()->exists());
    }

    /** @test */
    public function it_returns_the_object_attribute(): void
    {
        $log = factory(AuditLog::class)->create([]);
        $this->assertEquals(
            1,
            $log->object->{'user'}
        );
    }
}
