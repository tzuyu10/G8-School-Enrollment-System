@extends('common.main')

@section('title', 'My Profile | PUP Enrollment Portal')

<link rel="stylesheet" href="{{ asset('css/student/profile.css') }}">

@push('styles')
@endpush
@section('content')

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">My Profile</h1>
            <p class="text-muted mb-0">View and manage your personal information.</p>
        </div>
    </div>

    <section class="mb-4">
        <div class="card info-card completion-card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-bold mb-0">Profile Completion</h2>
                    <span class="badge rounded-pill {{ $profileStatus['is_complete'] ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $profileStatus['label'] }}
                    </span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $profileStatus['is_complete'] ? 'bg-success' : 'bg-warning' }}"
                         role="progressbar"
                         style="width: {{ $profileStatus['percentage'] }}%"
                         aria-valuenow="{{ $profileStatus['percentage'] }}"
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <div class="small text-muted mt-2">{{ $profileStatus['percentage'] }}% complete</div>
                @if (!$profileStatus['is_complete'])
                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Please complete the following before enrolling:
                        <ul class="mb-0 mt-1">
                            @foreach ($profileStatus['missing_fields'] as $field)
                                <li>{{ ucwords(str_replace('_', ' ', $field)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Account Information</h2>
        <div class="card info-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr><th scope="row" class="w-25">Full Name</th><td>{{ $user->full_name }}</td></tr>
                        <tr><th scope="row">Email</th><td>{{ $user->email }}</td></tr>
                        <tr>
                            <th scope="row">Student Number</th>
                            <td>
                                @if ($user->studentProfile?->student_number)
                                    <span class="fw-semibold text-danger">{{ $user->studentProfile->student_number }}</span>
                                @else
                                    <span class="text-muted fst-italic">Not assigned yet</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Account Status</th>
                            <td><span class="badge rounded-pill bg-success">{{ ucfirst($user->status->label ?? $user->status->code) }}</span></td>
                        </tr>
                        <tr><th scope="row">Student Type</th><td>{{ ucfirst($user->studentProfile?->student_type ?? 'N/A') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Personal Information</h2>
        <div class="card info-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr><th scope="row" class="w-25">Birthdate</th><td>{{ optional($user->studentProfile?->birthdate)->format('M d, Y') ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Gender</th><td>{{ ucfirst($user->studentProfile?->gender ?? 'N/A') }}</td></tr>
                        <tr><th scope="row">Civil Status</th><td>{{ ucfirst($user->studentProfile?->civil_status ?? 'N/A') }}</td></tr>
                        <tr><th scope="row">Nationality</th><td>{{ $user->studentProfile?->nationality ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Religion</th><td>{{ $user->studentProfile?->religion ?? 'N/A' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Contact Information</h2>
        <div class="card info-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr><th scope="row" class="w-25">Contact Number</th><td>{{ $user->studentProfile?->contact_number ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Permanent Address</th><td>{{ $user->studentProfile?->permanent_address ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Current Address</th><td>{{ $user->studentProfile?->current_address ?? 'Same as permanent address' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Family Background</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card info-card h-100 shadow-sm">
                    <div class="card-header">Father</div>
                    <div class="card-body">
                        @php $father = trim(implode(' ', array_filter([$user->studentProfile?->father_first_name, $user->studentProfile?->father_middle_name, $user->studentProfile?->father_last_name, $user->studentProfile?->father_suffix]))); @endphp
                        {{ $father !== '' ? $father : 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card info-card h-100 shadow-sm">
                    <div class="card-header">Mother</div>
                    <div class="card-body">
                        @php $mother = trim(implode(' ', array_filter([$user->studentProfile?->mother_first_name, $user->studentProfile?->mother_middle_name, $user->studentProfile?->mother_last_name, $user->studentProfile?->mother_suffix]))); @endphp
                        {{ $mother !== '' ? $mother : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Guardian Information</h2>
        <div class="card info-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th scope="row" class="w-25">Name</th>
                            <td>
                                @php $guardian = trim(implode(' ', array_filter([$user->studentProfile?->guardian_first_name, $user->studentProfile?->guardian_middle_name, $user->studentProfile?->guardian_last_name, $user->studentProfile?->guardian_suffix]))); @endphp
                                {{ $guardian !== '' ? $guardian : 'N/A' }}
                            </td>
                        </tr>
                        <tr><th scope="row">Relation to Student</th><td>{{ $user->studentProfile?->guardian_relation ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Contact Number</th><td>{{ $user->studentProfile?->guardian_contact ?? 'N/A' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section>
        <h2 class="h5 fw-bold">Academic Background</h2>
        <div class="card info-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr><th scope="row" class="w-25">Previous School</th><td>{{ $user->studentProfile?->previous_school ?? 'N/A' }}</td></tr>
                        <tr><th scope="row">Previous Program</th><td>{{ $user->studentProfile?->previous_program ?? 'N/A' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

@endsection