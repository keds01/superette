<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Determine if the user has the given abilities.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array|string  $abilities
     * @param  array  $arguments
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        // Appeler la méthode parent pour la validation standard
        return parent::validateCredentials($user, $credentials);
    }
} 