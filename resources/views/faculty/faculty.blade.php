@extends('common.main')
@section('title', 'Faculty Dashboard | PUP Enrollment Portal')
@section('content')

    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">Faculty Dashboard</h1>
        <p class="text-muted mb-4">View advised sections and assigned subject offerings.</p>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <section class="mb-4">
            <h2 class="h5 fw-bold">Advised Sections</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($advisedSections as $section)
                            <tr>
                                <td>{{ $section->name }}</td>
                                <td>{{ $section->program->code ?? $section->program->name ?? 'N/A' }}</td>
                                <td>{{ $section->yearLevel->label ?? 'N/A' }}</td>
                                <td>{{ $section->semester->label ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No advised sections yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="h5 fw-bold">Class List and Grade Encoding</h2>

            @forelse ($subjectOfferings as $offering)
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">{{ $offering->subject->code ?? 'N/A' }} - {{ $offering->subject->title ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $offering->section->name ?? 'N/A' }} | {{ $offering->schedule ?? 'TBA' }} | {{ $offering->room ?? 'TBA' }}</div>
                        </div>
                        <span class="badge bg-secondary align-self-center">{{ $offering->subjectEnrollments->count() }} student(s)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student No.</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Grade</th>
                                    <th>Remarks</th>
                                    <th style="width: 96px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($offering->subjectEnrollments as $enrollment)
                                    <tr>
                                        <td class="text-muted small">{{ $enrollment->enrollmentApplication->student->studentProfile->student_number ?? 'Pending' }}</td>
                                        <td>{{ $enrollment->enrollmentApplication->student->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-{{ $enrollment->status->color ?? 'secondary' }} {{ ($enrollment->status->code ?? '') === 'pending' ? 'text-dark' : '' }}">
                                                    {{ $enrollment->status->label ?? 'Enrolled' }}
                                            </span>
                                        </td>
                                        <td>
                                            <form id="grade-form-{{ $enrollment->id }}" method="POST" action="{{ route('faculty.grades.update', $enrollment->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="grade" class="form-control form-control-sm"
                                                    min="1" max="5" step="0.25" value="{{ $enrollment->grade }}">
                                            </form>
                                        </td>
                                        <td>
                                            <input type="text" name="remarks" class="form-control form-control-sm"
                                                maxlength="500" value="{{ $enrollment->remarks }}"
                                                form="grade-form-{{ $enrollment->id }}"
                                                placeholder="{{ $enrollment->grade_remark }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" type="submit" form="grade-form-{{ $enrollment->id }}">Save</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted text-center py-3">No enrolled students yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary">No subject offerings assigned yet.</div>
            @endforelse
        </section>
    </main>
@endsection
