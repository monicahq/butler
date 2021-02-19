<?php

namespace App\Services\Account;

use App\Models\User;
use App\Models\Template;
use App\Models\Information;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class AssociateInformationToTemplate extends BaseService implements ServiceInterface
{
    private User $author;
    private array $data;
    private Template $template;
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
            'action' => 'information_associated_to_template',
            'objects' => json_encode([
                'template_id' => $this->template->id,
                'template_name' => $this->template->name,
                'information_id' => $this->information->id,
                'information_name' => $this->information->name,
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
            'information_id' => 'required|integer|exists:information,id',
            'position' => 'required|integer',
        ];
    }

    /**
     * Associate a template with an information.
     *
     * @param array $data
     * @return Template
     */
    public function execute(array $data): Template
    {
        $this->data = $data;
        $this->validate();

        $this->template->informations()->syncWithoutDetaching([
            $this->information->id => ['position' => $data['position']],
        ]);

        $this->createAuditLog();

        return $this->template;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);
        $this->author = $this->validateAuthorBelongsToAccount($this->data);

        $this->information = Information::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['information_id']);

        $this->template = Template::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['template_id']);
    }
}
