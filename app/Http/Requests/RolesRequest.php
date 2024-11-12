<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolesRequest extends FormRequest
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
        if ($this->method() === 'PUT') {
            $params = $this->route()->parameters();
            $id = $params["role"];
            return [
                'slug' => 'required|string|max:255|unique:roles,slug,' . $id . ',id',
                'name' => 'required|string|max:255',
                'permissions' => 'nullable|string',
            ];
        } else {
            return [
                'slug' => 'required|string|max:255|unique:roles,slug,',
                'name' => 'required|string|max:255',
                'permissions' => 'nullable|string',
            ];
        }
    }
}
