<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    // Descomentar si se desea actualizar el nickname sin enviar un nuevo nickname, se le asignara por defecto 'Anonymous'
    /* public function prepareForValidation()
    {
        $this->merge([
            'nickname' => $this->input('nickname') ?: 'Anonymous',
        ]);
    } */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');  //id del user de la ruta
        $rules = [
            'nickname' => 'required|string|max:255',
        ];
    
        // unique solo si el nickname no es 'Anonimous'
        if ($this->input('nickname') !== 'Anonymous') {
            $rules['nickname'] .= '|unique:users,nickname,'.$userId;
        }

        return $rules;
    }
}
