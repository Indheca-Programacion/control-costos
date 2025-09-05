<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\RequisicionOrdenCompraGlobales;

class SaveRequisicionOrdenCompraGlobalesRequest extends Request
{
    static public function rules($id)
    {
        if (self::method() === 'POST') {
            $rules = [
            ];
        } else {
            $rules = [
            ];
        }

        return $rules;
    }

    static public function validated($id = null)
    {
        return self::validating(RequisicionOrdenCompraGlobales::fillable(), self::rules($id));
    }
}