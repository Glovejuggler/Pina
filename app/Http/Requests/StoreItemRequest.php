<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::id() === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supplier' => 'nullable',
            'brand' => 'required',
            'description' => 'nullable',
            'cost' => 'numeric|required',
            'price' => 'numeric|required',
            'code' => 'required|unique:items,code'.$this->item?->code,
        ];
    }

    /**
     * Validation messages
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'code.unique' => 'An item with this code already exists'
        ];
    }
}
