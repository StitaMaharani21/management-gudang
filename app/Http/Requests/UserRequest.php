<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|unique:users,email,' . $this->route('user'),
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:png,jpg|max:2048',
        ];
    }
}
