@extends('common.main')

@section('title', 'Records | PUP Enrollment Portal')
@section('content')

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    <h1 class="h3 fw-bold mb-1">Records</h1>
    <p class="text-muted mb-4">View your enrolled subjects, schedules, faculty, and encoded grades.</p>

    @forelse ($applications as $app)
        <div class="card shadow-sm mb-4 border border-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
                
                <div>
                    <div class="d-flex align-items-baseline mb-1">
                        <h5 class="mb-0 fw-bold text-dark">{{ $app->semester->label ?? 'N/A' }}</h5>
                        <span class="text-muted small ms-2 fw-medium">{{ $app->semester->academicYear->label ?? '' }}</span>
                    </div>
                    <div class="text-secondary small d-flex align-items-center gap-2">
                        <span>{{ $app->yearLevel->label ?? 'N/A' }}</span>
                        <span class="text-muted">•</span>
                        <span>{{ $app->sectionAssignment->section->name ?? 'Section pending' }}</span>
                    </div>
                </div>

                <div>
                    @if($app->gwa !== null)
                        <div class="border rounded px-3 py-2 bg-light text-center shadow-sm">
                            <span class="d-block text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                Semester GWA
                            </span>
                            <span class="d-block fs-5 fw-bold text-success" style="line-height: 1;">
                                {{ number_format($app->gwa, 2) }}
                            </span>
                        </div>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border">Not yet graded</span>
                    @endif
                </div>
                
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
                                    @if ($enrollment->grade !== null)
                                        <span class="{{ (float) $enrollment->grade <= 3.00 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format((float) $enrollment->grade, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($enrollment->grade !== null)
                                        <span>
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
