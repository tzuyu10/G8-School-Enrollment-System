<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="Logo" width="40" height="40" class="me-2">
      <div class="d-flex flex-column">
        <span class="fw-bold fs-5 lh-1" style="color:#8B0000;">PUP Enrollment Portal</span>
        <small class="text-muted" style="font-size:0.65rem;">PUP School Enrollment System</small>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="{{ auth()->check() ? match (auth()->user()->role->code) {
            'admin' => route('admin.dashboard'),
            'registrar' => route('registrar.dashboard'),
            'faculty' => route('faculty.dashboard'),
            'student' => route('student.dashboard'),
            default => route('unauthorized'),
          } : route('login') }}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>
        <li class="nav-item">
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-link btn btn-link border-0">Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
