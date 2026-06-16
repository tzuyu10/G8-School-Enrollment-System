@extends('common.main')

@section('title', 'Records | PUP Enrollment Portal')
@section('content')

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    <h1 class="h3 fw-bold mb-1">Records</h1>
    <p class="text-muted mb-4">View your enrolled subjects, schedules, faculty, and encoded grades.</p>

    @forelse ($applications as $app)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <span class="fw-semibold">{{ $app->semester->label ?? 'N/A' }}</span>
                    <span class="text-muted small ms-2">{{ $app->semester->academicYear->label ?? '' }}</span>
                    <div class="text-muted small">
                        {{ $app->program->code ?? 'N/A' }} | {{ $app->yearLevel->label ?? 'N/A' }} | {{ $app->sectionAssignment->section->name ?? 'Section pending' }}
                    </div>
                </div>
                <span class="badge rounded-pill align-self-center" style="background-color: {{ $app->status->color ?? '#6c757d' }}">
                    {{ $app->status->label ?? 'Pending' }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject Code</th>
                            <th>Description</th>
                            <th>Units</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Faculty</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($app->subjectEnrollments as $enrollment)
                            <tr>
                                <td class="fw-semibold">{{ $enrollment->subjectOffering->subject->code ?? 'N/A' }}</td>
                                <td>{{ $enrollment->subjectOffering->subject->title ?? 'N/A' }}</td>
                                <td class="text-center">{{ $enrollment->subjectOffering->subject->units ?? '-' }}</td>
                                <td>{{ $enrollment->subjectOffering->schedule ?? 'TBA' }}</td>
                                <td>{{ $enrollment->subjectOffering->room ?? 'TBA' }}</td>
                                <td>{{ $enrollment->subjectOffering->faculty->full_name ?? 'TBA' }}</td>
                                <td class="fw-semibold">
                                    {{ $enrollment->grade !== null ? number_format((float) $enrollment->grade, 2) : '-' }}
                                </td>
                                <td>
                                    @if ($enrollment->grade !== null)
                                        <span class="{{ (float) $enrollment->grade <= 3.00 ? 'text-success' : 'text-danger' }}">
                                            {{ $enrollment->remarks ?: $enrollment->grade_remark }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not encoded</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted text-center py-3">No subject records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($app->subjectEnrollments->isNotEmpty())
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-semibold">Total Units</td>
                                <td class="text-center fw-bold">
                                    {{ $app->subjectEnrollments->sum(fn($e) => $e->subjectOffering->subject->units ?? 0) }}
                                </td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">
            No enrollment records yet.
        </div>
    @endforelse
    
@endsection
