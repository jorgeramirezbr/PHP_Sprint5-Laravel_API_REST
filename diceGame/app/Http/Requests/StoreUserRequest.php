<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'nickname' => $this->input('nickname') ?: 'Anonymous',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'nickname' => 'string|max:255',
            'password' => ''
        ];
    
        // unique solo si el nickname no es 'Anonimous'
        if ($this->input('nickname') !== 'Anonymous') {
            $rules['nickname'] .= '|unique:users';
        }
    
        return $rules;
    }
}
