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
     * Get the information associated with the account.
     *
     * Note to future reader of this code: i KNOW that information doesn't take
     * an "S" at the end, but I needed to indicate that there were many pieces
     * of information in a simple word. Deal with it.
     *
     * @return HasMany
     */
    public function informations()
    {
        return $this->hasMany(Information::class);
    }
}
