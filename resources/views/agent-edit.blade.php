@include('layouts.session')

@include('layouts.main')

{{-- @section('title', 'New Job') --}}
<head>
    @include('layouts.title-meta', ['title' => $agent->name])
    <link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://harvesthq.github.io/chosen/chosen.css" rel="stylesheet" type="text/css" />
    @include('layouts.head-css')
</head>

    <?php
    
    $agentPhone = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $agent->phone);
    $agentOffice = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $agent->office_no);
    $agentFax = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $agent->fax_no);
    ?>
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layouts.menu')

        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @include('layouts.page-title', ['pagetitle' => 'Agent', 'title' => $agent->name])



                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <form class="tablelist-form" id="tablelist" autocomplete="off" method="post"
                                    action="{{ route('store-agent') }}" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Edit REALTOR&#174;</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-4">
                                            <input type="hidden" id="id-field" name="id-field"
                                                value="{{ old('id-field', $agent->id) }}" />
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

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_email-field" class="form-label">Agent Email<span
                                                            style="color: red">*</span></label>
                                                    <input type="email" id="agent_email-field" name="agent_email"
                                                        class="form-control" maxlength="30" placeholder="Email"
                                                        value="{{ old('agent_email', $agent->email) }}" required />
                                                    <small id="email-error" class="text-danger"
                                                        style="display: none;">This email is already
                                                        taken.</small>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_position-field" class="form-label">Job Title<span
                                                            style="color: red">*</span></label>
                                                    <div class="dropdown" >
                                                        <button class="btn btn-light dropdown-toggle" type="button"
                                                            id="agent_position-dropdown" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            @if ($agent->position)
                                                                {{ $agent->position }}
                                                            @else
                                                                Select Position
                                                            @endif
                                                        </button>
                                                        <input type="hidden" id="agent_position-field"
                                                            name="agent_position" class="form-control"
                                                            value="{{ $agent->position }}" required>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="agent_position-dropdown">
                                                            @foreach ($positions as $position)
                                                                <button class="dropdown-item" type="button"
                                                                    onclick="selectPosition('{{ $position }}')">
                                                                    {{ $position }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_profile" class="form-label">REALTOR&#174; Picture<span
                                                            style="color: red">*</span></label>
                                                    <br><span id="file-name-element" class="file-name"></span>
                                                    <input class="form-control" id="agent_profile_edit"
                                                        name="agent_profile" type="file"
                                                        accept="image/png, image/gif, image/jpeg">
                                                        <input type="hidden" value="{{($agent->profile_picture)}}"
                                                                        id="old_image" name="old_image">
                                                    @if ($agent->profile_picture)
                                                        <img src="{{ asset($agent->profile_picture) }}"
                                                            alt="Agent Picture" id="agent_image_display" width="100">
                                                    @else
                                                        <p>No picture available</p>
                                                    @endif
                                                   
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_address" class="form-label">REALTOR&#174; Address<span
                                                            style="color: red">*</span></label>
                                                    <textarea id="agent_address" name="agent_address" class="form-control" placeholder="Address" rows="2"
                                                        required>{{ old('agent_address', $agent->address) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_description" class="form-label">REALTOR&#174;
                                                        Description<span style="color: red">*</span></label>
                                                    <textarea id="agent_description" name="agent_description" maxlength="400" class="form-control"
                                                        placeholder="Description" rows="4" required>{{ old('agent_description', $agent->description) }}</textarea>
                                                </div>
                                            </div>
 

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_phone-field" class="form-label">REALTOR&#174; Phone<span
                                                            style="color: red">*</span></label>
                                                    <input type="text" id="agent_phone-field" name="agent_phone"
                                                        class="form-control" placeholder="Phone Number"
                                                        pattern="[0-9]{10}" maxlength="10"
                                                        value="{{ old('agent_phone', $agentPhone) }}" required />
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_office-field" class="form-label">REALTOR&#174; Office
                                                        Number</label>
                                                    <input type="text" id="agent_office-field" name="agent_office"
                                                        class="form-control" placeholder="Office Number"
                                                        pattern="[0-9]{10}" maxlength="10"
                                                        value="{{ old('agent_office', $agentOffice) }}" required />
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_fax-field" class="form-label">REALTOR&#174; Fax
                                                        Number</label>
                                                    <input type="text" id="agent_fax-field" name="agent_fax"
                                                        pattern="[0-9]{10}" maxlength="10" class="form-control"
                                                        placeholder="Fax Number"
                                                        value="{{ old('agent_fax', $agentFax) }}" required />
                                                </div>
                                            </div>


                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_mls-field" class="form-label">REALTOR&#174; Id</label>
                                                    <input type="text" id="agent_mls-field" name="agent_mls"
                                                        class="form-control" placeholder="REALTOR&#174; Id"
                                                        value="{{ old('agent_mls', $agent->mls_id) }}" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_logo-input" class="form-label">REALTOR&#174;
                                                        Logo</label>
                                                    <input class="form-control" id="agent_logo-input"
                                                        name="agent_logo" type="file"
                                                        accept="image/png, image/gif, image/jpeg">
                                                    @if ($agent->agent_logo)
                                                        <img src="{{ asset($agent->agent_logo) }}" alt="Agent Logo"
                                                            style="max-width: 100px;">
                                                    @else
                                                        <p>No logo available</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="agent_status" class="form-label">Status</label>
                                                    <div class="form-check form-switch">

                                                        <input type="hidden" name="status" id="status"
                                                            value="{{ $agent->status }}">

                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch" id="flexSwitchCheckDefault">

                                                        <label class="form-check-label" for="flexSwitchCheckDefault"
                                                            style="font-size: 15px" id="statusLabel">

                                                            {{ $agent->status ? 'Active' : 'Inactive' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="agent_website"
                                                        class="form-label">REALTOR&#174; Website</label>
                                                            <input type="text" id="agent_website"
                                                            name="agent_website" class="form-control"
                                                            placeholder="Website"  value="{{ old('agent_website', $agent->Website) }}" required />
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="facebook-field" class="form-label">Facebook</label>
                                                    <input type="text" id="facebook-field" name="facebook"
                                                        class="form-control" placeholder="Facebook"
                                                        value="{{ old('facebook', $agent->facebook) }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="linkedin-field" class="form-label">LinkedIn</label>
                                                    <input type="text" id="linkedin-field" name="linkedin"
                                                        class="form-control" placeholder="LinkedIn"
                                                        value="{{ old('linkedin', $agent->linkedin) }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="twitter-field" class="form-label">Twitter</label>
                                                    <input type="text" id="twitter-field" name="twitter"
                                                        class="form-control" placeholder="Twitter"
                                                        value="{{ old('twitter', $agent->twitter) }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="youtube-field" class="form-label">YouTube</label>
                                                    <input type="text" id="youtube-field" name="youtube"
                                                        class="form-control" placeholder="YouTube"
                                                        value="{{ old('youtube', $agent->youtube) }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="instagram-field" class="form-label">Instagram</label>
                                                    <input type="text" id="instagram-field" name="instagram"
                                                        class="form-control" placeholder="Instagram"
                                                        value="{{ old('instagram', $agent->instagram) }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="language-field"
                                                        class="form-label">Language</label>
                                                    <div>
                                                        <select class="form-control chosen-select"
                                                            name="choices-single-default[]"
                                                            id="language-field" multiple>
                                                            <option value="" disabled>Language
                                                                Spoken</option>
                                                            @foreach ($languages as $language)
                                                                <option
                                                                value="{{ old('language', $agent->language) }}">
                                                                    {{ $language }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @include('layouts.footer')
        </div>
    </div>

 
   

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
    
     .submitbtn, .submitbtn:hover, .submitbtn:active{
        background-color: #07579F !important;
        color: #fff !important;
    }
</style>

@include('layouts.customizer')

@include('layouts.vendor-scripts')
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>

    <script>
         $(".chosen-select").chosen();
        document.addEventListener('DOMContentLoaded', function() {
            var switchCheckbox = document.getElementById('flexSwitchCheckDefault');
            var statusLabel = document.getElementById('statusLabel');
            var hiddenInput = document.getElementById('status');

            if (hiddenInput.value == 1) {
                switchCheckbox.checked = true;
                statusLabel.textContent = 'Active';
            } else {
                switchCheckbox.checked = false;
                statusLabel.textContent = 'Inactive';
            }
            switchCheckbox.addEventListener('change', function() {
                hiddenInput.value = switchCheckbox.checked ? 1 : 0;
                statusLabel.textContent = switchCheckbox.checked ? 'Active' : 'Inactive';
            });
        });



        function validateAndSubmitForm() {
            const form = document.querySelector('.tablelist-form');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const firstName = document.getElementById('agent_first-field').value.trim();
                const lastName = document.getElementById('agent_last-field').value.trim();
                const email = document.getElementById('agent_email-field').value.trim();
                const position = document.getElementById('agent_position-field').value.trim();
                const profilePictureInput = document.getElementById('agent_profile_edit');
                const oldimage = document.getElementById('old_image').value.trim();
                const profilePicture = profilePictureInput.files[0];
                const address = document.getElementById('agent_address').value.trim();
                const description = document.getElementById('agent_description').value.trim();
                const phone = document.getElementById('agent_phone-field').value.trim();
                const status = document.getElementById('flexSwitchCheckDefault').checked ? "Active" :
                    "Inactive";
                if (firstName === '' || lastName === '' || email === '' || position === '' ||
                    address === '' || description === '' || phone === '') {
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
                if (phone.length < 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please make sure the phone number at least 10 digits long.'
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
                if (description.length > 400) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Description should be less than 400 characters.'
                    });
                    return;
                }

                const submitButton = document.getElementById('add-btn');

                const formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            const encodedAgentId = btoa('{{ $agent->id }}'.toString());
                            const url = '{{ route('agent.details', ':agentId') }}'.replace(':agentId',
                                encodedAgentId);
                            window.location.href = url;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                text: 'There was an error submitting the form. Please try again later.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: 'There was an error submitting the form. Please try again later.'
                        });
                    });
            });
        };

        validateAndSubmitForm();

        $(document).ready(function() {

            var languages = @json($languages);
            console.log(languages);
            $('#language-field').typeahead({
                source: languages
            });
        });

        function selectPosition(position) {
            document.getElementById('agent_position-field').value = position;
            document.getElementById('agent_position-dropdown').innerText = position;
        }
        ['agent_phone-field', 'agent_office-field', 'agent_fax-field'].forEach(fieldId => {
    document.getElementById(fieldId).addEventListener("input", function(event) {
        let phoneInput = event.target.value.replace(/\D/g, '').slice(0, 10);
        event.target.value = phoneInput.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    });
});


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
        document.getElementById("agent_position-field").addEventListener("keypress", validateAlphabets);
    </script>
