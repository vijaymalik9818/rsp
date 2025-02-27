@include('layouts.session')
@include('layouts.main')

<head>

    @include('layouts.title-meta', ['title' => 'REALTOR&#174;'])
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://harvesthq.github.io/chosen/chosen.css" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>

.nav-success.nav-tabs-custom .nav-link.active {
    color: #07579F;
    background-color: var(--vz-secondary-bg);
}

.nav-success.nav-tabs-custom .nav-link.active::after {
    background-color: #07579F;
}


.nav-link:focus, .nav-link:hover {
    color: #07579F;
}

.choices__list--multiple .choices__item {
    display: inline-block;
    vertical-align: initial;
    border-radius: 7px;
    padding: 2px 7px;
    font-size: 11px;
    font-weight: 400;
    margin-right: 3.75px;
    margin-bottom: 3.75px;
    margin-top: 2px;
    background-color: #07579F;
    border: 1px solid #07579F;
    word-break: break-all;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    color: #fff;
    -webkit-box-shadow: none;
    box-shadow: none;
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

.submitform {
    background-color: #07579F;
    color: #fff;
}
.close-btn{
    color: #07579F !important;
}


.close-btn:hover{
    color: #07579F !important;
}

.btn:disabled, fieldset:disabled .btn {
    color: #fff;
    pointer-events: none;
    background-color: #07579F !important;
    opacity: var(--vz-btn-disabled-opacity);
}


.submitform:hover{
    background-color: #07579F !important;
    color: #fff;
}

.btn-success:focus {
    color: #fff;
    background-color: #07579F !important;
    box-shadow: none;
}
.btn-success:active:focus {
    color: #fff;
    background-color: #07579F !important;
    box-shadow: none !important;

}
.submitform:active {
    color: #fff !important;

}


div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm {
    border: 0;
    border-radius: .25em;
    background: initial;
    background-color: #07579F;
    color: #fff;
    font-size: 1em;
}

.btn-danger{
    background-color: #c43029;
}
    .custom-modal-dialog {
        max-width: 800px;
        width: 100%;
    }
    .la, .las {
        font-family: "Line Awesome Free";
    font-weight: 900;
    font-size: 23px;
    margin-left: 10px;
}

#create-btn{
    background-color: #07579F;
}


    .loader {
        border: 8px solid #f3f3f3;
        / Light grey /
        border-top: 8px solid #07579F;
        / Blue /
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
.dropdown-toggle::after {
    display: inline-block;
    margin-left: .255em;
    font-size: 15px;
    line-height: 15px;
    content: "";
    font-family: "Material Design Icons";
}

.submitform , .resetrecords, .resetrecords:hover, .resetrecords:active{
    background-color: #07579F;
    color: #fff;
}

.restore-close-btn ,.restore-close-btn:hover {
    color: #c43029;
    color: #c43029 !important;
}
    
</style>


<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'REALTOR&#174;'])

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


                                        <div class="col-xxl-2 col-sm-4 mx-3">
                                            <div>
                                                <select class="form-control" data-choices data-choices-removeItem
                                                    data-choices-search-false name="choices-single-default[]"
                                                    id="language" multiple>
                                                    <option value="" disabled>Language Spoken</option>

                                                    <option value="English">English</option>
                                                    <option value="Spanish">Spanish</option>
                                                    <option value="French">French</option>
                                                    <option value="Punjabi">Punjabi</option>
                                                    <option value="Urdu">Urdu</option>
                                                    <option value="Arabic">Arabic</option>
                                                    <option value="Chinese (Cantonese)">Chinese (Cantonese)</option>
                                                    <option value="Chinese (Mandarin)">Chinese (Mandarin)</option>
                                                    <option value="Tamil">Tamil</option>
                                                    <option value="Farsi">Farsi</option>
                                                    <option value="Persian">Persian</option>
                                                    <option value="Italian">Italian</option>
                                                    <option value="Polish">Polish</option>
                                                    <option value="Russian">Russian</option>
                                                    <option value="Tagalog (Filipino)">Tagalog (Filipino)</option>
                                                    <option value="Hungarian">Hungarian</option>
                                                    <option value="Portuguese">Portuguese</option>
                                                    <option value="Croatian">Croatian</option>
                                                    <option value="Serbian">Serbian</option>
                                                    <option value="Gujarati">Gujarati</option>
                                                    <option value="Korean">Korean</option>
                                                    <option value="Dutch">Dutch</option>
                                                    <option value="German">German</option>
                                                    <option value="Turkish">Turkish</option>
                                                    <option value="Hebrew">Hebrew</option>
                                                    <option value="Malayalam">Malayalam</option>
                                                    <option value="Greek">Greek</option>
                                                    <option value="American Sign Language (ASL)">American Sign Language
                                                        (ASL)</option>
                                                    <option value="Romanian">Romanian</option>
                                                    <option value="Pashto">Pashto</option>
                                                    <option value="Slovak">Slovak</option>
                                                    <option value="Marathi">Marathi</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-sm-auto ms-auto">
                                            <div class="hstack gap-2">
                                                <button class="btn btn-soft-danger" id="remove-actions"
                                                    onClick="deleteMultiple()"><i
                                                        class="ri-delete-bin-2-line"></i></button>
                                                <button type="button" class="btn btn-success add-btn"
                                                    data-bs-toggle="modal" id="create-btn"
                                                    data-bs-target="#showModal"><i
                                                        class="ri-add-line align-bottom me-1"></i> Add REALTORS&#174;</button>


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
                                                    <i class="ri-store-2-fill me-1 align-bottom"></i> All REALTOR&#174;
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link py-3 Delivered" data-bs-toggle="tab" id="Delivered"
                                                    href="#delivered" role="tab" aria-selected="false">
                                                    <i class="ri-checkbox-circle-line me-1 align-bottom"></i> Active
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link py-3 Returns" data-bs-toggle="tab" id="Returns"
                                                    href="#returns" role="tab" aria-selected="false">
                                                    <i class="ri-arrow-left-right-fill me-1 align-bottom"></i> Inactive
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab"
                                                    id="Cancelled" href="#cancelled" role="tab"
                                                    aria-selected="false">
                                                    <i class="ri-close-circle-line me-1 align-bottom"></i> Deleted
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
                                                        <th class="sort" data-sort="address">Language Spoken</th>
                                                        <th class="sort" data-sort="mls_id">REALTOR&#174; Id</th>
                                                        <th class="sort" data-sort="position">Job Title</th>
                                                        <th class="sort" data-sort="status">Status</th>
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
                                                        {{ $total_agent }}+ agents We
                                                        did not find any agents for you search.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pagination_wrap"></div>
                                    </div>
                                    <div class="modal fade" id="showModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light p-3">
                                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close" id="close-modal"></button>
                                                </div>

                                                <form class="tablelist-form" id="tablelist" autocomplete="off"
                                                    method="post" action="{{ route('store-agent') }}"
                                                    enctype="multipart/form-data" novalidate>
                                                    @csrf
                                                    <div class="modal-body">
                                                        <input type="hidden" id="id-field" name="id-field" />

                                                        <div class="text-center">

                                                        </div>

                                                        <div class="row g-3">
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_name-field"
                                                                        class="form-label">First Name<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="agent_first-field"
                                                                        name="agent_first" class="form-control"
                                                                        placeholder="First Name" required />

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_name-field"
                                                                        class="form-label">Last Name<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="agent_last-field"
                                                                        name="agent_last" class="form-control"
                                                                        placeholder="Last Name" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_email-field"
                                                                        class="form-label">REALTOR&#174; Email<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="email" id="agent_email-field"
                                                                        name="agent_email" class="form-control"
                                                                        maxlength="30" placeholder="Email" required />
                                                                    <small id="email-error" class="text-danger"
                                                                        style="display: none;">This email is already
                                                                        taken.</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_position-field"
                                                                        class="form-label">Job Title<span
                                                                            style="color: red">*</span></label>
                                                                    <div class="dropdown  w-100 " >
                                                                        <button class="btn btn-light w-100 d-flex align-items-center justify-content-between dropdown-toggle"
                                                                            type="button"
                                                                            id="agent_position-dropdown"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            Select Position
                                                                        </button>
                                                                        <input type="hidden"
                                                                            id="agent_position-field"
                                                                            name="agent_position" class="form-control"
                                                                            required>
                                                                        <div class="dropdown-menu w-100"
                                                                            aria-labelledby="agent_position-dropdown">
                                                                            @foreach ($positions as $position)
                                                                                <button class="dropdown-item"
                                                                                    type="button"
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
                                                                    <label for="agent_profile"
                                                                        class="form-label">REALTOR&#174; Picture<span
                                                                            style="color: red">*</span></label>
                                                                    <br><span id="file-name-element"
                                                                        class="file-name"></span>
                                                                    <input class="form-control" value=""
                                                                        id="agent_profile_edit" name="agent_profile"
                                                                        type="file"
                                                                        accept="image/png, image/gif, image/jpeg">
                                                                    <input type="hidden" value=""
                                                                        id="old_image" name="old_image">
                                                                    <img id="agent_image_display" width="100"
                                                                        alt="Agent Picture" style="display: none;">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="agent_address"
                                                                        class="form-label">REALTOR&#174; Address<span
                                                                            style="color: red">*</span></label>
                                                                    <textarea id="agent_address" name="agent_address" class="form-control" placeholder="Address" rows="2"
                                                                        required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="agent_description"
                                                                        class="form-label">REALTOR&#174; Description<span
                                                                            style="color: red">*</span></label>
                                                                    <textarea id="agent_description" name="agent_description" class="form-control" placeholder="Description"
                                                                        rows="4" required></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_phone-field"
                                                                        class="form-label">REALTOR&#174; Phone<span
                                                                            style="color: red">*</span></label>
                                                                    <input type="text" id="agent_phone-field"
                                                                        pattern="[0-9]{10}"
                                                                        title="Phone number should contain exactly 10 digits"
                                                                        name="agent_phone" class="form-control"
                                                                        maxlength="10" placeholder="Phone Number"
                                                                        required />
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_office-field"
                                                                        class="form-label">REALTOR&#174; Office
                                                                        Number</label>
                                                                    <input type="text" id="agent_office-field"
                                                                        pattern="[0-9]{10}" maxlength="10"
                                                                        name="agent_office" class="form-control"
                                                                        placeholder="Office Number" required />
                                                                </div>
                                                            </div>

                                                            {{-- <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_fax-field"
                                                                        class="form-label">REALTOR&#174; Fax Number</label>
                                                                    <input type="text" id="agent_fax-field"
                                                                        pattern="[0-9]{10}" maxlength="10"
                                                                        name="agent_fax" class="form-control"
                                                                        placeholder="Fax Number" required />
                                                                </div>
                                                            </div> --}}

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_mls-field"
                                                                        class="form-label">REALTOR&#174; Id</label>
                                                                    <input type="text" id="agent_mls-field"
                                                                        name="agent_mls" class="form-control"
                                                                        placeholder="REALTOR&#174; Id" required />
                                                                </div>
                                                            </div>
                                                         
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_status"
                                                                        class="form-label">Status</label>
                                                                    <div class="form-check form-switch">
                                                                        <input type="hidden" name="status"
                                                                            value="0">
                                                                        <input class="form-check-input"
                                                                            type="checkbox" role="switch"
                                                                            id="flexSwitchCheckDefault">
                                                                        <label class="form-check-label"
                                                                            for="flexSwitchCheckDefault"
                                                                            style="font-size: 15px"
                                                                            id="statusLabel">Inactive</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="agent_logo-input"
                                                                        class="form-label">REALTOR&#174; Logo</label>
                                                                    <input class="form-control" value=""
                                                                        id="agent_logo-input" name="agent_logo"
                                                                        type="file"
                                                                        accept="image/png, image/gif, image/jpeg">
                                                                    <input type="hidden" value=""
                                                                        id="old_logo" name="old_logo">
                                                                    <img id="agent_logo_display" width="100"
                                                                        alt="Agent Logo" style="display: none;">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_website"
                                                                        class="form-label">REALTOR&#174; Website</label>
                                                                    <input type="text" id="agent_website"
                                                                        name="agent_website" class="form-control"
                                                                        placeholder="Website" required />
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="agent_website"
                                                                        class="form-label">REALTOR&#174; Password</label>
                                                                    <input type="text" id="agent_password"
                                                                        name="agent_password" class="form-control"
                                                                        placeholder="Password" required />
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="specialisation"
                                                                        class="form-label">Specialties</label>
                                                                    <input type="text" id="specialisation"
                                                                        name="specialisation" class="form-control"
                                                                        placeholder="Specialties" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="designation"
                                                                        class="form-label">Designations</label>
                                                                    <input type="text" id="designation"
                                                                        name="designation" class="form-control"
                                                                        placeholder="Designations" required />
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="facebook-field"
                                                                        class="form-label">Facebook</label>
                                                                    <input type="text" id="facebook-field"
                                                                        name="facebook" class="form-control"
                                                                        placeholder="Facebook" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="linkedin-field"
                                                                        class="form-label">LinkedIn</label>
                                                                    <input type="text" id="linkedin-field"
                                                                        name="linkedin" class="form-control"
                                                                        placeholder="LinkedIn" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="twitter-field"
                                                                        class="form-label">Twitter</label>
                                                                    <input type="text" id="twitter-field"
                                                                        name="twitter" class="form-control"
                                                                        placeholder="Twitter" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="youtube-field"
                                                                        class="form-label">YouTube</label>
                                                                    <input type="text" id="youtube-field"
                                                                        name="youtube" class="form-control"
                                                                        placeholder="YouTube" />
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="instagram-field"
                                                                        class="form-label">Instagram</label>
                                                                    <input type="text" id="instagram-field"
                                                                        name="instagram" class="form-control"
                                                                        placeholder="Instagram" />
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
                                                                                <option value="{{ $language }}">
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
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn submitform"
                                                                id="add-btn"
                                                                onclick="validateAndSubmitForm()">Submit</button>
                                                        </div>
                                                    </div>
                                                    <div id="loader" class="loader"></div>

                                                </form>



                                            </div>
                                        </div>
                                    </div>


                                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1"
                                        aria-labelledby="deleteRecordLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                     <button type="button" class="btn-close close-btn" data-bs-dismiss="modal"
                                                        aria-label="Close" id="btn-close"></button>
                                                </div>
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                                        style="width:90px;height:90px"></lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4 class="fs-semibold">You are about to delete a REALTOR&#174; ?</h4>
                                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your REALTOR&#174; will
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
                                    <div class="modal fade zoomIn" id="resetRecordModal" tabindex="-1"
                                    aria-labelledby="deleteRecordLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                 <button type="button" class="btn-close close-btn" data-bs-dismiss="modal"
                                                        aria-label="Close" id="btn-close"></button>
                                            </div>
                                            <div class="modal-body p-5 text-center">
                                                <lord-icon
                                                src="https://cdn.lordicon.com/ogkflacg.json"
                                                trigger="loop"
                                                colors="primary:#405189,secondary:#f06548"
                                                style="width:150px;height:150px">
                                            </lord-icon>
                                                <div class="mt-4 text-center">
                                                    <h4 class="fs-semibold">You are about to restore a REALTOR&#174;?</h4>
                                                    <p class="text-muted fs-14 mb-4 pt-1">Restoring this REALTOR&#174; will bring back all associated information.</p>
                                                    <div class="hstack gap-2 justify-content-center remove">
                                                         <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none restore-close-btn"
                                                                id="deleteRecord-close" data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Close</button>
                                                        <button class="btn btn-success resetrecords"
                                                            id="resetrecord">Yes, Restore It!!</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               

                                    
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample"
                                        aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header bg-light">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">REALTOR&#174; Filters</h5>
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
                                                        class="form-label text-muted text-uppercase fw-bold fs-13 mb-3">REALTOR&#174;
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
        
        var isEditing = false;
        const loader = document.getElementById('loader');
        loader.style.display = 'none';
        // $('#idStatus').on('change', function() {
        //     var selectedOption = $(this).val();
        //     switch (selectedOption) {
        //         case 'all':
        //             $('a.nav-link#All').tab('show');
        //             $('th[data-sort="status"]').addClass('sort');
        //             break;
        //         case 'Pending':
        //             $('a.nav-link#Delivered').tab('show');
        //             $('th[data-sort="status"]').removeClass('sort');
        //             break;
        //         case 'Inprogress':
        //             $('a.nav-link#Returns').tab('show');
        //             $('th[data-sort="status"]').removeClass('sort');
        //             break;
        //         default:
        //             break;
        //     }
        // });

        $('a.nav-link').on('click', function() {
            var tabId = $(this).attr('id');
            if (tabId === 'Delivered' || tabId === 'Returns' || tabId === 'Cancelled') {
                $('th[data-sort="status"]').removeClass('sort');
            } else {
                $('th[data-sort="status"]').addClass('sort');
            }
        });



        $(document).ready(function() {
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

            $('#agent_logo-input').change(function() {

                var file = this.files[0];
                if (file) {

                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#agent_logo_display').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#showModal').on('hidden.bs.modal', function() {
                isEditing = false;
                $('#email-error').hide();
                $('#agent_profile_edit').val('');
            });

            function clearEditModalFields() {
                document.getElementById('agent_image_display').style.display = 'none';
                document.getElementById('agent_logo_display').style.display = 'none';

                $('#id-field').val('');
                $('#agent_first-field').val('');
                $('#agent_last-field').val('');
                $('#old_image').val('');
                $('#old_logo').val('');
                $('#agent_email-field').val('');
                $('#agent_position-dropdown').text('Select Position');
                $('#agent_description').val('');
                $('#agent_position-field').val('');
                $('#file-name-element').text('');
                $('#agent_address').val('');
                $('#agent_phone-field').val('');
                $('#agent_office-field').val('');
                // $('#agent_fax-field').val('');
                $('#agent_mls-field').val('');
                $('#flexSwitchCheckDefault').prop('checked', false);
                $('#facebook-field').val('');
                $('#linkedin-field').val('');
                $('#twitter-field').val('');
                $('#youtube-field').val('');
                $('#instagram-field').val('');
                $('#agent_website').val('');
                $('#specialisation').val('');
                $('#designation').val('');
            }


            $(document).on('click', '.remove-item-btn', function(event) {
                var agentId = $(this).data('agent-id');
                $('#deleteRecordModal').data('agent-id', agentId);
            });
            $(document).on('click', '.reset-item-btn', function(event) {
                var agentId = $(this).data('agent-id');
                $('#resetRecordModal').data('agent-id', agentId);
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
                        nextPageData("getUsersByStatus?page=" + page, '');

                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting agent:', error);
                        alert('Error occurred while deleting agent.');
                    }
                });
            });
            $(document).on('click', '.resetrecords', function(e) {
    e.preventDefault();
    var agentId = $('#resetRecordModal').data('agent-id');
    console.log('Agent ID:', agentId);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: '/restore-agent/' + agentId,
        type: 'post', 
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            $('#agent-' + agentId).removeClass('text-danger').addClass('text-success');

            $('#resetRecordModal').modal('hide');

            var page = $('.page-item.active').text();
            nextPageData("getUsersByStatus?page=" + page, '');
        },
        error: function(xhr, status, error) {
            console.error('Error restoring agent:', error);
            alert('Error occurred while restoring agent.');
        }
    });
});


            function resetFormState() {
                $('#email-error').hide();
                $('#add-btn').prop('disabled', false);
            }


            $(document).on('click', '.edit-item-btn', function(event) {
                isEditing = true;
                document.getElementById('agent_image_display').style.display = 'inline-block';
                document.getElementById('agent_logo_display').style.display = 'inline-block';
                resetFormState();
                var editModal = $('#showModal');
                var editForm = $('.tablelist-form');
                var agentId = $(this).data('agent-id');
                $('#edit-agent-id').val(agentId);
                $.ajax({
                    url: '/agents/' + agentId,
                    type: 'GET',
                    success: function(response) {

                        var fullName = response.agent.name;
                        var nameParts = fullName.split(' ');
                        var firstName = nameParts[0];
                        var lastName = nameParts.slice(1).join(' ');
                        var agentImage = response.agent.profile_picture;
                        var agentlogo = response.agent.agent_logo;

                        if (!agentlogo) {
                            agentlogo = 'No-Image-Placeholder.png';
                        }


                        function formatPhoneNumber(phoneNumberString) {
                            var cleaned = ('' + phoneNumberString).replace(/\D/g, '');
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
                                return '(' + match[1] + ') ' + match[2] + '-' + match[3];
                            }
                            return phoneNumberString;
                        }

                        var formattedPhone = formatPhoneNumber(response.agent.phone);
                        var office = formatPhoneNumber(response.agent.office_no);
                        var fax = formatPhoneNumber(response.agent.fax_no);
                        var languages = response.agent.language;

                        var selectedValues = document.getElementById('language-field')
                            .options
                        for (var i = 0; i < selectedValues.length; i++) {
                            $('#language-field option')
                                .prop('selected', false);
                        }
                        $('#language-field').trigger('chosen:updated');

                        if (languages != null && languages != '') {

                            var languagesArray = languages.split(',');
                            var selectedValues = languagesArray;

                            $('#language-field').chosen();

                            for (var i = 0; i < selectedValues.length; i++) {
                                $('#language-field option[value="' + selectedValues[i] + '"]')
                                    .prop('selected', true);
                            }
                            $('#language-field').trigger('chosen:updated');
                        } else {
                            $('#language-field').chosen();
                            var selectedValues = document.getElementById('language-field')
                                .options
                            for (var i = 0; i < selectedValues.length; i++) {
                                $('#language-field option')
                                    .prop('selected', false);
                            }
                            $('#language-field').trigger('chosen:updated');
                        }

                        editForm.find('#agent_first-field').val(firstName);
                        editForm.find('#agent_last-field').val(lastName);
                        editForm.find('#agent_email-field').val(response.agent.email);
                        editForm.find('#agent_phone-field').val(formattedPhone);
                        editForm.find('#agent_office-field').val(office);
                        editForm.find('#agent_image_display').attr('src', agentImage);
                        editForm.find('#agent_logo_display').attr('src', agentlogo);
                        editForm.find("#old_image").val(agentImage);
                        editForm.find("#old_logo").val(agentlogo);
                        editForm.find('#agent_position-field').val(response.agent.position);
                        // editForm.find('#agent_fax-field').val(fax);
                        editForm.find('#agent_mls-field').val(response.agent.mls_id);
                        editForm.find('#agent_address').val(response.agent.address);
                        editForm.find('#agent_description').val(response.agent.description);
                        editForm.find('#agent_position-dropdown').text(response.agent.position);
                        editForm.find('#address-field').val(response.agent.address);
                        editForm.find('#linkedin-field').val(response.agent.linkedin);
                        editForm.find('#facebook-field').val(response.agent.facebook);
                        editForm.find('#youtube-field').val(response.agent.youtube);
                        editForm.find('#twitter-field').val(response.agent.twitter);
                        editForm.find('#instagram-field').val(response.agent.instagram);
                        editForm.find('#agent_website').val(response.agent.website);
                        editForm.find('#specialisation').val(response.agent.specialisation);
                        editForm.find('#designation').val(response.agent.designation);
                        editForm.find('#id-field').val(response.agent.id);
                        if (response.agent.status == 1) {
                            $('#flexSwitchCheckDefault').prop('checked', true);
                            $('#statusLabel').text('Active');
                        } else {
                            $('#flexSwitchCheckDefault').prop('checked', false);
                            $('#statusLabel').text('Inactive');
                        }

                        $('#showModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Error fetching agent data:', error);
                    }
                });

            });
            $('#create-btn').click(function() {
                $('#language-field').chosen();
                var selectedValues = document.getElementById('language-field').options
                for (var i = 0; i < selectedValues.length; i++) {
                    $('#language-field option')
                        .prop('selected', false);
                }
                $('#language-field').trigger('chosen:updated');
                clearEditModalFields();
            });


        });
        ['agent_phone-field', 'agent_office-field'].forEach(fieldId => {
            document.getElementById(fieldId).addEventListener("input", function(event) {
                let phoneInput = event.target.value.replace(/\D/g, '').slice(0, 10);
                event.target.value = phoneInput.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            var switchCheckbox = document.getElementById('flexSwitchCheckDefault');
            var statusLabel = document.getElementById('statusLabel');
            var hiddenInput = document.querySelector('input[name="status"]');

            switchCheckbox.addEventListener('change', function() {
                if (switchCheckbox.checked) {
                    hiddenInput.value = 1;
                    statusLabel.textContent = 'Active';
                } else {
                    console.log('heres');
                    hiddenInput.value = 0;
                    statusLabel.textContent = 'Inactive';
                }
            });
        });

        function validateAndSubmitForm() {
            const form = document.querySelector('.tablelist-form');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const loader = document.getElementById('loader');
                loader.style.display = 'block';
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
                const status = document.getElementById('flexSwitchCheckDefault').checked ? "1" :
                    "0";
                if (firstName === '' || lastName === '' || email === '' || position === '' ||
                    address === '' || description === '' || phone === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill all the required fields.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a valid email address.'
                    });
                    loader.style.display = 'none';
                    return;
                }
                if (phone.length < 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please make sure the phone number at least 10 digits long.'
                    });
                    loader.style.display = 'none';
                    return;
                }
                if (address.length > 200) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Address should be less than 200 characters.'
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

                if (!oldimage && !['image/png', 'image/jpeg'].includes(profilePicture?.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please upload a valid image file (PNG or JPEG).'
                    });
                    loader.style.display = 'none';
                    return;
                }

                if (profilePicture?.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Profile picture size should be less than 2MB.'
                    });
                    loader.style.display = 'none';
                    return;
                }

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                form.appendChild(statusInput);
                const submitButton = document.getElementById('add-btn');
                submitButton.disabled = true;
              setTimeout(function() {
            // Hide loader
            // loader.style.display = 'none';

            Swal.fire({
                icon: 'success',
                title: 'Thank You!',
                text: 'Your form has been submitted successfully.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }, 1000); 

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

        document.getElementById("agent_first-field").addEventListener("keypress", validateAlphabets);
        document.getElementById("agent_last-field").addEventListener("keypress", validateAlphabets);
        var options = {
            valueNames: ['name', 'email', 'phone', 'address', 'mls_id', 'position']
        };

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });

        $('#userSearch').keyup(function(event) {
            var query = $(this).val();
            var status = '';
            $(".nav-link").each(function() {

                if ($(this).hasClass("active")) {
                    var tabId = $(this).attr('id');
                    if (tabId === 'Delivered') {
                        status = 1;
                    } else if (tabId === 'All') {
                        status = '';
                    } else if (tabId === 'Returns') {
                        status = 0;
                    } else {
                        status = 2;
                    }
                }
            });

            if(event.which === 37){
                console.log('up');

            }
            else if(event.which === 40){
                console.log('down');
            }
            if (query !== '' && event.which !== 13) {
                $.ajax({
                    url: "{{ route('getautosuggestion') }}",
                    method: "GET",
                    data: {
                        term: query,
                        status: status
                    },
                    success: function(data) {

                        var nameSuggestions = [];
                        var emailSuggestions = [];
                        var phoneSuggestions = [];

                        $.each(data, function(key, user) {
                            if (user.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !
                                nameSuggestions.includes(user.name)) {
                                nameSuggestions.push(user.name);
                            }
                            if (user.email.toLowerCase().indexOf(query.toLowerCase()) !== -1 &&
                                !emailSuggestions.includes(user.email)) {
                                emailSuggestions.push(user.email);
                            }
                            var phoneString = user.phone.toString();
                            if (phoneString.indexOf(query) !== -1 && !phoneSuggestions.includes(
                                    user.phone)) {
                                phoneSuggestions.push(user.phone);
                            }
                        });

                        var allSuggestions = '';

                        if (nameSuggestions.length > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Name</strong></span><ul>';
                            $.each(nameSuggestions, function(index, name) {
                                allSuggestions += '<li class="searchs" data-name="' + name +
                                    '">' + name + '</li>';
                            });
                            allSuggestions += '</ul></div>';
                        }
                        if (emailSuggestions.length > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Email</strong></span><ul>';
                            $.each(emailSuggestions, function(index, email) {
                                allSuggestions += '<li class="searchs" data-email="' + email +
                                    '">' + email + '</li>';
                            });
                            allSuggestions += '</ul></div>';
                        }
                        if (phoneSuggestions.length > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Phone</strong></span><ul>';
                            $.each(phoneSuggestions, function(index, phone) {
                                allSuggestions += '<li class="searchs" data-phone="' + phone +
                                    '">' + phone + '</li>';
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

        function performSearch(query) {
            // var selectedStatus = $('#idStatus').val();
            // if (selectedStatus == '') {

            $(".nav-link").each(function() {
                if ($(this).hasClass("active")) {
                    var tabId = $(this).attr('id');
                    if (tabId === 'Delivered') {
                        status = 1;
                    } else if (tabId === 'All') {
                        status = '';
                    } else if (tabId === 'Returns') {
                        status = 0;
                    } else {
                        status = 2;
                    }
                }
            });
            // } 
            // else {
            //     if (selectedStatus === 'all') {
            //         status = '';
            //     } else if (selectedStatus === 'Pending') {
            //         status = 1;
            //     } else if (selectedStatus === 'Inprogress') {
            //         status = 0;
            //     }

            // }

            $.ajax({
                url: "{{ route('getautoquery') }}",
                method: "GET",
                data: {
                    name: query,
                    email: query,
                    phone: query,
                    status: status
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

                        html += '<a href="/agent-details/' + btoa(agent.id) +
                            '" class="text-primary d-inline-block custom-anchor">' + agent
                            .name + '</a>';
                        html += '</div>';
                        html += '</td>';
                        html += '<td class="email">' + agent.email + '</td>';
                        var phoneHtml = '';
                        if (agent.phone !== null) {
                            phoneHtml = '<td class="phone"> (' + String(agent.phone).substring(0, 3) +
                                ') ' + String(agent.phone).substring(3, 6) + '-' +
                                String(agent.phone).substring(6) + '</td>';
                        } else {
                            phoneHtml =
                                '<td class="phone"> </td>';
                        }
                        html += phoneHtml;

                        html += '<td class="address">' + formattedLanguage + '</td>';
                        html += '<td class="mls_id">' + (agent.mls_id !== null ? agent.mls_id :
                            '') + '</td>';
                        html += '<td class="position">' + (agent.position !== null ?
                            agent.position : '') + '</td>';
                        html += '<td class="status">';
                        html += agent.status == 1 ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                        html += '</td>';
                        html += '<td>';
                        html += '<ul class="list-inline hstack gap-2 mb-0">';
                        html += '<a href="/agent-details/' + btoa(agent.id) +
                            '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';

                        '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                        html +=
                            '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                            agent.id +
                            '"><i class="ri-pencil-fill align-bottom text-muted"></i></a>';
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
        document.getElementById("agent_phone-field").addEventListener("keypress", function(event) {
            var key = event.key;
            if (isNaN(key)) {
                event.preventDefault();
            }
        });

        document.getElementById("agent_office-field").addEventListener("keypress", function(event) {
            var key = event.key;
            if (isNaN(key)) {
                event.preventDefault();
            }
        });

        
        $(document).on('click', 'li', function() {
            $('#userSuggestions').fadeOut();
        });

        // $(document).ready(function() {
        //     $('.nav-link').on('click', function() {
        //         var tabId = $(this).attr('id');
        //         var status = '';
        //         if (tabId === 'Delivered') {
        //             status = 1;
        //         } else if (tabId === 'All') {
        //             status = '';
        //         } else if (tabId === 'Returns') {
        //             status = 0;
        //         } else {
        //             status = 2;
        //         }

        //         $.ajax({
        //             url: "{{ route('getUsersByStatus') }}",
        //             method: 'GET',
        //             data: {
        //                 status: status
        //             },
        //             success: function(response) {
        //                 if (response.data.length === 0) {
        //                     $('.noresult').show();
        //                     $('#agentTable tbody').empty();
        //                     return;
        //                 }
        //                 $('.noresult').hide();
        //                 var html = '';
        //                 $.each(response.data, function(index, agent) {
        //                     var formattedLanguage = '';
        //                     if (agent.language) {
        //                         formattedLanguage = agent.language.split(',').join(
        //                             ', ');
        //                     }
        //                     html += '<tr>';
        //                     html += '<td class="id">' + agent.ids + '</td>';
        //                     html += '<td>';
        //                     html += '<div class="d-flex align-items-center">';
        //                     html += '<div class="flex-shrink-0">';
        //                     if (agent.profile_picture) {
        //                         html += '<img src="' + agent
        //                             .profile_picture +
        //                             '" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
        //                     } else {

        //                         html +=
        //                             '<img src="assets/images/No-Image-Placeholder.png" alt="Default Image" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
        //                     }
        //                     html += '<a href="/agent-details/' + btoa(agent.id) +
        //                         '" class="text-primary d-inline-block custom-anchor">' +
        //                         agent.name + '</a>';

        //                     html += '</div>';
        //                     html += '</div>';
        //                     html += '</td>';
        //                     html += '<td class="email">' + agent.email + '</td>';
        //                     var phoneHtml = '';
        //                     if (agent.phone !== null) {
        //                         phoneHtml = '<td class="phone"> (' + String(agent.phone)
        //                             .substring(0, 3) +
        //                             ') ' + String(agent.phone).substring(3, 6) + '-' +
        //                             String(agent.phone).substring(6) + '</td>';
        //                     } else {
        //                         phoneHtml =
        //                             '<td class="phone"> </td>';
        //                     }
        //                     html += phoneHtml;
        //                     html += '<td class="address">' + formattedLanguage +
        //                         '</td>';
        //                     html += '<td class="mls_id">' + (agent.mls_id !== null ?
        //                         agent.mls_id : '') + '</td>';
        //                     html += '<td class="position">' + (agent.position !== null ?
        //                         agent.position : '') + '</td>';

        //                     html += '<td class="status">';
        //                     html += agent.status == 1 ?
        //                         '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
        //                         '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
        //                     html += '</td>';
        //                     html += '<td>';
        //                     html += '<ul class="list-inline hstack gap-2 mb-0">';
        //                     html += '<a href="/agent-details/' + btoa(agent.id) +
        //                         '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';

        //                     '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
        //                     html +=
        //                         '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
        //                         agent.id +
        //                         '"><i class="ri-pencil-fill align-bottom text-muted"></i></a>';
        //                     html += '</li>';
        //                     html +=
        //                         '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">';
        //                     html += '<a class="remove-item-btn" data-agent-id="' +
        //                         agent.id +
        //                         '" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-fill align-bottom text-danger"></i></a>';
        //                     html += '</li>';

        //                     html += '</ul>';
        //                     html += '</td>';
        //                     html += '</tr>';
        //                 });
        //                 $('#agentTable tbody').html(html);
        //                 createPaginationLinks(response);

        //             },
        //             error: function(xhr, status, error) {
        //                 console.error(xhr.responseText);
        //             }
        //         });
        //     });
        // });

        window.load = nextPageData("getUsersByStatus?status=''", '')
        $('#pagination_wrap').show();

        function nextPageData(nextPageUrl, status) {
            var status = '';
            var language = $('#language').val();
            var query = $('#userSearch').val();
            // var selectedStatus = $('#idStatus').val();

            $(".nav-link").each(function() {
                if ($(this).hasClass("active")) {
                    var tabId = $(this).attr('id');
                    if (tabId === 'Delivered') {
                        status = 1;
                    } else if (tabId === 'All') {
                        status = '';
                    } else if (tabId === 'Returns') {
                        status = 0;
                    } else {
                        status = 2;
                    }
                }
            });

            let reqData = {
                status: status,
                language: language,
                query: query,

            };

            $.ajax({
                url: nextPageUrl,
                method: 'GET',
                data: reqData,
                success: function(response) {
                    console.log('response: ', response);
                    var html = '';
                    if (nextPageUrl.includes('deleted') || status == 2) {
                        deleteNextPage(response);
                    } else {
                        if (response.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                        $('.noresult').hide();
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
                            if (agent.profile_picture) {
                                html += '<img src="' + agent.profile_picture +
                                    '" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                            } else {

                                html +=
                                    '<img src="assets/images/No-Image-Placeholder.png" alt="Default Image" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                            }
                            html += '<a href="/agent-details/' + btoa(agent.id) +
                                '" class="text-primary d-inline-block custom-anchor">' +
                                agent.name + '</a>';

                            html += '</div>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="email">' + agent.email + '</td>';
                            var phoneHtml = '';
                            if (agent.phone !== null) {
                                phoneHtml = '<td class="phone"> (' + String(agent.phone).substring(0,
                                        3) +
                                    ') ' + String(agent.phone).substring(3, 6) + '-' +
                                    String(agent.phone).substring(6) + '</td>';
                            } else {
                                phoneHtml =
                                    '<td class="phone"> </td>';
                            }
                            html += phoneHtml;
                            html += '<td class="address">' + formattedLanguage + '</td>';
                            html += '<td class="mls_id">' + (agent.mls_id !== null ? agent.mls_id :
                                '') + '</td>';
                            html += '<td class="position">' + (agent.position !== null ?
                                agent.position : '') + '</td>';
                            html += '<td class="status">';
                            html += agent.status == 1 ?
                                '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                                '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                            html += '</td>';
                            html += '<td>';
                            html += '<ul class="list-inline hstack gap-2 mb-0">';
                            html += '<a href="/agent-details/' + btoa(agent.id) +
                                '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';

                            '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                            html +=
                                '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                                agent.id +
                                '"><i class="ri-pencil-fill align-bottom text-muted"></i></a>';
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
                    }
                    o
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
      

        function createPaginationLinks(response) {
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

        function selectPosition(position) {
            document.getElementById('agent_position-field').value = position;
            document.getElementById('agent_position-dropdown').innerText = position;

        }

        $(document).ready(function() {
            $('.nav-link.Cancelled').on('click', function(e) {
                e.preventDefault();
                var url = '/deleted-agents';

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.deletedAgents.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }

                        $('.noresult').hide();
                        var html = '';
                        if (response.success && response.deletedAgents.data.length > 0) {
                            $.each(response.deletedAgents.data, function(index, agent) {
                                var formattedLanguage = '';
                                if (agent.language) {
                                    formattedLanguage = agent.language.split(',').join(
                                        ', ');
                                }

                                html += '<tr>';
                                html += '<td class="id">' + agent.ids + '</td>';
                                html += '<td>';
                                html += '<div class="d-flex align-items-center">';
                                html += '<div class="flex-shrink-0">';
                                if (agent.profile_picture) {
                                    html += '<img src="' + agent
                                        .profile_picture +
                                        '" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                                } else {

                                    html +=
                                        '<img src="assets/images/No-Image-Placeholder.png" alt="Default Image" class="avatar-xxs rounded-circle image_src object-fit-cover">  ';
                                }
                                html += '</div>';
                                html += '<div class="flex-grow-1 ms-2 name">' + agent
                                    .name +
                                    '</div>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td>' + agent.email + '</td>';
                                html += '<td>' + agent.phone + '</td>';
                                html += '<td>' + formattedLanguage + '</td>';
                                html += '<td class="mls_id">' + (agent.mls_id !== null ?
                                    agent.mls_id : '') + '</td>';
                                html += '<td class="position">' + (agent.position !==
                                    null ?
                                    agent.position : '') + '</td>';

                                html += '<td>' + (agent.status == 1 ?
                                    '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                                    '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>'
                                ) + '</td>';
                              
                                html += '<td>';

    html += '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Restore">';
    html += '<a class="reset-item-btn" data-agent-id="' + agent.id + '" data-bs-toggle="modal" href="#resetRecordModal"><i class="las la-sync align-bottom" style="color: #07579F;"></i></a>';
    html += '</li>';

    html += '</td>';
                                html += '</tr>';
                                createPaginationLinks(response.deletedAgents);
                            });
                        } else {
                            html += '<tr><td colspan="8">No deleted agents found.</td></tr>';
                        }
                        $('#agentTable tbody').html(html);
                        // $('#agentTable').addClass('deletion-tab');
                        // if ($('#agentTable').hasClass('deletion-tab')) {
                        //     $('#agentTable th[data-sort="action"]').hide();
                        // }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching deleted agents:', error);
                        alert('Error occurred while fetching deleted agents.');
                    }
                });
            });
        });

        $('#userSearch').on('input', function(e) {
            var inputValue = $(this).val();

            if (inputValue == '') {
                nextPageData("getUsersByStatus", '')
            }
        })

        function deleteNextPage(response) {
            var html = '';
            if (response.success && response.deletedAgents.data.length > 0) {
                $.each(response.deletedAgents.data, function(index, agent) {
                    var formattedLanguage = '';
                    if (agent.language) {
                        formattedLanguage = agent.language.split(',').join(', ');
                    }
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
                    html += '</div>';
                    html += '<div class="flex-grow-1 ms-2 name">' + agent
                        .name +
                        '</div>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td>' + agent.email + '</td>';
                    html += '<td>' + agent.phone + '</td>';
                    html += '<td>' + formattedLanguage + '</td>';
                    html += '<td class="mls_id">' + (agent.mls_id !== null ? agent.mls_id : '') + '</td>';
                    html += '<td class="position">' + (agent.position !== null ?
                        agent.position : '') + '</td>';

                    html += '<td>' + (agent.status == 1 ?
                        '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                        '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>'
                    ) + '</td>';

                    html += '<td>';

    html += '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Reset">';
    html += '<a class="reset-item-btn" data-agent-id="' + agent.id + '" data-bs-toggle="modal" href="#resetRecordModal"><i class="las la-sync align-bottom" style="color: #07579F;"></i></a>';
    html += '</li>';

html += '</td>';
                    html += '</tr>';
                    createPaginationLinks(response.deletedAgents);
                });
            } else {
                html += '<tr><td colspan="8">No deleted agents found.</td></tr>';
            }
            $('#agentTable tbody').html(html);
            // $('#agentTable').addClass('deletion-tab');
            // if ($('#agentTable').hasClass('deletion-tab')) {
            //     $('#agentTable th[data-sort="action"]').hide();
            // }
        }

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

                sortTableByColumn(tableElement, index, !currentIsAscending);
            });
        });



        $(document).ready(function() {

            var status = '';
            $('.nav-link').on('click', function() {
                var tabId = $(this).attr('id');
                var status = '';
                if (tabId === 'Delivered') {
                    status = 1;
                } else if (tabId === 'All') {
                    status = '';
                } else if (tabId === 'Returns') {
                    status = 0;
                } else {
                    status = 2;
                }

                var query = $('#userSearch').val();
                var selectedLanguages = $('#language').val();

                var requestData = {
                    status: status,
                };
                if (selectedLanguages && selectedLanguages.length > 0) {
                    requestData.language = selectedLanguages;
                }

                if (query.trim() !== '') {
                    requestData.query = query;
                }
                $.ajax({
                    url: '{{ route('getAgentsFiltered') }}',
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        if (response.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                        $('#pagination_wrap').show();
                        $('.noresult').hide();
                        var html = '';
                        $.each(response.data, function(index, agent) {
                            var formattedLanguage = agent.language ? agent.language
                                .split(',')
                                .join(
                                    ', ') :
                                '';
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
                            html += '<a href="/agent-details/' + btoa(agent.id) +
                                '" class="text-primary d-inline-block custom-anchor">' +
                                agent
                                .name +
                                '</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="email">' + agent.email + '</td>';
                            var phoneHtml = '';
                            if (agent.phone !== null) {
                                phoneHtml = '<td class="phone"> (' + String(agent.phone)
                                    .substring(
                                        0, 3) +
                                    ') ' + String(agent.phone).substring(3, 6) + '-' +
                                    String(agent.phone).substring(6) + '</td>';
                            } else {
                                phoneHtml =
                                    '<td class="phone"> </td>';
                            }
                            html += phoneHtml;
                            html += '<td class="address">' + formattedLanguage +
                                '</td>';
                            html += '<td class="mls_id">' + (agent.mls_id !== null ?
                                    agent
                                    .mls_id :
                                    '') +
                                '</td>';
                            html += '<td class="position">' + (agent.position !== null ?
                                agent.position : '') + '</td>';
                            html += '<td class="status">';
                            html += agent.status == 1 ?
                                '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                                '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                            html += '</td>';
                            html += '<td>';
                            html += '<ul class="list-inline hstack gap-2 mb-0">';
                            html += '<a href="/agent-details/' + btoa(agent.id) +
                                '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';
                            html +=
                                '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                            html +=
                                '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                                agent.id +
                                '"><i class="ri-pencil-fill align-bottom text-muted"></i></a>';
                            html += '</li>';
                            html +=
                                '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">';
                            html += '<a class="remove-item-btn" data-agent-id="' + agent
                                .id +
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
            })
        });
        $('#language').change(function() {
            var query = $('#userSearch').val();
            var selectedLanguages = $('#language').val();
            var status = '';
            var activeTab = $(".nav-link.active");

            var tabId = activeTab.attr('id');
            console.log(tabId);
            if (tabId === 'All') {
                status = '';
            } else if (tabId === 'Delivered') {
                status = 1;
            } else if (tabId === 'Returns') {
                status = 0;
            } else if (tabId === 'Cancelled') {
                status = 2;
            }

            var requestData = {
                status: status
            };
            if (selectedLanguages && selectedLanguages.length > 0) {
                requestData.language = selectedLanguages;
            }

            if (query.trim() !== '') {
                requestData.query = query;
            }
            $.ajax({
                url: '{{ route('getAgentsFiltered') }}',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    if (response.data.length === 0) {
                        $('.noresult').show();
                        $('#pagination_wrap').hide();
                        $('#agentTable tbody').empty();
                        return;
                    }

                    $('.noresult').hide();
                    var html = '';
                    $.each(response.data, function(index, agent) {
                        var formattedLanguage = agent.language ? agent.language
                            .split(',')
                            .join(
                                ', ') :
                            '';
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
                        html += '<a href="/agent-details/' + btoa(agent.id) +
                            '" class="text-primary d-inline-block custom-anchor">' +
                            agent
                            .name +
                            '</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</td>';
                        html += '<td class="email">' + agent.email + '</td>';
                        var phoneHtml = '';
                        if (agent.phone !== null) {
                            phoneHtml = '<td class="phone"> (' + String(agent.phone)
                                .substring(
                                    0, 3) +
                                ') ' + String(agent.phone).substring(3, 6) + '-' +
                                String(agent.phone).substring(6) + '</td>';
                        } else {
                            phoneHtml =
                                '<td class="phone"> </td>';
                        }
                        html += phoneHtml;
                        html += '<td class="address">' + formattedLanguage +
                            '</td>';
                        html += '<td class="mls_id">' + (agent.mls_id !== null ?
                                agent
                                .mls_id :
                                '') +
                            '</td>';
                        html += '<td class="position">' + (agent.position !== null ?
                            agent.position : '') + '</td>';
                        html += '<td class="status">';
                        html += agent.status == 1 ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                        html += '</td>';
                        html += '<td>';
                        html += '<ul class="list-inline hstack gap-2 mb-0">';
                        html += '<a href="/agent-details/' + btoa(agent.id) +
                            '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';
                        html +=
                            '<li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">';
                        html +=
                            '<a href="javascript:void(0)" class="text-primary d-inline-block edit-item-btn" data-agent-id="' +
                            agent.id +
                            '"><i class="ri-pencil-fill align-bottom text-muted"></i></a>';
                        html += '</li>';
                        html +=
                            '<li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">';
                        html += '<a class="remove-item-btn" data-agent-id="' + agent
                            .id +
                            '" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-fill align-bottom text-danger"></i></a>';
                        html += '</li>';
                        html += '</ul>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);
                    $('#pagination_wrap').show();
                    createPaginationLinks(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
        // });
    </script>

</body>

</html>