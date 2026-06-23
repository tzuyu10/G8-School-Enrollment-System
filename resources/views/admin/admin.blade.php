@extends('common.main')
@section('title', 'Admin Dashboard | PUP Enrollment Portal')
@section('content')
    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">Admin Dashboard</h1>
        <p class="text-muted mb-4">Monitor accounts and enrollment applications.</p>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="border rounded p-3">
                    <div class="text-muted small">Total Profiles</div>
                    <div class="display-6 fw-bold">{{ $profileCount }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3">
                    <div class="text-muted small">Pending Applications</div>
                    <div class="display-6 fw-bold">{{ $pendingApplications }}</div>
                </div>
            </div>
        </div>

        <section class="mb-4">
            <h2 class="h5 fw-bold">Demo Controls</h2>
            <div class="border rounded p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="fw-semibold">Semester Availability</div>
                    <div class="text-muted small">Make every semester selectable in demo enrollment forms.</div>
                </div>
                <form method="POST" action="{{ route('admin.semesters.activate-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-unlock me-1"></i> Make All Semesters Active
                    </button>
                </form>
            </div>
        </section>

        <section>
            <h2 class="h5 fw-bold">Recent Applications</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentApplications as $application)
                            <tr>
                                <td>{{ $application->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $application->program->code ?? $application->program->name ?? 'N/A' }}</td>
                                <td class="fw-semibold text-{{ $application->status->color ?? 'muted' }}">
                                    {{ $application->status->label ?? ucfirst($application->status->code ?? 'N/A') }}
                                </td>
                                <td>{{ optional($application->submitted_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No applications yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
