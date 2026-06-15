@extends('common.main')
@section('title', 'Faculty Dashboard | PUP Enrollment Portal')
@section('content')

    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">Faculty Dashboard</h1>
        <p class="text-muted mb-4">View advised sections and assigned subject offerings.</p>

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
            <h2 class="h5 fw-bold">Class List</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Schedule</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjectOfferings as $offering)
                            <tr>
                                <td>{{ $offering->subject->code ?? $offering->subject->title ?? 'N/A' }}</td>
                                <td>{{ $offering->section->name ?? 'N/A' }}</td>
                                <td>{{ $offering->schedule ?? 'TBA' }}</td>
                                <td>{{ $offering->room ?? 'TBA' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No subject offerings assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
