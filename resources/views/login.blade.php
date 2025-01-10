@include('layouts.session')

@include('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => 'Sign In'])
    @include('layouts.head-css')
</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                    <img src="{{ asset('assets/images/rsplogo.png') }}" alt="" height="70">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <p class="text-muted">Sign in to continue to RSP.</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <form action="{{ route('signin') }}" method="POST" id="signinForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Email Address</label>
                                            <input type="text" class="form-control" id="username" name="email"
                                                placeholder="Enter email address" value="{{ old('username') }}" >
                                        </div>

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="{{ route('forgot') }}" class="text-muted">Forgot password?</a>
                                            </div>
                                            <label for="password-input" class="form-label">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input"
                                                    placeholder="Enter password" id="password-input" name="password">
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                    type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="auth-remember-check" name="remember">
                                            <label class="form-check-label" for="auth-remember-check">Remember
                                                me</label>
                                        </div>

                                        @if (session()->has('error'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('error') }}
                                            </div>
                                        @endif
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit" id="submitBtn"
                                                >Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        @include('layouts.footer')
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    @include('layouts.vendor-scripts')

    <!-- particles js -->
    <script src="{{ asset('assets/libs/particles.js/particles.js') }}"></script>
    <!-- particles app js -->
    <script src="{{ asset('assets/js/pages/particles.app.js') }}"></script>
    <!-- password-addon init -->
    <script src="{{ asset('assets/js/pages/password-addon.init.js') }}"></script>
    <!-- jQuery library (CDN) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        // $('#submitBtn').prop('disabled', true);
        function checkFields() {
            var email = $('#username').val();
            var password = $('#password-input').val();
            if (email.trim() !== '' && password.trim() !== '') {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        }

        $(window).on('load', function() {
            $('#username, #password-input').trigger('input');
        });

        $('#username, #password-input').on('input', function() {
            checkFields();
        });
    });
</script>

</body>

</html>
