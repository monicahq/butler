<?php

namespace App\Services\Account;

use App\Models\User;
use App\Models\Attribute;
use App\Models\Information;
use App\Services\BaseService;
use Illuminate\Validation\Rule;
use App\Interfaces\ServiceInterface;

class CreateAttribute extends BaseService implements ServiceInterface
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
                    'contact',
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
        $this->data = $data;

        $this->validate();

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

    public function validate(): void
    {
        $this->validateRules($this->data);
        $this->author = $this->validateAuthorBelongsToAccount($this->data);
        $this->information = Information::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['information_id']);
    }
}
