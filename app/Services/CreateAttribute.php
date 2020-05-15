<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attribute;
use App\Models\Information;
use Illuminate\Validation\Rule;

class CreateAttribute extends BaseService
{
    private User $author;
    private array $data;
    private Attribute $attribute;
    private Information $information;

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
            'action' => 'attribute_created',
            'objects' => json_encode([
                'information_id' => $this->information->id,
                'information_name' => $this->information->name,
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
            'information_id' => 'required|integer|exists:information,id',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'unit_placement_after' => 'nullable|boolean',
            'type' => [
                'required',
                Rule::in([
                    'text',
                    'dropdown',
                    'date',
                    'textarea',
                ]),
            ],
            'has_default_value' => 'nullable|boolean',
        ];
    }

    /**
     * Create an attribute.
     *
     * @param array $data
     * @return Attribute
     */
    public function execute(array $data): Attribute
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);
        $this->information = Information::where('account_id', $data['account_id'])
            ->findOrFail($data['information_id']);

        $this->data = $data;

        $this->attribute = Attribute::create([
            'information_id' => $data['information_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'unit' => $this->valueOrNull($data, 'unit'),
            'unit_placement_after' => $this->valueOrFalse($data, 'unit_placement_after'),
            'has_default_value' => $this->valueOrFalse($data, 'has_default_value'),
        ]);

        $this->createAuditLog();

        return $this->attribute;
    }
}
