<?php

namespace App\Repositories;

use App\Models\FcmToken;
use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @return User
     */
    public function getAuth(): User;

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
     * @return FcmToken
     */
    public function checkInfcmToken(array $attributes): FcmToken;

    /**
     * @param array $attributes
     * @return void
     */
    public function invalidAccessToken(array $attributes): void;
}
