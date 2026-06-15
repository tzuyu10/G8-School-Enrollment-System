<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Account credentials
            'full_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:profiles,email'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],

            // Enrollment form — student info
            'student_type'      => ['required', 'in:freshman,transferee,shiftee,returnee'],
            'birthdate'         => ['required', 'date', 'before:today'],
            'gender'            => ['required', 'in:male,female,other'],
            'civil_status'      => ['required', 'in:single,married,widowed'],
            'nationality'       => ['nullable', 'string', 'max:100'],
            'religion'          => ['nullable', 'string', 'max:100'],

            // Contact
            'contact_number'    => ['required', 'string', 'max:20'],
            'permanent_address' => ['required', 'string'],
            'current_address'   => ['nullable', 'string'],

            // Family
            'guardian_name'     => ['required', 'string', 'max:255'],
            'guardian_relation' => ['required', 'string', 'max:100'],
            'guardian_contact'  => ['required', 'string', 'max:20'],
            'father_name'       => ['nullable', 'string', 'max:255'],
            'mother_name'       => ['nullable', 'string', 'max:255'],

            // Academic background (required for transferees/shiftees)
            'previous_school'   => ['nullable', 'string', 'max:255'],
            'previous_program'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'          => 'This email is already registered.',
            'password.confirmed'    => 'Passwords do not match.',
            'student_type.in'       => 'Student type must be freshman, transferee, shiftee, or returnee.',
            'birthdate.before'      => 'Birthdate must be a past date.',
            'gender.in'             => 'Gender must be male, female, or other.',
            'civil_status.in'       => 'Civil status must be single, married, or widowed.',
        ];
    }
}