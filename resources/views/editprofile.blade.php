@extends('layouts.session')
@include('layouts.main')
{{-- @extends('layouts.main') --}}

@section('title', 'New Job')

<head>
@include('layouts.head-css')
<link href="https://harvesthq.github.io/chosen/chosen.css" rel="stylesheet" type="text/css" />

    @include('layouts.title-meta', ['title' => 'Realtor&#174;'])
  
</head>
<body>
    
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layouts.menu')

        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @include('layouts.page-title', ['pagetitle' => 'Admin', 'title' =>'profile' ])



                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <form class="tablelist-form" id="tablelist" autocomplete="off" method="post"
                                    action="{{ route('profile.personaledit') }}" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <div class="card-header">
                                       
                                    {{-- </div> --}}
                                    <div class="card-body">
                                        <div class="row g-4">
                                            <input type="hidden" id="id-field" name="id-field"
                                                value="{{$agent->id}}" />
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_first-field" class="form-label">First Name<span
                                                            style="color: red">*</span></label>
                                                    <input type="text" id="agent_first-field" name="agent_first"
                                                        class="form-control" placeholder="First Name"
                                                        value="{{ old('agent_first', explode(' ', $agent->name)[0]) }}"
                                                        required />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_last-field" class="form-label">Last Name<span
                                                            style="color: red">*</span></label>
                                                    <input type="text" id="agent_last-field" name="agent_last"
                                                        class="form-control" placeholder="Last Name"
                                                        value="{{ old('agent_last', implode(' ', array_slice(explode(' ', $agent->name), 1))) }}"
                                                        required />
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_email-field" class="form-label">Email<span
                                                            style="color: red">*</span></label>
                                                    <input type="email" id="agent_email-field" name="agent_email"
                                                        class="form-control" maxlength="30" placeholder="Email"
                                                        value="{{ old('agent_email', $agent->email) }}" autocomplete="off" required />
                                                    <small id="email-error" class="text-danger"
                                                        style="display: none;">This email is already
                                                        taken.</small>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_profile" class="form-label">Realtor Picture<span
                                                            style="color: red">*</span></label>
                                                    <br><span id="file-name-element" class="file-name"></span>
                                                    <input class="form-control" id="agent_profile_edit"
                                                        name="agent_profile" type="file"
                                                        accept="image/png, image/gif, image/jpeg">
                                                        <input type="hidden" value="{{ asset($agent->profile_picture) }}"
                                                                        id="old_image" name="old_image">
                                                    @if ($agent->profile_picture)
                                                        <img src="{{ asset($agent->profile_picture) }}"
                                                            alt="Agent Picture" id="agent_image_display" width="100">
                                                    @else
                                                        <p>No picture available</p>
                                                    @endif
                                                   
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="oldpasswordInput" class="form-label">Old Password</label>
                                                    <input type="password" value="" class="form-control @error('old_password') is-invalid @enderror" id="oldpasswordInput" placeholder="Enter current password" name="old_password" autocomplete="new-password">
                                                    @error('old_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                          
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="newpasswordInput" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" name="new_password" id="newpasswordInput" placeholder="Enter new password">
                                                </div>
                                            </div>
                                        
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="confirmpasswordInput" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" id="confirmpasswordInput" placeholder="Confirm password">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn submitbtn" id="add-btn"
                                                onclick="validateAndSubmitForm()">Submit</button>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>
    <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    @include('layouts.footer')
    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    @include('layouts.customizer')

    @include('layouts.vendor-scripts')
<style>
      .chosen-container-multi {
        width: 100% !important;
    }

    .chosen-container-multi .chosen-choices {
        height: 38px;
        display: flex;
        align-items: center;
        border-radius: var(--vz-border-radius);
    }
    
    .submitbtn{
        background-color: #07579F;
        color: #fff;
    }
    .submitbtn:hover{
        background-color: #07579F !important;
        color: #fff;
    }
    .submitbtn:active{
        background-color: #07579F !important;
        color: #fff !important;
    }
    
     .btn-success,.btn-success:hover,.btn-success:active {
    background-color: #07579F;  
} 
.btn:disabled, fieldset:disabled .btn {
    color: #fff;
    pointer-events: none;
    background-color: #07579F !important;
    opacity: var(--vz-btn-disabled-opacity);
}
</style>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>

    <script>
        var isEditing = false;
        $('#agent_profile_edit').change(function() {

var file = this.files[0];
if (file) {

    var reader = new FileReader();
    reader.onload = function(e) {

        $('#agent_image_display').attr('src', e.target.result);
    }
    reader.readAsDataURL(file);
}
});
        function validateAndSubmitForm() {

            const form = document.querySelector('.tablelist-form');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                isEditing = true;
                const firstName = document.getElementById('agent_first-field').value.trim();
                const lastName = document.getElementById('agent_last-field').value.trim();
                const email = document.getElementById('agent_email-field').value.trim();
                const oldpass = document.getElementById('oldpasswordInput').value.trim();
                const newpass = document.getElementById('newpasswordInput').value.trim();
                const confirmpass = document.getElementById('confirmpasswordInput').value.trim();
                
                const profilePictureInput = document.getElementById('agent_profile_edit');
                const oldimage = document.getElementById('old_image').value.trim();
                const profilePicture = profilePictureInput.files[0];
              
                if (firstName === '' || lastName === '' || email === ''  ) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill all the required fields.'
                    });
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a valid email address.'
                    });
                    return;
                }
               
                if (!oldimage && !profilePicture) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a profile picture.'
                    });
                    return;
                }

                if (!oldimage && !['image/png', 'image/jpeg'].includes(profilePicture?.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a valid image file (PNG or JPEG).'
                    });
                    return;
                }

                if (profilePicture?.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Profile picture size should be less than 2MB.'
                    });
                    return;
                }
             
                const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z\d@$!%*?&]{8,}$/;
        if (!passwordRegex.test(newpass) && newpass != '') {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Password should be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one number.'
            });
            return;
        }

        if (newpass !== confirmpass) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'New password and confirm password do not match.'
            });
            return;
        }
        const submitButton = document.getElementById('add-btn');
                submitButton.disabled = true;
                form.submit();   
            });
        };

        function validateAlphabets(event) {
            const input = event.target;
            const regex = /^[a-zA-Z\s]*$/;
            const key = event.key;

            if (!regex.test(key) && key !== 'Backspace' || input.value.length >= 40) {
                event.preventDefault();
            }
        }

        document.getElementById("agent_first-field").addEventListener("keypress", validateAlphabets);
        document.getElementById("agent_last-field").addEventListener("keypress", validateAlphabets);

        $('#agent_email-field').on('input', function() {
                if (isEditing) {
                    $('#email-error').hide();
                    $('#add-btn').prop('disabled', false);
                    return;
                }
                var email = $(this).val();
                var submitBtn = $('#add-btn');

                if (email) {
                    $.ajax({
                        url: '/check-email',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            email: email
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.exists) {
                                $('#email-error').show();
                                submitBtn.prop('disabled', true);
                            } else {
                                $('#email-error').hide();
                                submitBtn.prop('disabled', false);
                            }
                        }
                    });
                } else {
                    $('#email-error').hide();
                    submitBtn.prop('disabled', true);
                }



            });
        
    </script>
</body>

</html>
