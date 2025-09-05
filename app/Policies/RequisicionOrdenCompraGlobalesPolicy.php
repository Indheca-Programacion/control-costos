<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\RequisicionOrdenCompraGlobales;
use App\Controllers\Autorizacion;

class RequisicionOrdenCompraGlobalesPolicy
{
    public function view(Usuario $usuario, RequisicionOrdenCompraGlobales $obj)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "RequisicionOrdenCompraGlobales");
    }

    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "RequisicionOrdenCompraGlobales");
    }

    public function update(Usuario $usuario, RequisicionOrdenCompraGlobales $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "RequisicionOrdenCompraGlobales");
    }

    public function delete(Usuario $usuario, RequisicionOrdenCompraGlobales $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "RequisicionOrdenCompraGlobales");
    }

    public function restore(Usuario $usuario, RequisicionOrdenCompraGlobales $obj)
    {
        //
    }

    public function forceDelete(Usuario $usuario, RequisicionOrdenCompraGlobales $obj)
    {
        //
    }
}
