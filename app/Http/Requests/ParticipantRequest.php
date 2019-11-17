<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantRequest extends FormRequest
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
            'last_name' => 'required|max:190',
            'first_name' => 'required|max:190',
            'mi' => 'required|max:3',
            'sex' => [
                'required',
                Rule::in(['M', 'F'])
            ],
            'mobile' => 'required|max:190'
        ];
    }
}
