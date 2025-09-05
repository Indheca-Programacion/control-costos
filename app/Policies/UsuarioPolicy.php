<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Controllers\Autorizacion;

class UsuarioPolicy
{
    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(Usuario $usuarioAutenticado, Usuario $usuario)
    {
        return Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || Autorizacion::permiso($usuarioAutenticado, "usuarios") || $usuarioAutenticado->id == $usuario->id;
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(Usuario $usuarioAutenticado)
    {
        return Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || Autorizacion::permiso($usuarioAutenticado, "usuarios");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(Usuario $usuarioAutenticado, Usuario $usuario)
    {
        return Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || Autorizacion::permiso($usuarioAutenticado, "usuarios") || $usuarioAutenticado->id == $usuario->id;
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(Usuario $usuarioAutenticado, Usuario $usuario)
    {
        // if ( $role->id === 1 )
        // {
        //     $this->deny('No se puede eliminar este perfil');
        // }

        return Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || Autorizacion::permiso($usuarioAutenticado, "usuarios");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Usuario $usuarioAutenticado, Usuario $usuario)
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
    public function forceDelete(Usuario $usuarioAutenticado, Usuario $usuario)
    {
        //
    }
}
