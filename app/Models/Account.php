<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /**
     * Get the users associated with the account.
     *
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the templates associated with the account.
     *
     * @return HasMany
     */
    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    /**
     * Get the attributes associated with the account.
     *
     * @return HasMany
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
