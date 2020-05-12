<?php

namespace App\ViewHelpers\Settings;

use App\Models\Account;
use Illuminate\Support\Collection;

class SettingsControllerViewHelper
{
    /**
     * Collection containing all the templates for this account.
     *
     * @return Collection
     */
    public static function templates(Account $account): Collection
    {
        $templates = $account->templates()->with('attributes')->get();

        $templateCollection = collect([]);
        foreach ($templates as $template) {
            $templateCollection->push([
                'id' => $template->id,
                'name' => $template->name,
                'number_of_attributes' => $template->attributes->count(),
            ]);
        }

        return $templateCollection;
    }

    /**
     * Collection containing all the attributes for this account.
     *
     * @return Collection
     */
    public static function attributes(Account $account): Collection
    {
        $attributes = $account->attributes()->with('defaultValues')->get();

        $attributeCollection = collect([]);
        foreach ($attributes as $attribute) {
            $defaultValueCollection = collect([]);
            foreach ($attribute->defaultValues as $defaultValue) {
                $defaultValueCollection->push([
                    'id' => $defaultValue->id,
                    'value' => $defaultValue->value,
                ]);
            }

            $attributeCollection->push([
                'id' => $attribute->id,
                'name' => $attribute->name,
                'type' => trans('app.attribute_type_'.$attribute->type),
                'default_values' => $attribute->has_default_value ? $defaultValueCollection : null,
            ]);
        }

        return $attributeCollection;
    }
}
