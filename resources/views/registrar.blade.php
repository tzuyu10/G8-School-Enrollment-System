<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Dashboard | PUP Enrollment Portal</title>
    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    @include('common.navbar')
    @include('common.sidebar')

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

        {{-- Summary card --}}
        <div class="border rounded p-3 mb-4">
            <div class="text-muted small">Pending Applications</div>
            <div class="display-6 fw-bold text-warning">{{ $pendingApplications }}</div>
        </div>

        {{-- Applications table --}}
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
                                        style="background-color: {{ $app->status->color ?? '#6c757d' }}">
                                        {{ $app->status->label ?? ucfirst($statusCode) }}
                                    </span>
                                </td>
                                <td class="small">
                                    {{-- Note: Ensure 'submitted_at' is added to $casts as 'datetime' in your Application Model --}}
                                    {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if ($statusCode === 'pending')
                                        <button
                                            class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal"
                                            data-app-id="{{ $app->id }}"
                                            data-student-name="{{ $app->student->full_name ?? 'N/A' }}"
                                            data-year-level-id="{{ $app->year_level_id }}"
                                            data-semester-id="{{ $app->semester_id }}">
                                            Approve
                                        </button>
                                        <button
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal"
                                            data-app-id="{{ $app->id }}"
                                            data-student-name="{{ $app->student->full_name ?? 'N/A' }}">
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
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Application</h5>
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
                            Subjects will be auto-assigned from the section's offerings.
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
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
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

    {{-- Script Engine --}}
    <script>
        // Securely encode PHP collection payload to escape raw quotes/XSS
        const allSections = {!! json_encode($sections->map(fn($s) => [
            'id'           => $s->id,
            'name'         => $s->name,
            'year_level_id'=> $s->year_level_id,
            'semester_id'  => $s->semester_id,
            'program_code' => $s->program->code ?? '',
            'year_label'   => $s->yearLevel->label ?? '',
            'sem_label'    => $s->semester->label ?? '',
        ])) !!};

        // Approve Modal Event Handling
        document.getElementById('approveModal').addEventListener('show.bs.modal', function (event) {
            const btn         = event.relatedTarget;
            const appId       = btn.getAttribute('data-app-id');
            const studentName = btn.getAttribute('data-student-name');
            
            // Cast string metadata to strictly typed numeric forms
            const yearLevelId = Number(btn.getAttribute('data-year-level-id'));
            const semesterId  = Number(btn.getAttribute('data-semester-id'));

            document.getElementById('approveStudentName').textContent = studentName;
            document.getElementById('approveForm').action = `/registrar/applications/${appId}/approve`;

            // Filter configuration via accurate dataset types
            const filtered = allSections.filter(s =>
                s.year_level_id === yearLevelId && s.semester_id === semesterId
            );

            const select = document.getElementById('section_id');
            select.innerHTML = '<option value="">Select a section</option>';

            if (filtered.length === 0) {
                const opt = document.createElement('option');
                opt.disabled = true;
                opt.textContent = 'No sections available for this year level and semester';
                select.appendChild(opt);
                document.getElementById('sectionHint').textContent =
                    'No matching sections found. Please create sections first.';
            } else {
                document.getElementById('sectionHint').textContent =
                    `${filtered.length} section(s) available.`;
                filtered.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = `${s.name} (${s.program_code} — ${s.year_label} — ${s.sem_label})`;
                    select.appendChild(opt);
                });
            }
        });

        // Reject Modal Event Handling
        document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
            const btn         = event.relatedTarget;
            const appId       = btn.getAttribute('data-app-id');
            const studentName = btn.getAttribute('data-student-name');
            document.getElementById('rejectStudentName').textContent = studentName;
            document.getElementById('rejectForm').action = `/registrar/applications/${appId}/reject`;
        });
    </script>
</body>
</html>