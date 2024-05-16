<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Encontrar um usuário pelo e-mail.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Atualizar a senha de um usuário.
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        $user->password = Hash::make($newPassword);
        $user->recuperationCode = null;
        return $user->save();
    }

    /**
     * Salva o código de recuperação de senha de um usuário.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function saveCodeRecuperation(User $user, string $code): bool
    {
        $user->recuperationCode = $code;
        return $user->save();
    }

    /**
     * Salva o código de ativação da conta de um usuário.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function saveCodeActivation(User $user, string $code): bool
    {
        $user->activationCode = $code;
        return $user->save();
    }

    /**
     * Salva o código de ativação da conta de um usuário.
     *
     * @param User $user
     * @return bool
     */
    public function activeAccount(User $user): bool
    {
        $user->isActive = true;
        $user->activationCode = null;
        return $user->save();
    }

    /**
     * Salva um novo usuário
     *
     * @param User $user
     * @return bool
     */
    public function saveUser(User $user): bool
    {
        if ($user->role === "USER") {
            $user->isActive = true;
        } else {
            $user->isActive = false;
        }
        return $user->save();
    }
}
