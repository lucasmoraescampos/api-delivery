<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param array $attributes
     * @return User
     */
    public function authenticate(array $attributes): ?User;

    /**
     * @param array $attributes
     * @return User
     */
    public function authenticateWithProvider(array $attributes): User;

    /**
     * @param User $user
     * @return string
     */
    public function createAccessToken(User $user): string;

    /**
     * @param array $attributes
     * @return void
     */
    public function invalidAccessToken(array $attributes): void;
}
