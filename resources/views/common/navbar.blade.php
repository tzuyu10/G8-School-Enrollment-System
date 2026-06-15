<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<style>
  @media (max-width: 991.98px) {
      .navbar-collapse {
          background: white;
          margin-top: 10px;
          padding: 12px;
          border-radius: 8px;
          box-shadow: 0 4px 10px rgba(0,0,0,.1);
      }

      .navbar-nav {
          gap: 8px;
      }

      .navbar-nav .nav-link,
      .navbar-nav button.nav-link {
          padding: 8px 0;
      }
  }
</style>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm fixed-top">
    <div class="container-fluid px-4">

        @auth
        <!-- Sidebar Toggle -->
        <button
            id="sidebarToggle"
            type="button"
            class="btn p-1 me-2 border-0 d-flex align-items-center flex-shrink-0"
            aria-label="Toggle sidebar"
            aria-controls="mainSidebar"
            aria-expanded="false"
            style="color:#8B0000;">
            <i class="bi bi-list fs-4" id="sidebarToggleIcon"></i>
        </button>
        @endauth

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center me-auto" href="#">
            <img
                src="{{ asset('build/assets/images/pup-logo.png') }}"
                alt="Logo"
                width="40"
                height="40"
                class="me-2 flex-shrink-0">

            <div class="d-flex flex-column brand-text">
                <span class="fw-bold fs-5 lh-1" style="color:#8B0000;">
                    PUP Enrollment Portal
                </span>
                <small class="text-muted" style="font-size:0.65rem;">
                    PUP School Enrollment System
                </small>
            </div>
        </a>

        <!-- Mobile Menu Toggle -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav fs-5">

                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ auth()->check() ? match(auth()->user()->role->code) {
                            'admin'     => route('admin.dashboard'),
                            'registrar' => route('registrar.dashboard'),
                            'faculty'   => route('faculty.dashboard'),
                            'student'   => route('student.dashboard'),
                            default     => route('unauthorized'),
                        } : route('login') }}">
                        <i class="bi bi-house-door me-1"></i>
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                    <i class="bi bi-info-circle me-1"></i>
                        About
                    </a>
                </li>

                @auth
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="nav-link btn btn-link border-0 text-start w-100"
                            style="transition: color .3s ease;"
                            onmouseover="this.style.color='#dc3545'"
                            onmouseout="this.style.color=''">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Logout
                        </button>
                    </form>
                </li>
                @endauth

            </ul>
        </div>

    </div>
</nav>