
    <link rel="stylesheet" href="{{ asset('css/common/sidebar.css') }}">

    <aside class="sidebar bg-dark text-white">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ auth()->check() ? match (auth()->user()->role->code) {
                        'admin' => route('admin.dashboard'),
                        'registrar' => route('registrar.dashboard'),
                        'faculty' => route('faculty.dashboard'),
                        'student' => route('student.dashboard'),
                        default => route('unauthorized'),
                    } : route('login') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                </li>
                @if (auth()->user()?->role?->code === 'student')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('enroll.form') }}">
                            <i class="bi bi-pencil-square me-2"></i> Enrollment
                        </a>
                    </li>
                @endif
                @if (auth()->user()?->role?->code === 'admin')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.users') }}">
                            <i class="bi bi-people me-2"></i> Users
                        </a>
                    </li>
                @endif
                @if (auth()->user()?->role?->code === 'registrar')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('registrar.dashboard') }}">
                            <i class="bi bi-clipboard-check me-2"></i> Applications
                        </a>
                    </li>
                @endif
                @if (auth()->user()?->role?->code === 'faculty')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('faculty.dashboard') }}">
                            <i class="bi bi-journal-text me-2"></i> Classes
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-folder2-open me-2"></i> Records
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </a>
                </li>
        </ul>
        </div>
            <div class="name-dashboard-bg">
                <div class="name-display">
                    <h5 class="student-name">{{ auth()->user()->full_name ?? 'Guest User' }}</h5>
                    <p class="student-id">{{ auth()->user()?->studentProfile?->student_number ?? auth()->user()?->email ?? '' }}</p>
                </div>
            </div>
        </div>
</aside>
