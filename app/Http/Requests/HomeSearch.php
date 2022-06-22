<?php

namespace Gigtrooper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeSearch extends FormRequest
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
           'homeSearch.memberCategory' => 'required'
        ];
    }

	public function messages()
	{
		return [
			'homeSearch.memberCategory.required' => 'Please enter a service or a talent in the box to start searching.'
		];
	}
}
