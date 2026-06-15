@extends('common.main')
@section('title', 'Admin Dashboard | PUP Enrollment Portal')
@section('content')
    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">Admin Dashboard</h1>
        <p class="text-muted mb-4">Monitor accounts and enrollment applications.</p>

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
                                <td>{{ ucfirst($application->status->label ?? $application->status->code) }}</td>
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