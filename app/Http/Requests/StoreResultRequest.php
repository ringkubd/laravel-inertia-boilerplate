<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('Student');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $semester = $this->request->get('semester');
        $student_id = $this->request->get('student_id');
        return [
            'semester' => [
                'required', Rule::unique('results')->using(function ($query) use($student_id) {
                    return $query->where('student_id', $student_id)
                        ->whereNull('deleted_at')
                        ->where('status', '!=',"Referred");
                }),
                'max:8',
                'min:1',
            ],
            'student_id' => ['required'],
            'status' => ['required'],
            'failed_in_subject' => 'required',
            'supporting_document' => 'required'
        ];
    }
}
