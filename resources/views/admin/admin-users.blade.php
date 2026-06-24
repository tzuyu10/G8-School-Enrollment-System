@extends('common.main')

@section('title', 'User Management | PUP Enrollment Portal')
@section('content')

<style>
    .card {
        border-radius: 16px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
    }

    .btn {
        border-radius: 10px;
    }
</style>

<main class="main-content p-4">
    <h1 class="h3 fw-bold mb-1">User Management</h1>
    <p class="text-muted mb-4">View and filter portal accounts.</p>

    @if (session('status'))
    <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first('delete_user') ?: 'Please review the form and try again.' }}
    </div>
    @endif

    <section class="border rounded p-3 mb-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h2 class="h4 fw-semibold mb-4">
                    Create Staff Account
                </h2>

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <h6 class="text-muted mb-3">Personal Information</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label" for="first_name">First Name</label>
                            <input id="first_name" name="first_name" type="text"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name') }}" required>
                            @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="middle_name">Middle Name</label>
                            <input id="middle_name" name="middle_name" type="text"
                                class="form-control @error('middle_name') is-invalid @enderror"
                                value="{{ old('middle_name') }}">
                            @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input id="last_name" name="last_name" type="text"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name') }}" required>
                            @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="suffix">Suffix</label>
                            <input id="suffix" name="suffix" type="text"
                                class="form-control @error('suffix') is-invalid @enderror"
                                value="{{ old('suffix') }}" placeholder="Jr., Sr.">
                            @error('suffix')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">Account Information</h6>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="email">Email Address</label>
                            <input id="email" name="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="password">Password</label>
                            <input id="password" name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="new_role">Role</label>
                            <select id="new_role" name="role" class="form-select" required>
                                <option value="faculty">Faculty</option>
                                <option value="registrar">Registrar</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                Create Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h2 class="h5 fw-semibold mb-3">Filter Users</h2>

        <form method="GET" action="{{ route('admin.users') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->code }}"
                            @selected(($filters['role'] ?? '') === $role->code)>
                            {{ $role->label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $status)
                        <option value="{{ $status->code }}"
                            @selected(($filters['status'] ?? '') === $status->code)>
                            {{ $status->label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel me-1"></i> Apply</button>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle me-1"></i> Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Student Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td>
                            <form id="update-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                @csrf
                                @method('PUT')
                            </form>
                            {{ $user->full_name }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <select name="role" form="update-user-{{ $user->id }}" class="form-select form-select-sm">
                                @foreach ($roles as $role)
                                <option value="{{ $role->code }}" @selected($user->role?->code === $role->code)>{{ $role->label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="status" form="update-user-{{ $user->id }}" class="form-select form-select-sm">
                                @foreach ($statuses as $status)
                                <option value="{{ $status->code }}" @selected($user->status?->code === $status->code)>{{ $status->label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ $user->studentProfile->student_number ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" form="update-user-{{ $user->id }}" class="btn btn-sm btn-success">Save</button>
                                @if ($user->status?->code === 'inactive')
                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        Activate
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}"
                                    data-deactivate-user-form
                                    data-user-name="{{ $user->full_name }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled(auth()->id() === $user->id)>
                                        Deactivate
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                    data-delete-user-form
                                    data-user-name="{{ $user->full_name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-role="{{ $user->role?->code }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="confirm_delete" value="">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" @disabled(auth()->id() === $user->id)>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-muted">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</main>
<script>
    document.querySelectorAll('[data-delete-user-form]').forEach(form => {
        form.addEventListener('submit', event => {
            const name = form.dataset.userName;
            const email = form.dataset.userEmail;
            const isStudent = form.dataset.userRole === 'student';

            if (!isStudent) {
                if (!confirm(`Delete ${name}? This cannot be undone.`)) {
                    event.preventDefault();
                }
                return;
            }

            const typed = prompt(`Permanent delete ${name} and all linked student records?\n\nType the student's email to confirm:\n${email}`);
            if (typed !== email) {
                event.preventDefault();
                return;
            }

            form.querySelector('input[name="confirm_delete"]').value = typed;
        });
    });

    document.querySelectorAll('[data-deactivate-user-form]').forEach(form => {
        form.addEventListener('submit', event => {
            const name = form.dataset.userName;

            if (!confirm(`Deactivate ${name}? They will no longer be able to access the portal.`)) {
                event.preventDefault();
            }
        });
    });
</script>
@endsection
