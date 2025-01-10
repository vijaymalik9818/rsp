@extends('layouts.session')
@extends('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => 'Sign In'])
    @include('layouts.head-css')
</head>

<body>

    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>

        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row justify-content-center g-0">

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <h5 class="text-primary">Create new password</h5>
                                        <p class="text-muted">Your new password must be different from previous used
                                            password.</p>

                                        <div class="p-2">
                                            <form action="{{ route('password.update') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="hidden" name="token" id="resetToken">
                                                        <input type="password" class="form-control pe-5 password-input"
                                                            name="password" onpaste="return false"
                                                            placeholder="Enter password" id="password-input"
                                                            aria-describedby="passwordInput"
                                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" id="password-addon"
                                                            onclick="togglePasswordVisibility('password-input')">
                                                            <i class="ri-eye-fill align-middle"></i>
                                                        </button>
                                                    </div>
                                                    <div id="passwordInput" class="form-text">Must be at least 8
                                                        characters.</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="confirm-password-input">Confirm
                                                        Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input"
                                                            name="password" onpaste="return false"
                                                            placeholder="Confirm password"
                                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                            id="confirm-password-input" required>
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" id="confirm-password-addon"
                                                            onclick="togglePasswordVisibility('confirm-password-input')">
                                                            <i class="ri-eye-fill align-middle"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-13">Password must contain:</h5>
                                                    <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8
                                                            characters</b></p>
                                                    <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b>
                                                        letter (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-12 mb-2">At least
                                                        <b>uppercase</b> letter (A-Z)
                                                    </p>
                                                    <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b>
                                                        (0-9)</p>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Remember
                                                        me</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Reset
                                                        Password</button>
                                                </div>

                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Wait, I remember my password... <a href="/logout"
                                                    class="fw-semibold text-primary text-decoration-underline"> Click
                                                    here </a> </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> RSP. Crafted with <i class="mdi mdi-heart text-danger"></i> by
                                Peregrine
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    @include('layouts.vendor-scripts');

    <script src="assets/js/pages/passowrd-create.init.js"></script>
    <script>
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var passwordAddon = document.getElementById(inputId + "-addon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordAddon.innerHTML = '<i class="ri-eye-off-fill align-middle"></i>';
            } else {
                passwordInput.type = "password";
                passwordAddon.innerHTML = '<i class="ri-eye-fill align-middle"></i>';
            }
        }
        var currentUrl = "{{ url()->current() }}";
        var urlParts = currentUrl.split('/');
        var tokenIndex = urlParts.indexOf('reset-password') + 1;
        var token = urlParts[tokenIndex];
        document.getElementById('resetToken').value = token;
    </script>

</body>
