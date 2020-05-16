<?php

namespace App\Services;

use App\Models\User;
use App\Models\Template;

class DestroyTemplate extends BaseService
{
    private User $author;
    private array $data;
    private Template $template;

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
            'action' => 'template_destroyed',
            'objects' => json_encode([
                'template_name' => $this->template->name,
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
        ];
    }

    /**
     * Destroy a template.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $this->data = $data;
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);

        $this->template = Template::where('account_id', $data['account_id'])
            ->findOrFail($data['template_id']);

        $this->template->delete();

        $this->createAuditLog();
    }
}
