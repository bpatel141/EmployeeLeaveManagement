<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User;

    /**
     * Register a new user with default employee role.
     *
     * @param array $data
     * @return User
     */
    public function registerUser(array $data): User;

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User;

    /**
     * Delete a user (soft delete).
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool;

    /**
     * Force delete a user (permanent deletion).
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteUser(User $user): bool;

    /**
     * Restore a soft deleted user.
     *
     * @param User $user
     * @return bool
     */
    public function restoreUser(User $user): bool;

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUser(int $id): ?User;

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User;

    /**
     * Attach role to user.
     *
     * @param User $user
     * @param string $roleName
     * @return void
     */
    public function attachRole(User $user, string $roleName): void;
}

