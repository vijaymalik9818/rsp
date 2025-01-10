@include('layouts.session')
@include('layouts.main')

<head>

    @include('layouts.title-meta', ['title' => 'Realtor&#174;'])
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://harvesthq.github.io/chosen/chosen.css" rel="stylesheet" type="text/css" />
    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .custom-modal-dialog {
        max-width: 800px;
        width: 100%;
    }
    .btn-success {
    background-color: #07579F;
}
.nav-success.nav-tabs-custom .nav-link.active {
    color: #07579F;
    background-color: var(--vz-secondary-bg);
}

.nav-success.nav-tabs-custom .nav-link.active::after {
    background-color: #07579F;
}

.text-primary {
    
    color: #07579F !important;
}

.pagination .page-item .active{
        background-color: #07579F;
    }

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #07579F;
    border-color: #07579F;
}
.page-link {
    color: #07579F;
  }



div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #07579F;
    color: #fff;
    font-size: 1em;
}
    .pagination {
        float: right;
        margin-top: 20px;
    }

    .chosen-container-multi {
        width: 100% !important;
    }

    .chosen-container-multi .chosen-choices {
        height: 38px;
        display: flex;
        align-items: center;
        border-radius: var(--vz-border-radius);
    }
    .btn-success:hover {
    background-color: #07579F !important;
}
.btn-danger{
    background-color: #c43029;
}
.close-btn, .close-btn:hover{
    color: #07579F !important;
}
.close-btn:active{
    background-color: white !important;
    color: #07579F !important;
}
.btn:disabled, fieldset:disabled .btn {
    color: #fff;
    pointer-events: none;
    background-color: #07579F !important;
    opacity: var(--vz-btn-disabled-opacity);
}

</style>

<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'Staff'])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="leadsList">
                                <div class="card-header border-0">

                                    <div class="row g-4 align-items-center">
                                        <div style="position: relative" class="col-sm-3">
                                            <div class="search-box">
                                                <input type="text" id="userSearch" name="userSearch"
                                                    class="form-control search" placeholder="Search for...">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                            <div id="userSuggestions"
                                                style="position: absolute; top: 67%; left: 0; max-height: 150px; overflow-y: auto; z-index: 100; width: 290px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">
                                            </div>
                                        </div>
                                        <div class="col-sm-auto ms-auto">
                                            <div class="hstack gap-2">
                                                <button type="button" class="btn  btn-success add-btn"
                                                    data-bs-toggle="modal" id="create-staff-btn"
                                                    data-bs-target="#showModalforstaff"><i
                                                        class="ri-add-line align-bottom me-1"></i> Add Staff</button>


                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="card-body">
                                    <div>
                                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All"
                                                    href="#home1" role="tab" aria-selected="true">
                                                    <i class="ri-store-2-fill me-1 align-bottom"></i> All Staff
                                                </a>
                                            </li>

                                        </ul>
                                        <div class="table-responsive table-card">
                                            <table class="table align-middle table-sortable" id="agentTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort" data-sort="id">S.no</th>
                                                        <th class="sort" data-sort="name">Name</th>
                                                        <th class="sort" data-sort="email">Email</th>
                                                        <th class="sort" data-sort="phone">Phone</th>

                                                        <th class="sort" data-sort="position">Job Title</th>

                                                        <th data-sort="action">Action</th>
                                                    </tr>

                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @if (sizeof($agents) == 0)
                                                        <tr>
                                                            <td colspan="8">No data available</td>
                                                        </tr>
                                                    @else
                                                    @endif
                                                </tbody>
                                            </table>
                                            <div class="noresult" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                    <p class="text-muted mb-0">We've searched more than
                                                        {{ $total_agent }}+ staff We
                                                        did not find any staff for you search.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pagination_wrap"></div>
                                    </div>

                                    <div class="modal fade" id="showModalforstaff" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light p-3">
                                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close" id="close-modal"></button>
                                                </div>

                                                <form class="tablelist-form-staff" id="tablelist" autocomplete="off"
                                                    method="post" action="{{ route('store-agent') }}"
                                                    enctype="multipart/form-data" novalidate>
                                                    @csrf
                                                    <div class="modal-body">
                                                        <input type="hidden" id="id-field-staff"
                                                            name="id-field-staff" />

                                                        <div class="text-center">

                                                        </div>

                                                        <div class="row g-3">
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_name-field"
                                                                        class="form-label">First Name<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="staff_first-field"
                                                                        name="agent_first" class="form-control"
                                                                        placeholder="First Name" required />

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_name-field"
                                                                        class="form-label">Last Name<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="staff_last-field"
                                                                        name="agent_last" class="form-control"
                                                                        placeholder="Last Name" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_email-field"
                                                                        class="form-label">Staff Email</label>
                                                                    <input type="email" id="staff_email-field"
                                                                        name="agent_email" class="form-control"
                                                                        maxlength="30" placeholder="Email" required />
                                                                    <small id="email-error" class="text-danger"
                                                                        style="display: none;">This email is already
                                                                        taken.</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_position-field"
                                                                        class="form-label">Job Title<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="staff_position-field"
                                                                        name="agent_position" class="form-control"
                                                                        maxlength="40" placeholder="Position"
                                                                        required />
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_profile"
                                                                        class="form-label">Staff Picture (1)<span
                                                                            style="color: red">*</span></label>
                                                                    <br><span id="file-name-element"
                                                                        class="file-name"></span>
                                                                    <input class="form-control" value=""
                                                                        id="staff_profile_edit" name="agent_profile"
                                                                        type="file"
                                                                        accept="image/png, image/gif, image/jpeg">
                                                                    <input type="hidden" value=""
                                                                        id="old_staff_image" name="old_image">
                                                                    <img id="staff_image_display" width="100"
                                                                        alt="Agent Picture" style="display: none;">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="staff_profile2"
                                                                        class="form-label">Staff Picture (2)<span
                                                                            style="color: red">*</span></label>
                                                                    <br><span id="file-name-element"
                                                                        class="file-name"></span>
                                                                    <input class="form-control" value=""
                                                                        id="staff_profile_edit2" name="agent_profile2"
                                                                        type="file"
                                                                        accept="image/png, image/gif, image/jpeg">
                                                                    <input type="hidden" value=""
                                                                        id="old_staff_image2" name="old_image2">
                                                                    <img id="staff_image_display2" width="100"
                                                                        alt="Agent Picture" style="display: none;">
                                                                </div>
                                                            </div>



                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="staff_phone-field"
                                                                        class="form-label">Staff Phone</label>
                                                                    <input type="text" id="staff_phone-field"
                                                                        pattern="[0-9]{10}"
                                                                        title="Phone number should contain exactly 10 digits"
                                                                        name="agent_phone" class="form-control"
                                                                        maxlength="10" placeholder="Phone Number"
                                                                        required />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="staff_description"
                                                                        class="form-label">Staff Description<span
                                                                            style="color: red">*</span></label>
                                                                    <textarea id="staff_description" name="agent_description" class="form-control" placeholder="Description"
                                                                        rows="4" required></textarea>
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="facebook-field-staff"
                                                                        class="form-label">Facebook</label>
                                                                    <input type="text" id="facebook-field"
                                                                        name="facebook" class="form-control"
                                                                        placeholder="Facebook" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="linkedin-field-staff"
                                                                        class="form-label">LinkedIn</label>
                                                                    <input type="text" id="linkedin-field"
                                                                        name="linkedin" class="form-control"
                                                                        placeholder="LinkedIn" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="twitter-field-staff"
                                                                        class="form-label">Twitter</label>
                                                                    <input type="text" id="twitter-field"
                                                                        name="twitter" class="form-control"
                                                                        placeholder="Twitter" />
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="instagram-field-staff"
                                                                        class="form-label">Instagram</label>
                                                                    <input type="text" id="instagram-field"
                                                                        name="instagram" class="form-control"
                                                                        placeholder="Instagram" />
                                                                </div>
                                                            </div>
                                                            <input type="hidden" id="staff_roll" name="agent_role"
                                                                class="form-control" value="2" required>


                                                        </div>

                                                    </div>
                                                      <div class="modal-footer">
                                                        <div class="hstack gap-2 justify-content-end">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success submitbtn"
                                                                id="add-btn-staff"
                                                                onclick="staffvalidateAndSubmitForm()">Submit</button>
                                                            <div id="loader" class="loader"></div>
                                                        </div>
                                                    </div>

                                                </form>



                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1"
                                        aria-labelledby="deleteRecordLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close" id="btn-close"></button>
                                                </div>
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                                        style="width:90px;height:90px"></lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4 class="fs-semibold">You are about to delete a lead ?</h4>
                                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your lead will
                                                            remove all of your information from our database.</p>
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                              <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none close-btn"
                                                                id="deleteRecord-close" data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Close</button>
                                                            <button class="btn btn-danger deleterecords"
                                                                id="deleterecord">Yes,
                                                                Delete It!!</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample"
                                        aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header bg-light">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Leads Filters</h5>
                                            <button type="button" class="btn-close text-reset"
                                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>

                                        <form action="" method="post"
                                            class="d-flex flex-column justify-content-end h-100">
                                            @csrf
                                            <div class="offcanvas-body">

                                                <div class="mb-4">
                                                    <label for="country-select"
                                                        class="form-label text-muted text-uppercase fw-bold fs-13 mb-3">Country</label>
                                                    <select class="form-control" data-choices
                                                        data-choices-multiple-remove="true" name="countries[]"
                                                        id="country-select" multiple>
                                                        <option value="">Select country</option>
                                                        <option value="Argentina">Argentina</option>
                                                        <option value="Belgium">Belgium</option>
                                                        <option value="Brazil" selected>Brazil</option>
                                                        <option value="Colombia">Colombia</option>
                                                        <option value="Denmark">Denmark</option>

                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="status-select"
                                                        class="form-label text-muted text-uppercase fw-bold fs-13 mb-3">Status</label>
                                                    <div class="row g-2">

                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="leadscore"
                                                        class="form-label text-muted text-uppercase fw-bold fs-13 mb-3">Lead
                                                        Score</label>
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-lg">
                                                            <input type="number" class="form-control"
                                                                id="leadscore_from" name="leadscore_from"
                                                                placeholder="0">
                                                        </div>
                                                        <div class="col-lg-auto">
                                                            To
                                                        </div>
                                                        <div class="col-lg">
                                                            <input type="number" class="form-control"
                                                                id="leadscore_to" name="leadscore_to"
                                                                placeholder="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="leads-tags"
                                                        class="form-label text-muted text-uppercase fw-bold fs-13 mb-3">Tags</label>
                                                    <div class="row g-3">

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                                                <button class="btn btn-light w-100" type="reset">Clear
                                                    Filter</button>
                                                <button type="submit" class="btn btn-success w-100">Apply
                                                    Filters</button>
                                            </div>

                                        </form>
                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>


                </div>
            </div>

            @include ('layouts.footer');
        </div>

    </div>



    @include ('layouts.customizer')

    @include ('layouts.vendor-scripts')
    <style>
        .form-check {
            margin: 10px;
        }

        #userSuggestions ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #userSuggestions ul li {
            margin-bottom: 10px;
            background-color: white;
            padding: 5px;
            cursor: pointer;
        }

        #userSuggestions ul li:hover {
            background-color: #f0f0f0;
        }

        .category-heading {
            pointer-events: none;
        }

        .custom-anchor {
            color: black !important;
        }

        .loader {
            border: 8px solid #f3f3f3;
            /* Light grey */
            border-top: 8px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: auto;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script src="assets/libs/list.pagination.js/list.pagination.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>

    <script>
        $(".chosen-select").chosen();
    </script>

    <script>
        $(document).ready(function() {
            const loader = document.getElementById('loader');
            loader.style.display = 'none';

            function clearEditModalFields() {
                document.getElementById('staff_image_display').style.display = 'none';
                document.getElementById('staff_image_display2').style.display = 'none';

                $('#id-field-staff').val('');
                $('#agent_first-field').val('');
                $('#staff_first-field').val('');
                $('#agent_last-field').val('');
                $('#staff_last-field').val('');

                $('#old_staff_image').val('');
                $('#old_staff_image2').val('');

                $('#staff_email-field').val('');
                $('#staff_position-field').val('');

                $('#staff_description').val('');

                $('#file-name-element').text('');

                $('#staff_phone-field').val('');


                $('#facebook-field').val('');
                $('#linkedin-field').val('');
                $('#twitter-field').val('');
                $('#youtube-field').val('');
                $('#instagram-field').val('');

            }


            $('#showModalforstaff').on('hidden.bs.modal', function() {

                $('#staff_profile_edit').val('');
                $('#staff_profile_edit2').val('');

            });
            var isEditing = false;




            $(document).ready(function() {

                $('#staff_profile_edit').change(function() {

                    var file = this.files[0];
                    if (file) {

                        var reader = new FileReader();
                        reader.onload = function(e) {

                            $('#staff_image_display').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(file);
                    }
                });
                $('#staff_profile_edit2').change(function() {

                    var file = this.files[0];
                    if (file) {

                        var reader = new FileReader();
                        reader.onload = function(e) {

                            $('#staff_image_display2').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(file);
                    }
                });


                $(document).on('click', '.remove-item-btn', function(event) {
                    var agentId = $(this).data('agent-id');
                    $('#deleteRecordModal').data('agent-id', agentId);
                });

                $(document).on('click', '.deleterecords', function(e) {

                    e.preventDefault();
                    var agentId = $('#deleteRecordModal').data('agent-id');
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '/delete-agent/' + agentId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },

                        success: function(response) {
                            var page = $('.page-item.active').text();
                            $('#agent-' + agentId).remove();
                            $('#deleteRecordModal').modal('hide');
                            nextPageData("getagents?page=" + page);

                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting agent:', error);
                            alert('Error occurred while deleting agent.');
                        }
                    });
                });

                function resetFormState() {
                    $('#email-error').hide();
                    $('#add-btn').prop('disabled', false);
                }


                $(document).on('click', '.edit-item-btn', function(event) {
                    isEditing = true;
                    document.getElementById('staff_image_display').style.display = 'inline-block';
                    document.getElementById('staff_image_display2').style.display = 'inline-block';
                    resetFormState();
                    var editModal = $('#showModalforstaff');
                    var editForm = $('.tablelist-form-staff');
                    var agentId = $(this).data('agent-id');
                    $('#edit-agent-id').val(agentId);
                    $.ajax({
                        url: '/agents/' + agentId,
                        type: 'GET',
                        success: function(response) {
                            console.log('response: ', response);


                            var fullName = response.agent.name;
                            var nameParts = fullName.split(' ');
                            var firstName = nameParts[0];
                            var lastName = nameParts.slice(1).join(' ');
                            var agentImage = response.agent.profile_picture;
                            var agentImages = response.agent.other_profile_picture;





                            function formatPhoneNumber(phoneNumberString) {
                                var cleaned = ('' + phoneNumberString).replace(/\D/g,
                                    '');
                                var match = cleaned.match(/^(\d{2})(\d{3})(\d{4})$/);
                                if (match) {
                                    return match[1] + '-' + match[2] + '-' + match[3];
                                }
                                match = cleaned.match(/^(\d{4})(\d{4})$/);
                                if (match) {
                                    return match[1] + '-' + match[2];
                                }
                                match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
                                if (match) {
                                    return '(' + match[1] + ') ' + match[2] + '-' +
                                        match[3];
                                }
                                return phoneNumberString;
                            }
if(response.agent.phone != 0){
                            var formattedPhone = formatPhoneNumber(response.agent
                                .phone);
}else{
    var formattedPhone = '';
}

                            editForm.find('#staff_first-field').val(firstName);
                            editForm.find('#staff_last-field').val(lastName);
                            editForm.find('#staff_email-field').val(response.agent
                                .email);
                            editForm.find('#staff_phone-field').val(formattedPhone);

                            editForm.find('#staff_image_display').attr('src',
                                agentImage);

                            editForm.find("#old_staff_image").val(agentImage);
                            editForm.find('#staff_image_display2').attr('src',
                                agentImages);

                            editForm.find("#old_staff_image2").val(agentImages);

                            editForm.find('#staff_position-field').val(response.agent
                                .position);


                            editForm.find('#staff_description').val(response.agent
                                .description);
                            editForm.find('#staff_position').text(response
                                .agent.position);

                            editForm.find('#linkedin-field').val(response.agent
                                .linkedin);
                            editForm.find('#facebook-field').val(response.agent
                                .facebook);
                            editForm.find('#youtube-field').val(response.agent.youtube);
                            editForm.find('#twitter-field').val(response.agent.twitter);
                            editForm.find('#instagram-field').val(response.agent
                                .instagram);


                            editForm.find('#id-field-staff').val(response.agent.id);


                            $('#showModalforstaff').modal('show');
                        },
                        error: function(error) {
                            console.error('Error fetching agent data:', error);
                        }
                    });

                });


            });

            $('#create-staff-btn').click(function() {
                clearEditModalFields();
            });


        });
        ['staff_phone-field'].forEach(fieldId => {
            document.getElementById(fieldId).addEventListener("input", function(event) {
                let phoneInput = event.target.value.replace(/\D/g, '').slice(0, 10);
                event.target.value = phoneInput.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            });
        });


        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });
        $(document).on('click', '.searchs', function() {
            var query = $(this).data('name') || $(this).data('email') || $(this).data(
                'phone');
            $('#userSearch').val(query);
            performSearch(query);
        });
        $('#userSearch').on('keypress', function(event) {
            if (event.which === 13) {
                var query = $(this).val();
                performSearch(query);
                $('#userSuggestions').hide();
            }
        });
        $('#userSearch').keyup(function(event) {
            var query = $(this).val();

            if (query !== '' && event.which !== 13) {
    $.ajax({
        url: "{{ route('getautosuggestionstaff') }}",
        method: "GET",
        data: {
            term: query,
        },
        success: function(data) {

            var nameSuggestions = new Set();
            var emailSuggestions = new Set();
            var phoneSuggestions = new Set();

            $.each(data, function(key, user) {
                if (user.name && user.name.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
                    nameSuggestions.add(user.name);
                }
                if (user.email && user.email.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
                    emailSuggestions.add(user.email);
                }
                if (user.phone && user.phone.toString().indexOf(query) !== -1) {
                    phoneSuggestions.add(user.phone);
                }
            });

            var allSuggestions = '';

            if (nameSuggestions.size > 0) {
                allSuggestions += '<div class="category"><span class="category-heading"><strong>Name</strong></span><ul>';
                nameSuggestions.forEach(function(name) {
                    allSuggestions += '<li class="searchs" data-name="' + name + '">' + name + '</li>';
                });
                allSuggestions += '</ul></div>';
            }
            if (emailSuggestions.size > 0) {
                allSuggestions += '<div class="category"><span class="category-heading"><strong>Email</strong></span><ul>';
                emailSuggestions.forEach(function(email) {
                    allSuggestions += '<li class="searchs" data-email="' + email + '">' + email + '</li>';
                });
                allSuggestions += '</ul></div>';
            }
            if (phoneSuggestions.size > 0) {
                allSuggestions += '<div class="category"><span class="category-heading"><strong>Phone</strong></span><ul>';
                phoneSuggestions.forEach(function(phone) {
                    allSuggestions += '<li class="searchs" data-phone="' + phone + '">' + phone + '</li>';
                });
                allSuggestions += '</ul></div>';
            }

            if (allSuggestions !== '') {
                $('#userSuggestions').fadeIn();
                $('#userSuggestions').html(allSuggestions);
            } else {
                $('#userSuggestions').fadeOut();
            }

        }
    });
}


        });

        function performSearch(query) {

            $.ajax({
                url: "{{ route('getautoquerystaff') }}",
                method: "GET",
                data: {
                    name: query,
                    email: query,
                    phone: query

                },
                success: function(response) {
                    if (response.data.length === 0) {
                        $('#pagination_wrap').hide();
                        $('.noresult').show();
                        $('#agentTable tbody').empty();
                        return;
                    }
                    $('.noresult').hide();
                    var html = '';
                    $.each(response.data, function(index, agent) {
                        var formattedLanguage = '';
                        if (agent.language) {
                            formattedLanguage = agent.language.split(',').join(', ');
                        }
                        html += '<tr>';
                        html += '<td class="id">' + agent.ids + '</td>';
                        html += '<td>';
                        html += '<div class="d-flex align-items-center">';
                        html += '<div class="flex-shrink-0">';
                        html += '<img src="' + agent.profile_picture +
                            '" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';

                        html += '<span class="text-primary d-inline-block custom-anchor">' + agent
                            .name + '</span>';

                        html += '</div>';
                        html += '</td>';

                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.email + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        var phoneHtml = '';
                        if (agent.phone !== 0) {
                            phoneHtml = '<td class="phone"> (' + String(agent.phone).substring(0,
                                    3) +
                                ') ' + String(agent.phone).substring(3, 6) + '-' +
                                String(agent.phone).substring(6) + '</td>';
                        } else {
                            phoneHtml =
                                '<td class="phone"> </td>';
                        }
                        html += phoneHtml;


                        html += '<td class="position">' + (agent.position !== null ?
                            agent.position : '') + '</td>';

                        html += '<td>';
                        html += '<ul class="list-inline hstack gap-2 mb-0">';
                        '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                        html +=
                            '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                            agent.id +
                            '"><i class="ri-pencil-fill align-bottom text-primary"></i></a>';
                        html += '</li>';
                        html +=
                            '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">';
                        html += '<a class="remove-item-btn" data-agent-id="' +
                            agent.id +
                            '" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-fill align-bottom text-danger"></i></a>';
                        html += '</li>';

                        html += '</ul>';
                        html += '</td>';
                        html += '</tr>';

                    });
                    $('#agentTable tbody').html(html);
                    createPaginationLinks(response);

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);

                }
            });

        }
        $(document).on('click', '.searchs', function() {
            var query = $(this).data('name') || $(this).data('email') || $(this).data(
                'phone');
            $('#userSearch').val(query);
            performSearch(query);
        });
        $('#userSearch').on('keypress', function(event) {
            if (event.which === 13) {
                var query = $(this).val();
                performSearch(query);
                $('#userSuggestions').hide();
            }
        });



        $(document).on('click', 'li', function() {
            $('#userSuggestions').fadeOut();
        });

        function staffvalidateAndSubmitForm() {
            const form = document.querySelector('.tablelist-form-staff');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const loader = document.getElementById('loader');
                loader.style.display = 'block';
                const firstName = document.getElementById('staff_first-field').value.trim();
                const lastName = document.getElementById('staff_last-field').value.trim();
                const email = document.getElementById('staff_email-field').value.trim();
                const position = document.getElementById('staff_position-field').value.trim();
                const profilePictureInput = document.getElementById('staff_profile_edit');
                const profilePictureInputs = document.getElementById('staff_profile_edit2');
                const oldimage = document.getElementById('old_staff_image').value.trim();
                const oldimages = document.getElementById('old_staff_image2').value.trim();
                const profilePicture = profilePictureInput.files[0];
                const profilePictures = profilePictureInputs.files[0];
                const description = document.getElementById('staff_description').value.trim();
                const phone = document.getElementById('staff_phone-field').value.trim();

                if (firstName === '' || lastName === '' || position === '' ||
                    description === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill all the required fields.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email) && email != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a valid email address.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                if (!oldimage && !profilePicture) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a profile picture.'
                    });
                    loader.style.display = 'none';
                    return;
                }
                if (!oldimages && !profilePictures) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a profile picture.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                if (!oldimage && !['image/png', 'image/jpeg'].includes(profilePicture?.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a valid image file (PNG or JPEG).'
                    });
                    loader.style.display = 'none';
                    return;
                }
                if (!oldimages && !['image/png', 'image/jpeg'].includes(profilePictures?.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a valid image file (PNG or JPEG).'
                    });
                    loader.style.display = 'none';
                    return;
                }

                if (profilePicture?.size > 20 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Profile picture size should be less than 20MB.'
                    });
                    loader.style.display = 'none';
                    return;
                }
                if (profilePictures?.size > 20 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Profile picture size should be less than 20MB.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                const submitButton = document.getElementById('add-btn-staff');
                submitButton.disabled = true;
                setTimeout(function() {
            // Hide loader
            loader.style.display = 'none';

            // Show SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Thank You!',
                text: 'Your form has been submitted successfully.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    form.submit();
                }
            });
        }, 1000); // Change 2000 to actual delay time or remove if not needed

    });
    form.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        loader.style.display = 'none';
    });
}

        function validateAlphabets(event) {
            const input = event.target;
            const regex = /^[a-zA-Z\s]*$/;
            const key = event.key;

            if (!regex.test(key) && key !== 'Backspace' || input.value.length >= 40) {
                event.preventDefault();
            }
        }

        document.getElementById("staff_first-field").addEventListener("keypress", validateAlphabets);
        document.getElementById("staff_last-field").addEventListener("keypress", validateAlphabets);




        window.load = nextPageData("getagents")

        function nextPageData(nextPageUrl) {



            $.ajax({
                url: nextPageUrl,
                method: 'GET',

                success: function(response) {
                    console.log('response: ', response);
                    var html = '';
                    // var idCounter = 1; 

                    if (response.agent.data.length === 0) {
                        $('.noresult').show();
                        $('#agentTable tbody').empty();
                        return;
                    }
                    $('.noresult').hide();
                    $.each(response.agent.data, function(index, agent) {


                        html += '<tr>';
                        html += '<td class="id">' + agent.ids + '</td>';
                        html += '<td>';
                        html += '<div class="d-flex align-items-center">';
                        html += '<div class="flex-shrink-0">';
                        if (agent.profile_picture) {
                            html += '<img src="' + agent.profile_picture +
                                '" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                        } else {

                            html +=
                                '<img src="assets/images/No-Image-Placeholder.png" alt="Default Image" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                        }
                        html += '<span class="text-primary d-inline-block custom-anchor">' + agent
                            .name + '</span>';


                        html += '</div>';
                        html += '</div>';
                        html += '</td>';

                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.email + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        var phoneHtml = '';
                        if (agent.phone !== 0) {
                            phoneHtml = '<td class="phone"> (' + String(agent.phone).substring(0,
                                    3) +
                                ') ' + String(agent.phone).substring(3, 6) + '-' +
                                String(agent.phone).substring(6) + '</td>';
                        } else {
                            phoneHtml =
                                '<td class="phone"> </td>';
                        }
                        html += phoneHtml;

                        html += '<td class="position">' + (agent.position !== null ?
                            agent.position : '') + '</td>';

                        html += '<td>';
                        html += '<ul class="list-inline hstack gap-2 mb-0">';
                        '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                        html +=
                            '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                            agent.id +
                            '"><i class="ri-pencil-fill align-bottom text-primary"></i></a>';
                        html += '</li>';
                        html +=
                            '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">';
                        html += '<a class="remove-item-btn" data-agent-id="' +
                            agent.id +
                            '" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-fill align-bottom text-danger"></i></a>';
                        html += '</li>';

                        html += '</ul>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);
                    createPaginationLinks(response.agent);

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
       
        function createPaginationLinks(response) {
            console.log('response: ', response);

            var paginationHtml = '<ul class="pagination">';
            $.each(response.links, function(index, link) {
                if (link.url) {
                    paginationHtml +=
                        '<li class="page-item' + (link.active ? ' active' :
                            '') +
                        '"><a class="page-link" onClick="nextPageData(\'' +
                        link.url + '\', \'' + status + '\')" href="javascript:void(0)">' +
                        link.label + '</a></li>';;
                } else {
                    paginationHtml +=
                        '<li class="page-item disabled"><span class="page-link">' +
                        link.label + '</span></li>';
                }
            });
            paginationHtml += '</ul>';
            $('#pagination_wrap').html(paginationHtml);
            $('#agentTable th[data-sort="action"]').show();
        }

        $(document).ready(function() {

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
        });

        $('#userSearch').on('input', function(e) {
            var inputValue = $(this).val();

            if (inputValue == '') {
                nextPageData('getagents');
            }
        })


        function sortTableByColumn(table, column, asc = true) {
            const dirModifier = asc ? 1 : -1;
            const tBody = table.tBodies[0];
            const rows = Array.from(tBody.querySelectorAll("tr"));
            console.log('rows: ', rows);

            const sortedRows = rows.sort((a, b) => {
                const aColText = a.querySelector(`td:nth-child(${ column + 1 })`).textContent.trim();
                const bColText = b.querySelector(`td:nth-child(${ column + 1})`).textContent.trim();

                return aColText.localeCompare(bColText) * dirModifier;
            });

            while (tBody.firstChild) {
                tBody.removeChild(tBody.firstChild);
            }

            tBody.append(...sortedRows);

            table.querySelectorAll("th").forEach(th => th.classList.remove("th-sort-asc", "th-sort-desc"));
            table.querySelector(`th:nth-child(${ column + 1 })`).classList.toggle("th-sort-asc", asc);
            table.querySelector(`th:nth-child(${ column + 1 })`).classList.toggle("th-sort-desc", !asc);
        }

        document.querySelectorAll("#agentTable th").forEach((headerCell, index) => {
            headerCell.addEventListener("click", () => {
                const tableElement = headerCell.closest("table");
                const currentIsAscending = headerCell.classList.contains("th-sort-asc");
                // index
                console.log('index: ', index);
                sortTableByColumn(tableElement, index, !currentIsAscending);
            });
        });
    </script>

</body>

</html>