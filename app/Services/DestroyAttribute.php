<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attribute;
use App\Models\Information;

class DestroyAttribute extends BaseService
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
            'action' => 'attribute_destroyed',
            'objects' => json_encode([
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
        ];
    }

    /**
     * Destroy an attribute.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);

        $this->attribute = Attribute::findOrFail($data['attribute_id']);
        Information::where('account_id', $data['account_id'])
            ->findOrFail($this->attribute->information_id);

        $this->data = $data;

        $this->attribute->delete();

        $this->createAuditLog();
    }
}
