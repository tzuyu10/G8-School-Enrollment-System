<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management | PUP Enrollment Portal</title>
    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
    ])
</head>
<body>
    @include('common.navbar')
    @include('common.sidebar')

    <main class="main-content p-4">
        <h1 class="h3 fw-bold mb-1">User Management</h1>
        <p class="text-muted mb-4">View and filter portal accounts.</p>

        @if (session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">Please review the form and try again.</div>
        @endif

        <section class="border rounded p-3 mb-4">
            <h2 class="h5 fw-bold">Create Staff Account</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="row g-2">
                @csrf
                <div class="col-md-3">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input id="full_name" name="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    <button type="submit" class="btn btn-danger w-100">Create</button>
                </div>
            </form>
        </section>

        <form method="GET" action="{{ route('admin.users') }}" class="row g-2 mb-4">
            <div class="col-md-4">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->code }}" @selected(($filters['role'] ?? '') === $role->code)>{{ $role->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->code }}" @selected(($filters['status'] ?? '') === $status->code)>{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-danger">Apply Filters</button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Student Number</th>
                        <th>Update</th>
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
                            <td><button type="submit" form="update-user-{{ $user->id }}" class="btn btn-sm btn-outline-danger">Save</button></td>
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
</body>
</html>
