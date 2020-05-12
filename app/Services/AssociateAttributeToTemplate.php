<?php

namespace App\Services;

use App\Models\User;
use App\Models\Template;
use App\Models\Attribute;

class AssociateAttributeToTemplate extends BaseService
{
    private User $author;
    private array $data;
    private Template $template;
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
            'action' => 'attribute_associated_to_template',
            'objects' => json_encode([
                'template_id' => $this->template->id,
                'template_name' => $this->template->name,
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
            'template_id' => 'required|integer|exists:templates,id',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'position' => 'required|integer',
        ];
    }

    /**
     * Associate a template with an attribute.
     *
     * @param array $data
     * @return Template
     */
    public function execute(array $data): Template
    {
        $this->data = $data;
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);

        $this->attribute = Attribute::where('account_id', $data['account_id'])
            ->findOrFail($data['attribute_id']);

        $this->template = Template::where('account_id', $data['account_id'])
            ->findOrFail($data['template_id']);

        $this->template->attributes()->syncWithoutDetaching([
            $this->attribute->id => ['position' => $data['position']],
        ]);

        $this->createAuditLog();

        return $this->template;
    }
}
