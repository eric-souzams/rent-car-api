<?php

namespace App\Http\Requests\Marca;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarcaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|string|min:3|unique:marcas',
            'image' => 'required|file|mimes:jpg,png,jpeg'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatorio',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O campo nome precisa ter no mínimo 3 caracteres',
            'image.mimes' => 'O arquivo de imagem precisa ser do formato PNG ou JPG'
        ];
    }
}
