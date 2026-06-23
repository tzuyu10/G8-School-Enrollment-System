<link rel="stylesheet" href="{{ asset('css/common/sidebar.css') }}">

{{-- Backdrop overlay (mobile only) --}}
<div id="sidebarBackdrop"></div>

{{-- Sidebar collapse/expand/reopen handle --}}
<button
    type="button"
    id="sidebarArrowToggle"
    class="sidebar-arrow-toggle"
    aria-label="Toggle sidebar"
    aria-controls="mainSidebar"
    aria-expanded="false">
    <i class="bi bi-chevron-right"></i>
</button>

<aside class="sidebar bg-dark text-white" id="mainSidebar">

    <div class="sidebar-sticky" style="padding-top: 1rem;">
        <ul class="nav flex-column fs-5">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('*.dashboard') ? 'active' : '' }}"
                   href="{{ auth()->check() ? match (auth()->user()->role->code) {
                    'admin'     => route('admin.dashboard'),
                    'registrar' => route('registrar.dashboard'),
                    'faculty'   => route('faculty.dashboard'),
                    'student'   => route('student.dashboard'),
                    default     => route('unauthorized'),
                } : route('login') }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>
            @if (auth()->user()?->role?->code === 'student')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('enroll.*') ? 'active' : '' }}"
                       href="{{ route('enroll.form') }}">
                        <i class="bi bi-pencil-square me-2"></i>
                        <span class="nav-label">Enrollment</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()?->role?->code === 'admin')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                       href="{{ route('admin.users') }}">
                        <i class="bi bi-people me-2"></i>
                        <span class="nav-label">Users</span>
                    </a>
                </li>
            @endif
           @if (auth()->user()?->role?->code === 'registrar')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('registrar.dashboard', 'registrar.approve', 'registrar.reject') ? 'active' : '' }}"
                    href="{{ route('registrar.dashboard') }}">
                        <i class="bi bi-clipboard-check me-2"></i>
                        <span class="nav-label">Applications</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('registrar.faculty*') ? 'active' : '' }}"
                    href="{{ route('registrar.faculty') }}">
                        <i class="bi bi-person-workspace me-2"></i>
                        <span class="nav-label">Faculty</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()?->role?->code === 'faculty')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('faculty.*') ? 'active' : '' }}"
                       href="{{ route('faculty.dashboard') }}">
                        <i class="bi bi-journal-text me-2"></i>
                        <span class="nav-label">Classes</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()?->role?->code === 'student')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('student.records') ? 'active' : '' }}"
                   href="{{ route('student.records') }}">
                    <i class="bi bi-folder2-open me-2"></i>
                    <span class="nav-label">Records</span>
                </a>
            </li>
            @endif
            @if (auth()->user()?->role?->code === 'student')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('student.profile') ? 'active' : '' }}"
                       href="{{ route('student.profile') }}">
                        <i class="bi bi-person-circle me-2"></i>
                        <span class="nav-label">Profile</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="name-dashboard-bg">
        <div class="name-display">
            <h5 class="student-name">{{ auth()->user()->full_name ?? 'Guest User' }}</h5>
            <p class="student-id">{{ auth()->user()?->studentProfile?->student_number ?? auth()->user()?->email ?? '' }}</p>
        </div>
        <div class="sidebar-avatar">
            {{ collect(explode(' ', auth()->user()->full_name ?? 'G U'))
                ->filter()->map(fn($w) => strtoupper($w[0]))->take(2)->implode('') }}
        </div>
    </div>

</aside>