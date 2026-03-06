<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vistoria;

class VistoriaPolicy
{
    /**
     * Se o usuario tem a permission via Spatie, autoriza direto.
     * Isso permite que admins com 'editar qualquer vistoria' passem.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($ability === 'update' && $user->can('editar qualquer vistoria')) {
            return true;
        }

        if ($ability === 'delete' && $user->can('excluir vistorias')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vistoria $vistoria): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vistoria $vistoria): bool
    {
        return $vistoria->user_id === $user->id;
    }

    public function delete(User $user, Vistoria $vistoria): bool
    {
        return $vistoria->user_id === $user->id;
    }

    public function restore(User $user, Vistoria $vistoria): bool
    {
        return $user->hasPermissionTo('excluir vistorias');
    }

    public function forceDelete(User $user, Vistoria $vistoria): bool
    {
        return false;
    }
}
