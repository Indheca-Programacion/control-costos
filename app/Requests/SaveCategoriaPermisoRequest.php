<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\CategoriaPermiso;

class SaveCategoriaPermisoRequest extends Request
{
	static public function rules($id)
    {
        $rules = [

        ];
      
        return $rules;
    }

    static public function messages()
    {
        return [
        ];
    }

    static public function validated($id = null) {
        return self::validating(CategoriaPermiso::fillable(), self::rules($id), self::messages());
    }
}