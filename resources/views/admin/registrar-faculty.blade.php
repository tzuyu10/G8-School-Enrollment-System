@extends('common.main')
@section('title', 'Registrar Faculty Assignments | PUP Enrollment Portal')
@section('content')

<style>
    .faculty-shell {
        align-items: flex-start;
    }

    .faculty-panel {
        position: sticky;
        top: 84px;
        height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
    }

    .faculty-scroll {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding-right: 0.25rem;
    }

    .faculty-item {
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .faculty-item.active {
        background: #8B0000;
        border-color: #8B0000;
        color: #fff;
    }

    .faculty-item.active .text-muted,
    .faculty-item.active .small {
        color: rgba(255, 255, 255, 0.82) !important;
    }
</style>

    <h1 class="h3 fw-bold mb-1">Faculty Assignments</h1>
    <p class="text-muted mb-4">View faculty loads and assign subjects, rooms, and schedules.</p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-4 faculty-shell">
        <div class="col-lg-4">
            <section class="border rounded p-3 faculty-panel">
                <h2 class="h5 fw-bold mb-3">Faculty Members</h2>

                <div class="list-group faculty-scroll">
                    @forelse ($facultyMembers as $member)
                        <a href="{{ route('registrar.faculty', $member->id) }}"
                        class="list-group-item list-group-item-action faculty-item {{ $selectedFaculty?->id === $member->id ? 'active' : '' }}">
                            <div class="fw-semibold">{{ $member->full_name }}</div>
                            <div class="small text-muted">{{ $member->email }}</div>
                            <div class="small mt-1">
                                {{ $member->subjectOfferings->count() }} subject(s),
                                {{ $member->subjectOfferings->pluck('section_id')->filter()->unique()->count() }} section(s)
                            </div>
                        </a>
                    @empty
                        <div class="text-muted small">No faculty accounts found.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            @if ($selectedFaculty)
                @php
                    $assignedSections = $selectedFaculty->subjectOfferings
                        ->pluck('section_id')
                        ->filter()
                        ->unique()
                        ->count();
                @endphp

                <section class="border rounded p-3 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h4 fw-bold mb-1">{{ $selectedFaculty->full_name }}</h2>
                            <div class="text-muted">{{ $selectedFaculty->email }}</div>
                        </div>

                        <div class="d-flex gap-2">
                            <span class="badge text-bg-primary">
                                {{ $selectedFaculty->subjectOfferings->count() }} Subject(s)
                            </span>
                            <span class="badge text-bg-success">
                                {{ $assignedSections }} Section(s)
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('registrar.faculty.offerings.assign', $selectedFaculty->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="subject_offering_id" class="form-label fw-semibold">Subject / Course Offering</label>
                                <select id="subject_offering_id" name="subject_offering_id" class="form-select" required>
                                    <option value="">Select subject offering</option>
                                    @foreach ($offerings as $offering)
                                        <option value="{{ $offering->id }}"
                                            data-room="{{ $offering->room }}"
                                            data-schedule="{{ $offering->schedule }}">
                                            {{ $offering->subject->code ?? 'N/A' }} -
                                            {{ $offering->subject->title ?? 'Untitled' }}
                                            |
                                            {{ $offering->section->name ?? 'No section' }}
                                            |
                                            {{ $offering->section->semester->label ?? 'No semester' }}
                                            @if ($offering->faculty)
                                                | Current: {{ $offering->faculty->full_name }}
                                            @else
                                                | Unassigned
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label for="room" class="form-label fw-semibold">Room</label>
                                <input id="room" name="room" type="text" class="form-control"
                                    placeholder="e.g. S501">
                            </div>

                            <div class="col-md-5">
                                <label for="schedule" class="form-label fw-semibold">Schedule</label>
                                <input id="schedule" name="schedule" type="text" class="form-control"
                                    placeholder="e.g. MWF 7:30-9:00 AM">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">
                                    Assign
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <section>
                    <h2 class="h5 fw-bold mb-3">Assigned Subjects</h2>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Subject</th>
                                    <th>Section</th>
                                    <th>Room</th>
                                    <th>Schedule</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($selectedFaculty->subjectOfferings as $offering)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $offering->subject->code ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $offering->subject->title ?? 'Untitled' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $offering->section->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">
                                                {{ $offering->section->program->code ?? 'N/A' }}
                                                |
                                                {{ $offering->section->yearLevel->label ?? 'N/A' }}
                                                |
                                                {{ $offering->section->semester->label ?? 'N/A' }}
                                            </div>
                                        </td>
                                        
                                        {{-- Room Input in its own column --}}
                                        <td>
                                            <input form="assign-form-{{ $offering->id }}" name="room" type="text" class="form-control form-control-sm" style="min-width: 100px;"
                                                value="{{ $offering->room }}" placeholder="Room">
                                        </td>

                                        {{-- Schedule Input in its own column --}}
                                        <td>
                                            <input form="assign-form-{{ $offering->id }}" name="schedule" type="text" class="form-control form-control-sm" style="min-width: 140px;"
                                                value="{{ $offering->schedule }}" placeholder="Schedule">
                                        </td>

                                        {{-- Action Buttons --}}
                                        <td>
                                            <div class="d-flex flex-nowrap gap-2">
                                                
                                                {{-- Assign Form --}}
                                                <form id="assign-form-{{ $offering->id }}" method="POST" action="{{ route('registrar.faculty.offerings.assign', $selectedFaculty->id) }}" class="m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="subject_offering_id" value="{{ $offering->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        Save
                                                    </button>
                                                </form>

                                                {{-- Remove Form --}}
                                                <form method="POST"
                                                    action="{{ route('registrar.faculty.offerings.unassign', $offering->id) }}"
                                                    onsubmit="return confirm('Remove this subject from {{ $selectedFaculty->full_name }}?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Remove
                                                    </button>
                                                </form>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">
                                            No assigned subjects yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                <section class="border rounded p-4 text-muted">
                    No faculty member selected.
                </section>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('subject_offering_id')?.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];

            document.getElementById('room').value = option.dataset.room || '';
            document.getElementById('schedule').value = option.dataset.schedule || '';
        });
    </script>
@endsection