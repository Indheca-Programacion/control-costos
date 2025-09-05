<?php

namespace App\Policies;

// require_once "app/Controllers/Autorizacion.php";

use App\Models\Usuario;
use App\Models\Perfil;
// use Illuminate\Auth\Access\HandlesAuthorization;
use App\Controllers\Autorizacion;

// class PerfilPolicy extends Autorizacion
class PerfilPolicy
{
    // use HandlesAuthorization;

    // public function before($user)
    // {
    //     if ( $user->hasRole('Administrador') )
    //     {
    //         return true;
    //     }
    // }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(Usuario $usuario, Perfil $perfil)
    {
        // return $user->hasRole('Administrador') || $user->hasPermissionTo('Ver perfiles');
        // return Autorizacion::perfil($usuario, "Administrador") || Autorizacion::permiso($usuario, "perfiles");
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "perfiles");
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "perfiles");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(Usuario $usuario, Perfil $perfil)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "perfiles");
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(Usuario $usuario, Perfil $perfil)
    {
        // if ( $role->id === 1 )
        // {
        //     $this->deny('No se puede eliminar este perfil');
        // }

        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "perfiles");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Usuario $usuario, Perfil $perfil)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function forceDelete(Usuario $usuario, Perfil $perfil)
    {
        //
    }
}
