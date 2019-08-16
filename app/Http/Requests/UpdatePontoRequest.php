<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ponto;

class UpdatePontoRequest extends FormRequest
{


    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return Ponto::$rules;
    }
}
