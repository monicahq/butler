<?php

namespace App\Services\Account;

use App\Models\User;
use App\Models\Template;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class CreateTemplate extends BaseService implements ServiceInterface
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
            'action' => 'template_created',
            'objects' => json_encode([
                'template_id' => $this->template->id,
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
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Create a template.
     *
     * @param array $data
     * @return Template
     */
    public function execute(array $data): Template
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);
        $this->data = $data;

        $this->template = Template::create([
            'account_id' => $data['account_id'],
            'name' => $data['name'],
        ]);

        $this->createAuditLog();

        return $this->template;
    }
}
