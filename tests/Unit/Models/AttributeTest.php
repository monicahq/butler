<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Template;
use App\Models\Attribute;
use App\Models\AttributeDefaultValue;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $gender = factory(Attribute::class)->create();

        $this->assertTrue($gender->account()->exists());
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

    /** @test */
    public function it_has_many_templates()
    {
        $template = factory(Template::class)->create();

        $attribute = factory(Attribute::class)->create([
            'account_id' => $template->account_id,
        ]);
        $attribute->templates()->attach(
            $template->id,
            [
                'position' => 0,
            ]
        );

        $this->assertTrue($attribute->templates()->exists());
    }
}
