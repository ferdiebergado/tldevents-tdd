<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
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
            'title' => 'required|min:2|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => [
                'required',
                Rule::in(['W', 'T', 'C'])
            ],
            'grouping' => [
                'required',
                Rule::in(['R', 'L', 'M', 'N'])
            ],
            'is_active' => 'sometimes|boolean'
        ];
    }
}
