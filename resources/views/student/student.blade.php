@extends('common.main')

@section('title', 'Student Dashboard | PUP Enrollment Portal')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Student Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ $user->full_name }}.</p>
        </div>
        @if (!$hasActiveApplication)
            <a href="{{ route('enroll.form') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Enroll
            </a>
        @else
            <span class="btn btn-secondary disabled">
                <i class="bi bi-clock me-1"></i> Already Enrolled
            </span>
        @endif
    </div>

    <section class="mb-4">
        <h2 class="h5 fw-bold">Profile Summary</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr>
                        <th scope="row" class="w-25">Full Name</th>
                        <td>{{ $user->full_name }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
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
                        <td>
                            <span class="badge rounded-pill bg-success">
                                {{ ucfirst($user->status->label ?? $user->status->code) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Student Type</th>
                        <td>{{ ucfirst($user->studentProfile?->student_type ?? 'N/A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <h2 class="h5 fw-bold mb-3">Enrollment Applications</h2>

        @forelse ($applications as $app)
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold">{{ $app->semester->label ?? 'N/A' }}</span>
                        <span class="text-muted small ms-2">{{ $app->semester->academicYear->label ?? '' }}</span>
                    </div>
                    <span class="badge rounded-pill" style="background-color: {{ $app->status->color ?? '#6c757d' }}">
                        {{ $app->status->label ?? ucfirst($app->status->code) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Program</div>
                            <div class="fw-semibold">{{ $app->program->code ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $app->program->name ?? '' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Year Level</div>
                            <div class="fw-semibold">{{ $app->yearLevel->label ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Submitted</div>
                            <div class="fw-semibold">{{ optional($app->submitted_at)->format('M d, Y') }}</div>
                        </div>
                    </div>

                    @if ($app->status->code === 'approved' || $app->subjectEnrollments->isNotEmpty())
                        <hr>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Assigned Section</div>
                            <div class="fw-semibold text-success">
                                <i class="bi bi-building me-1"></i>
                                {{ $app->sectionAssignment->section->name ?? 'Not yet assigned' }}
                            </div>
                        </div>
                        @if ($app->subjectEnrollments->isNotEmpty())
                            <div class="text-muted small mb-2">
                                {{ $app->status->code === 'approved' ? 'Enrolled Subjects' : 'Requested Subjects' }}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code</th><th>Subject</th><th>Units</th>
                                            <th>Schedule</th><th>Room</th><th>Faculty</th><th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($app->subjectEnrollments as $enrollment)
                                            <tr>
                                                <td class="fw-semibold">{{ $enrollment->subjectOffering->subject->code ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->subjectOffering->subject->title ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $enrollment->subjectOffering->subject->units ?? '-' }}</td>
                                                <td>{{ $enrollment->subjectOffering->schedule ?? 'TBA' }}</td>
                                                <td>{{ $enrollment->subjectOffering->room ?? 'TBA' }}</td>
                                                <td>{{ $enrollment->subjectOffering->faculty->full_name ?? 'TBA' }}</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $enrollment->status->color ?? 'secondary' }} {{ ($enrollment->status->code ?? '') === 'pending' ? 'text-dark' : '' }}">
                                                        {{ $enrollment->status->label ?? 'Enrolled' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="fw-semibold">Total Units</td>
                                            <td class="text-center fw-bold">
                                                {{ $app->subjectEnrollments->sum(fn($e) => $e->subjectOffering->subject->units ?? 0) }}
                                            </td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                No subjects have been assigned yet. Please check back later.
                            </div>
                        @endif

                        @if ($app->status->code === 'pending')
                            <div class="alert alert-warning small mt-3 mb-0">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Your selected subjects are being reviewed by the registrar.
                            </div>
                        @endif

                    @elseif ($app->status->code === 'rejected')
                        <hr>
                        <div class="alert alert-danger small mb-0">
                            <i class="bi bi-x-circle me-1"></i>
                            <strong>Rejection Reason:</strong> {{ $app->remarks ?? 'No reason provided.' }}
                        </div>

                    @elseif ($app->status->code === 'pending')
                        <hr>
                        <div class="alert alert-warning small mb-0">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Your application is being reviewed by the registrar.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-secondary">
                <i class="bi bi-info-circle me-1"></i>
                No enrollment applications yet.
                <a href="{{ route('enroll.form') }}" class="alert-link">Click here to enroll.</a>
            </div>
        @endforelse
    </section>

@endsection
