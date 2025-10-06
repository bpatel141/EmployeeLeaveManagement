<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        // Hash password if provided
        if (isset($data['password']) && !Hash::needsRehash($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    /**
     * Register a new user with default employee role.
     *
     * @param array $data
     * @return User
     */
    public function registerUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Hash the password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Create the user
            $user = User::create($data);

            // Attach employee role
            $this->attachRole($user, 'employee');

            return $user->load('roles');
        });
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            
            return $user->fresh();
        });
    }

    /**
     * Delete a user (soft delete).
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Detach all roles from the user (removes entries from role_user table)
            $user->roles()->detach();
            
            // Soft delete all related leave allocations
            $user->leaveAllocations()->delete();
            
            // Soft delete all related leave requests
            $user->leaveRequests()->delete();
            
            // Soft delete the user
            return $user->delete();
        });
    }

    /**
     * Force delete a user (permanent deletion).
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Detach all roles from the user (removes entries from role_user table)
            $user->roles()->detach();
            
            // Force delete all related leave allocations
            $user->leaveAllocations()->forceDelete();
            
            // Force delete all related leave requests
            $user->leaveRequests()->forceDelete();
            
            // Force delete the user
            return $user->forceDelete();
        });
    }

    /**
     * Restore a soft deleted user.
     *
     * @param User $user
     * @return bool
     */
    public function restoreUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Restore the user
            $user->restore();
            
            // Restore related leave allocations
            $user->leaveAllocations()->restore();
            
            // Restore related leave requests
            $user->leaveRequests()->restore();
            
            return true;
        });
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUser(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Attach role to user.
     *
     * @param User $user
     * @param string $roleName
     * @return void
     */
    public function attachRole(User $user, string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        if (!$user->roles()->where('name', $roleName)->exists()) {
            $user->roles()->attach($role->id);
        }
    }
}

