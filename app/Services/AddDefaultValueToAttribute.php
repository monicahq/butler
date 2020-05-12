<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attribute;
use App\Models\AttributeDefaultValue;

class AddDefaultValueToAttribute extends BaseService
{
    private User $author;
    private array $data;
    private Attribute $attribute;

    /**
     * Get the data to log after calling the service.
     *
     * @return array
     */
    public function logs(): array
    {
        return [
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action' => 'default_value_to_attribute_added',
            'objects' => json_encode([
                'attribute_id' => $this->attribute->id,
                'attribute_name' => $this->attribute->name,
            ]),
        ];
    }

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value' => 'required|string|max:255',
        ];
    }

    /**
     * Add a default value to an attribute.
     *
     * @param array $data
     * @return Attribute
     */
    public function execute(array $data): Attribute
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);

        $this->attribute = Attribute::where('account_id', $data['account_id'])
            ->findOrFail($data['attribute_id']);

        $this->data = $data;

        AttributeDefaultValue::create([
            'attribute_id' => $data['attribute_id'],
            'value' => $data['value'],
        ]);

        $this->createAuditLog();

        return $this->attribute;
    }
}
