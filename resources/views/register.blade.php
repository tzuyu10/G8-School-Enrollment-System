<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/pup-logo.png') }}">
    <title>Register | PUP Enrollment Portal</title>
    @vite(['resources/sass/app.scss','resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .hero-right { display:flex; align-items:center; justify-content:center; width:100%; max-width:680px; }
        .login-card { width:100%; }
        .section-label { font-size:.7rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.45); margin-bottom:.5rem; margin-top:1rem; }
    </style>
</head>
<body>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>

    <a href="{{ route('login') }}" class="brand-mark d-flex align-items-center gap-2">
        <div class="brand-seal">
            <img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="PUP seal" style="width:42px;height:42px;border-radius:50%;">
        </div>
        <div>
            <div class="brand-name">PUP Enrollment Portal</div>
            <div class="brand-sub">PUP School Enrollment System</div>
        </div>
    </a>

    <div class="hero-left" aria-hidden="true">
        <span class="eyebrow-badge">Start Your Journey</span>
        <h1 class="hero-headline">JOIN<br>SINTA<br><span class="text-gold">TODAY</span></h1>
    </div>

    <div class="hero-right">
        <div class="login-card" style="max-height:82vh;overflow-y:auto;">
            <p class="card-title-custom mb-1">Create your account</p>
            <p class="card-sub mb-4">Submit your student account for approval.</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">Please review the form and try again.</div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST">
                @csrf

                {{-- Account Info --}}
                <p class="section-label">Account Information</p>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="first_name">First Name</label>
                        <input id="first_name" name="first_name" type="text"
                            class="form-control @error('first_name') is-invalid @enderror"
                            placeholder="e.g. Juan" value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="middle_name">Middle Name <span class="text-muted small">(optional)</span></label>
                        <input id="middle_name" name="middle_name" type="text"
                            class="form-control" placeholder="e.g. Santos"
                            value="{{ old('middle_name') }}">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input id="last_name" name="last_name" type="text"
                            class="form-control @error('last_name') is-invalid @enderror"
                            placeholder="e.g. dela Cruz" value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="suffix">Suffix <span class="text-muted small">(optional)</span></label>
                        <input id="suffix" name="suffix" type="text"
                            class="form-control" placeholder="e.g. Jr., III"
                            value="{{ old('suffix') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input id="email" name="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="e.g. juandelacruz@gmail.com"
                        value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min. 8 characters" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="form-control" placeholder="Re-enter password" required>
                    </div>
                </div>

                {{-- Personal Info --}}
                <p class="section-label">Personal Information</p>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="student_type">Student Type</label>
                        <select id="student_type" name="student_type"
                            class="form-control @error('student_type') is-invalid @enderror" required>
                            <option value="" disabled selected>Select type</option>
                            <option value="freshman"   @selected(old('student_type')==='freshman')>Freshman</option>
                            <option value="transferee" @selected(old('student_type')==='transferee')>Transferee</option>
                            <option value="shiftee"    @selected(old('student_type')==='shiftee')>Shiftee</option>
                            <option value="returnee"   @selected(old('student_type')==='returnee')>Returnee</option>
                        </select>
                        @error('student_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="birthdate">Birthdate</label>
                        <input id="birthdate" name="birthdate" type="date"
                            class="form-control @error('birthdate') is-invalid @enderror"
                            value="{{ old('birthdate') }}" required>
                        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender"
                            class="form-control @error('gender') is-invalid @enderror" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male"   @selected(old('gender')==='male')>Male</option>
                            <option value="female" @selected(old('gender')==='female')>Female</option>
                            <option value="other"  @selected(old('gender')==='other')>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="civil_status">Civil Status</label>
                        <select id="civil_status" name="civil_status"
                            class="form-control @error('civil_status') is-invalid @enderror" required>
                            <option value="" disabled selected>Select status</option>
                            <option value="single"  @selected(old('civil_status')==='single')>Single</option>
                            <option value="married" @selected(old('civil_status')==='married')>Married</option>
                            <option value="widowed" @selected(old('civil_status')==='widowed')>Widowed</option>
                        </select>
                        @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="nationality">Nationality</label>
                        <input id="nationality" name="nationality" type="text" class="form-control"
                            placeholder="e.g. Filipino" value="{{ old('nationality','Filipino') }}">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="religion">Religion</label>
                        <input id="religion" name="religion" type="text" class="form-control"
                            placeholder="e.g. Catholic" value="{{ old('religion') }}">
                    </div>
                </div>

                {{-- Contact & Address --}}
                <p class="section-label">Contact & Address</p>

                <div class="mb-3">
                    <label class="form-label" for="contact_number">Contact Number</label>
                    <input id="contact_number" name="contact_number" type="text"
                        class="form-control @error('contact_number') is-invalid @enderror"
                        placeholder="e.g. 09171234567" value="{{ old('contact_number') }}" required>
                    @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="permanent_address">Permanent Address</label>
                    <textarea id="permanent_address" name="permanent_address" rows="2"
                        class="form-control @error('permanent_address') is-invalid @enderror"
                        placeholder="e.g. 123 Mabini St, Sta. Mesa, Manila" required>{{ old('permanent_address') }}</textarea>
                    @error('permanent_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="current_address">Current Address <span class="text-muted small fw-normal">(if different from permanent)</span></label>
                    <textarea id="current_address" name="current_address" rows="2"
                        class="form-control" placeholder="Leave blank if same as permanent address">{{ old('current_address') }}</textarea>
                </div>

                {{-- Family Background --}}
                <p class="section-label">Family Background</p>

                {{-- Father --}}
                <p class="small text-muted mb-1">Father's Name</p>
                <div class="row g-2">
                    <div class="col-5 mb-2">
                        <input name="father_first_name" type="text" class="form-control"
                            placeholder="First name" value="{{ old('father_first_name') }}">
                    </div>
                    <div class="col-4 mb-2">
                        <input name="father_middle_name" type="text" class="form-control"
                            placeholder="Middle name" value="{{ old('father_middle_name') }}">
                    </div>
                    <div class="col-3 mb-2">
                        <input name="father_last_name" type="text" class="form-control"
                            placeholder="Last name" value="{{ old('father_last_name') }}">
                    </div>
                </div>

                {{-- Mother --}}
                <p class="small text-muted mb-1 mt-1">Mother's Maiden Name</p>
                <div class="row g-2">
                    <div class="col-5 mb-2">
                        <input name="mother_first_name" type="text" class="form-control"
                            placeholder="First name" value="{{ old('mother_first_name') }}">
                    </div>
                    <div class="col-4 mb-2">
                        <input name="mother_middle_name" type="text" class="form-control"
                            placeholder="Middle name" value="{{ old('mother_middle_name') }}">
                    </div>
                    <div class="col-3 mb-2">
                        <input name="mother_last_name" type="text" class="form-control"
                            placeholder="Last name" value="{{ old('mother_last_name') }}">
                    </div>
                </div>

                {{-- Guardian --}}
                <p class="small text-muted mb-1 mt-1">Guardian's Name</p>
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <input name="guardian_first_name" type="text"
                            class="form-control @error('guardian_first_name') is-invalid @enderror"
                            placeholder="First name" value="{{ old('guardian_first_name') }}" required>
                        @error('guardian_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-4">
                        <input name="guardian_middle_name" type="text" class="form-control"
                            placeholder="Middle name" value="{{ old('guardian_middle_name') }}">
                    </div>
                    <div class="col-3">
                        <input name="guardian_last_name" type="text"
                            class="form-control @error('guardian_last_name') is-invalid @enderror"
                            placeholder="Last name" value="{{ old('guardian_last_name') }}" required>
                        @error('guardian_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="guardian_relation">Guardian Relation</label>
                        <input id="guardian_relation" name="guardian_relation" type="text"
                            class="form-control @error('guardian_relation') is-invalid @enderror"
                            placeholder="e.g. Mother, Father, Sibling"
                            value="{{ old('guardian_relation') }}" required>
                        @error('guardian_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="guardian_contact">Guardian Contact</label>
                        <input id="guardian_contact" name="guardian_contact" type="text"
                            class="form-control @error('guardian_contact') is-invalid @enderror"
                            placeholder="e.g. 09181234567"
                            value="{{ old('guardian_contact') }}" required>
                        @error('guardian_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Academic Background (transferee/shiftee only) --}}
                <div id="prev-school-section" style="display:none;">
                    <p class="section-label">Academic Background</p>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label" for="previous_school">Previous School</label>
                            <input id="previous_school" name="previous_school" type="text"
                                class="form-control" placeholder="e.g. University of Santo Tomas"
                                value="{{ old('previous_school') }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label" for="previous_program">Previous Program</label>
                            <input id="previous_program" name="previous_program" type="text"
                                class="form-control" placeholder="e.g. BS Computer Science"
                                value="{{ old('previous_program') }}">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-signin w-100 mt-2">Submit Registration</button>
            </form>

            <p class="contact-note text-center mt-3 mb-0">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </div>
    </div>
</section>

@include('common.footer')

<script>
    const studentTypeSelect = document.getElementById('student_type');
    const prevSchoolSection = document.getElementById('prev-school-section');
    function togglePrevSchool() {
        const type = studentTypeSelect.value;
        prevSchoolSection.style.display = ['transferee','shiftee'].includes(type) ? 'block' : 'none';
    }
    studentTypeSelect.addEventListener('change', togglePrevSchool);
    togglePrevSchool();
</script>
</body>
</html>