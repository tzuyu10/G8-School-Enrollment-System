<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Account
            'first_name'  => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'suffix'      => ['nullable', 'string', 'max:20'],
            'email'       => ['required', 'email', 'unique:profiles,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],

            // Student info
            'student_type'      => ['required', 'in:freshman,transferee,shiftee,transferee_same_course,transferee_same_field,transferee_diff_field,shiftee_same_field,shiftee_diff_field', 'not_in:returnee'],
            'birthdate'         => ['required', 'date', 'before:today'],
            'gender'            => ['required', 'in:male,female,other'],
            'civil_status'      => ['required', 'in:single,married,widowed'],
            'nationality'       => ['nullable', 'string', 'max:100'],
            'religion'          => ['nullable', 'string', 'max:100'],

            // Contact
            'contact_number'    => ['required', 'string', 'max:20'],
            'permanent_address' => ['required', 'string'],
            'current_address'   => ['nullable', 'string'],

            // Father
            'father_first_name'  => ['nullable', 'string', 'max:100'],
            'father_middle_name' => ['nullable', 'string', 'max:100'],
            'father_last_name'   => ['nullable', 'string', 'max:100'],
            'father_suffix'      => ['nullable', 'string', 'max:20'],

            // Mother
            'mother_first_name'  => ['nullable', 'string', 'max:100'],
            'mother_middle_name' => ['nullable', 'string', 'max:100'],
            'mother_last_name'   => ['nullable', 'string', 'max:100'],
            'mother_suffix'      => ['nullable', 'string', 'max:20'],

            // Guardian
            'guardian_first_name'  => ['required', 'string', 'max:100'],
            'guardian_middle_name' => ['nullable', 'string', 'max:100'],
            'guardian_last_name'   => ['required', 'string', 'max:100'],
            'guardian_suffix'      => ['nullable', 'string', 'max:20'],
            'guardian_relation'    => ['required', 'string', 'max:100'],
            'guardian_contact'     => ['required', 'string', 'max:20'],

            // Academic background
            'previous_school'   => ['nullable', 'string', 'max:255'],
            'previous_program'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'          => 'First name is required.',
            'last_name.required'           => 'Last name is required.',
            'email.unique'                 => 'This email is already registered.',
            'password.confirmed'           => 'Passwords do not match.',
            'student_type.not_in'          => 'Returnees should contact the registrar instead of creating a new account.',
            'birthdate.before'             => 'Birthdate must be a past date.',
            'guardian_first_name.required' => 'Guardian first name is required.',
            'guardian_last_name.required'  => 'Guardian last name is required.',
        ];
    }
}
