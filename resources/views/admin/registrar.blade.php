@extends('common.main')
@section('title', 'Registrar Dashboard | PUP Enrollment Portal')
@section('content')

    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">Registrar Dashboard</h1>
        <p class="text-muted mb-4">Review enrollment applications and student records.</p>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="border rounded p-3 mb-4">
            <div class="text-muted small">Pending Applications</div>
            <div class="display-6 fw-bold text-warning">{{ $pendingApplications }}</div>
        </div>

        <section>
            <h2 class="h5 fw-bold mb-3">Recent Applications</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Student</th>
                            <th>Student No.</th>
                            <th>Semester</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentApplications as $app)
                            <tr>
                                <td>{{ $app->student->full_name ?? 'N/A' }}</td>
                                <td class="text-muted small">
                                    {{ $app->student->studentProfile->student_number ?? 'Not assigned' }}
                                </td>
                                <td>{{ $app->semester->label ?? 'N/A' }}</td>
                                <td>{{ $app->program->code ?? 'N/A' }}</td>
                                <td>{{ $app->yearLevel->label ?? 'N/A' }}</td>
                                <td>
                                    @php $statusCode = $app->status->code ?? 'pending'; @endphp
                                    <span class="badge rounded-pill"
                                        style="background-color: {{ $app->status->color ?? '#6c757d' }}; color: #6c757d;">
                                        {{ $app->status->label ?? ucfirst($statusCode) }}
                                    </span>
                                </td>
                                <td class="small">{{ optional($app->submitted_at)->format('M d, Y') }}</td>
                                <td>
                                    @if ($statusCode === 'pending')
                                        <button
                                            class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal"
                                            data-app-id="{{ $app->id }}"
                                            data-student-name="{{ $app->student->full_name }}"
                                            data-year-level-id="{{ $app->year_level_id }}"
                                            data-semester-id="{{ $app->semester_id }}"
                                            data-requested-section-id="{{ $app->subjectEnrollments->pluck('subjectOffering.section_id')->filter()->unique()->first() }}">
                                            Approve
                                        </button>
                                        <button
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal"
                                            data-app-id="{{ $app->id }}"
                                            data-student-name="{{ $app->student->full_name }}">
                                            Reject
                                        </button>
                                    @elseif ($statusCode === 'approved')
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            {{ $app->sectionAssignment->section->name ?? 'N/A' }}
                                        </span>
                                    @elseif ($statusCode === 'rejected')
                                        <span class="text-danger small">
                                            <i class="bi bi-x-circle-fill me-1"></i>
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted text-center py-3">No applications yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    {{-- Approve Modal --}}
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="approveForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Approving application for <strong id="approveStudentName"></strong>.</p>
                        <div class="mb-3">
                            <label for="section_id" class="form-label fw-semibold">
                                Assign Section <span class="text-danger">*</span>
                            </label>
                            <select name="section_id" id="section_id" class="form-select" required>
                                <option value="">Select a section</option>
                            </select>
                            <div class="form-text text-muted" id="sectionHint"></div>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Only sections matching the student's year level and semester are shown.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Rejecting application for <strong id="rejectStudentName"></strong>.</p>
                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-semibold">
                                Reason for Rejection <span class="text-danger">*</span>
                            </label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3"
                                placeholder="e.g. Incomplete requirements, invalid documents..."
                                required maxlength="500"></textarea>
                            <div class="form-text">Max 500 characters.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const allSections = {!! $sectionsJson !!};

        document.getElementById('approveModal').addEventListener('show.bs.modal', function (event) {
            const btn         = event.relatedTarget;
            const appId       = btn.getAttribute('data-app-id');
            const studentName = btn.getAttribute('data-student-name');
            const yearLevelId = btn.getAttribute('data-year-level-id');
            const semesterId  = btn.getAttribute('data-semester-id');
            const requestedSectionId = btn.getAttribute('data-requested-section-id');

            document.getElementById('approveStudentName').textContent = studentName;
            document.getElementById('approveForm').action = `/registrar/applications/${appId}/approve`;

            const filtered = allSections.filter(s =>
                s.year_level_id === yearLevelId && s.semester_id === semesterId
                && (!requestedSectionId || s.id === requestedSectionId)
            );

            const select = document.getElementById('section_id');
            select.innerHTML = '<option value="">Select a section</option>';

            if (filtered.length === 0) {
                const opt = document.createElement('option');
                opt.disabled = true;
                opt.textContent = 'No sections available for this year level and semester';
                select.appendChild(opt);
                document.getElementById('sectionHint').textContent = 'No matching sections found.';
            } else {
                document.getElementById('sectionHint').textContent = filtered.length + ' section(s) available.';
                filtered.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name + ' (' + s.program_code + ' — ' + s.year_label + ' — ' + s.sem_label + ')';
                    select.appendChild(opt);
                });
            }
        });

        document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
            const btn         = event.relatedTarget;
            const appId       = btn.getAttribute('data-app-id');
            const studentName = btn.getAttribute('data-student-name');
            document.getElementById('rejectStudentName').textContent = studentName;
            document.getElementById('rejectForm').action = `/registrar/applications/${appId}/reject`;
        });
    </script>
@endsection
