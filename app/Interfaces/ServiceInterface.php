<?php

namespace App\Interfaces;

interface ServiceInterface
{
    /**
     * Define what the log should be for this service.
     */
    public function logs(): array;

    /**
     * Define what the rules for the service should be.
     */
    public function rules(): array;
}
