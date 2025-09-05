<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\OrdenCompraGlobales;
use App\Controllers\Autorizacion;

class OrdenCompraGlobalesPolicy
{
    public function view(Usuario $usuario, OrdenCompraGlobales $obj)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompraGlobales");
    }

    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompraGlobales");
    }

    public function update(Usuario $usuario, OrdenCompraGlobales $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompraGlobales");
    }

    public function delete(Usuario $usuario, OrdenCompraGlobales $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompraGlobales");
    }

    public function restore(Usuario $usuario, OrdenCompraGlobales $obj)
    {
        //
    }

    public function forceDelete(Usuario $usuario, OrdenCompraGlobales $obj)
    {
        //
    }
}
