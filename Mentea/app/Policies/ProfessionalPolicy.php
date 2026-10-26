<?php

namespace App\Policies;

use App\Models\Professional;
use App\Models\User;

class ProfessionalPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // listado público de profesionales disponibles
    }

    public function view(User $user, Professional $professional): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Professional $professional): bool
    {
        return $user->isAdmin() || $professional->user_id === $user->id;
    }

    public function delete(User $user, Professional $professional): bool
    {
        return $user->isAdmin();
    }
}
