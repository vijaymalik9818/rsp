@extends('layouts.session')
@include('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => 'Reset Password'])
    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                                         <h2 class="text-primary">Reset Your Password</h2>
                                        {{-- <p class="text-muted">Reset password</p>  --}}
                                        

                                        <div class="mt-2 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop"
                                                colors="primary:#0ab39c" class="avatar-xl"></lord-icon>
                                        </div>

                                        <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                            @if (!session()->has('success') && !session()->has('error'))
                                                Enter your email and instructions will be sent to you!
                                            @endif

                                            @if (session()->has('success'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('success') }}
                                                </div>
                                            @endif

                                            @if (session()->has('error'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('error') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-2">
                                            <form id="resetPasswordForm" method="post"
                                                action="{{ route('password.email') }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        name="email" placeholder="Enter email address">
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button id="resetPasswordBtn" class="btn btn-success w-100"
                                                        type="submit">Send Reset
                                                        Link</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Wait, I remember my password... <a
                                                    href="{{ route('login') }}"
                                                    class="fw-bold text-primary text-decoration-underline"> Click here
                                                </a> </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @include('layouts.footer')

    </div>

    @include('layouts.vendor-scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resetPasswordForm').submit(function(event) {
                event.preventDefault();

                var email = $('#email').val();

                if (!email) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter your email address.'
                    });
                    return;
                } else {
                    $(this).unbind('submit').submit();
                }
            });
        });
    </script>
</body>
