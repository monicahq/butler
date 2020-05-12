<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Attribute;
use App\Models\AttributeDefaultValue;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_information()
    {
        $gender = factory(Attribute::class)->create();

        $this->assertTrue($gender->information()->exists());
    }

    /** @test */
    public function it_has_many_default_values()
    {
        $gender = factory(Attribute::class)->create();
        factory(AttributeDefaultValue::class, 2)->create([
            'attribute_id' => $gender->id,
        ]);

        $this->assertTrue($gender->defaultValues()->exists());
    }
}
