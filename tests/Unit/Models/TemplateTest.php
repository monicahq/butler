<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Template;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $template = factory(Template::class)->create();

        $this->assertTrue($template->account()->exists());
    }

    /** @test */
    public function it_has_many_attributes()
    {
        $template = factory(Template::class)->create();

        $attribute = factory(Attribute::class)->create([
            'account_id' => $template->account_id,
        ]);
        $template->attributes()->attach(
            $attribute->id,
            [
                'position' => 0,
            ]
        );

        $this->assertTrue($template->attributes()->exists());
    }
}
