@include('layouts.session')
@include('layouts.main')

<head>

    @include('layouts.title-meta', ['title' => 'Realtor&#174;'])
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .custom-modal-dialog {
        max-width: 800px;
        width: 100%;
    }


    .pagination {
        float: right;
        margin-top: 20px;

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

  .btn:hover {
    background-color: #07579F;
}

.btn-success {
    background-color: #07579F;
}

.btn-check:checked+.btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check)+.btn:active {
    background-color: #07579F !important;
}
.flatpickr-months,.flatpickr-weekdays,.flatpickr-weekday,.flatpickr-day.startRange,.flatpickr-day.endRange {
    background-color: #07579F !important;
}
</style>

<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'Tour'])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="leadsList">
                                <div class="row g-4 align-items-center">
                                    <div style="position: relative; padding: 3%; margin-top: -3px;" class="col-sm-4">
                                        <div class="search-box">
                                            <input type="text" id="userSearch" name="userSearch"
                                                class="form-control search" placeholder="Search for...">
                                            <i class="ri-search-line search-icon"></i>
                                            <div id="userSuggestions"
                                                style="position: absolute; top: 67%; left: 40px; max-height: 150px; overflow-y: auto; z-index: 100; width: 290px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" style="margin-top:-3px;">
                                        <div>
                                            <select class="form-select" id="datetype">
                                                <option value="" selected>Select Date</option>
                                                <option value="1">Last 1 Day</option>
                                                <option value="7">Last 7 Days</option>
                                                <option value="15">Last 15 Days</option>
                                                <option value="custom">Custom Date</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3" id="custom-date-container" style="display: none;">
                                        <div style="margin-top:-9%;  margin-left:30px; width:280px;">
                                            <input type="text" class="form-control" data-provider="flatpickr"
                                                data-date-format="d M, Y" data-range-date="true" id="demo-datepicker"
                                                placeholder=" Select Custom Date">
                                        </div>
                                    </div>

                                    <div class="col-sm-auto ms-auto">
                                        <button type="button" class="btn btn-success add-btn" id="reset-btn"
                                            style="margin-top: -30px; margin-left: -20%;">Reset</button>
                                    </div>

                                </div>




                                <div class="card-body">
                                    <div>
                                        <div class="table-responsive table-card">

                                            <table class="table align-middle table-sortable" id="agentTable">
                                                <thead class="table-light">

                                                    <tr>

                                                        <th class="sort" data-sort="id">Sr.no</th>
                                                        <th class="sort" data-sort="name">Name</th>
                                                        <th class="sort" data-sort="email">Email</th>
                                                        <th class="sort" data-sort="phone">Phone</th>
                                                        <th class="sort" data-sort="message">Message</th>
                                                        <th class="sort" data-sort="time">Scheduled Time</th>
                                                        <th class="sort" data-sort="date">Scheduled Date</th>
                                                        <th class="sort" data-sort="Created_at">Created_at</th>

                                                        <!-- <th class="sort" data-sort="status">Status</th>
                                                        <th class="sort" data-sort="action">Action</th> -->
                                                    </tr>

                                                </thead>
                                                <tbody class="list form-check-all">

                                                </tbody>

                                            </table>
                                            <div class="noresult" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pagination_wrap"></div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>


                </div>
            </div>

            @include ('layouts.footer')
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script src="assets/libs/list.js/list.min.js"></script>
    {{-- <script src="assets/libs/list.pagination.js/list.pagination.min.js"></script> --}}
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/pages/crm-leads.init.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/language.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script>
        $('#userSearch').keyup(function() {
            var query = $(this).val();

            if (query != '') {
                $.ajax({
                    url: "{{ route('getAutoSuggestionTour') }}",
                    method: "GET",
                    data: {
                        term: query
                    },
                    success: function(data) {

                        var nameSuggestions = '';
                        var emailSuggestions = '';
                        var phoneSuggestions = '';
                        var addedSuggestions = {}; // Object to keep track of added suggestions

                        $.each(data.data, function(key, user) {
                            if (user.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !
                                addedSuggestions[user.name]) {
                                nameSuggestions += '<li class="searchs" data-name="' + user
                                    .name + '">' + user.name + '</li>';
                                addedSuggestions[user.name] =
                                true; // Marking suggestion as added
                            }
                            if (user.email.toLowerCase().indexOf(query.toLowerCase()) !== -1 &&
                                !addedSuggestions[user.email]) {
                                emailSuggestions += '<li class="searchs" data-email="' + user
                                    .email + '">' + user.email + '</li>';
                                addedSuggestions[user.email] =
                                true; // Marking suggestion as added
                            }
                            var phoneString = user.phone.toString();
                            if (phoneString.indexOf(query) !== -1 && !addedSuggestions[user
                                    .phone]) {
                                phoneSuggestions += '<li class="searchs" data-phone="' + user
                                    .phone + '">' + user.phone + '</li>';
                                addedSuggestions[user.phone] =
                                true; // Marking suggestion as added
                            }
                        });

                        var allSuggestions = '';
                        if (nameSuggestions !== '') {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Name</strong></span><ul>' +
                                nameSuggestions + '</ul></div>';
                        }
                        if (emailSuggestions !== '') {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Email</strong></span><ul>' +
                                emailSuggestions + '</ul></div>';
                        }
                        if (phoneSuggestions !== '') {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Phone</strong></span><ul>' +
                                phoneSuggestions + '</ul></div>';
                        }
                        if (allSuggestions !== '') {
                            $('#userSuggestions').fadeIn();
                            $('#userSuggestions').html(allSuggestions);
                        } else {
                            $('#userSuggestions').fadeOut();
                        }

                        createPaginationLinks(data);
                    }
                });
            } else {
                $('#userSuggestions').fadeOut();
            }

        });

        $('#userSearch').on('input', function() {
            var inputValue = $(this).val();
            if (inputValue == '') {
                nextPageData("gettourdata");
            }
        })

        $(document).on('click', 'li', function() {
            $('#userSuggestions').fadeOut();
        });

        $(document).on('click', '.searchs', function() {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var phone = $(this).data('phone');
            if (name) {
                $('#userSearch').val(name);
            } else if (email) {
                $('#userSearch').val(email);
            } else if (phone) {
                $('#userSearch').val(phone);
            }
            $.ajax({
                url: "{{ route('getAutoQuerytour') }}",
                method: "GET",
                data: {
                    name: name,
                    email: email,
                    phone: phone
                },
                success: function(response) {
                    if (response.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
                    var html = '';
                    $.each(response, function(index, tour) {
                        html += '<tr>';
                        html += '<td>' + (tour.sno !== null && tour.sno !== undefined ? tour
                            .sno : '') + '</td>';
                        html += '<td>' + ((tour.name !== null && tour.name !== undefined) ? tour
                            .name : '') + '</td>';
                        html += '<td>' + ((tour.email !== null && tour.email !== undefined) ?
                            tour.email : '') + '</td>';
                        html += '<td>' + ((tour.phone !== null && tour.phone !== undefined) ?
                            tour.phone : '') + '</td>';
                        html += '<td>' + ((tour.message !== null && tour.message !==
                            undefined) ? tour.message : '') + '</td>';
                        html += '<td>' + ((tour.time !== null && tour.time !== undefined) ? tour
                            .time : '') + '</td>';
                        html += '<td>' + ((tour.date !== null && tour.date !== undefined) ? tour
                            .date : '') + '</td>';
                        var createdAtDate = (tour.created_at !== null && tour.created_at !==
                            undefined) ? new Date(tour.created_at) : null;
                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                            createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td>' + formattedCreatedAt + '</td>';

                        html += '</tr>';

                    });
                    $('#agentTable tbody').html(html);

                    createPaginationLinks(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $(document).ready(function() {
            nextPageData("gettourdata");
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });

        function nextPageData(nextPageUrl) {
            $.ajax({
                url: nextPageUrl,
                method: 'GET',
                success: function(response) {
                    if (response.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
                    console.log('response: ', response);
                    var html = '';
                    $('#pagination_wrap').css('display', 'block');
                    $.each(response.data, function(index, tour) {
                        html += '<tr>';
                        html += '<td>' + (tour.sno !== null && tour.sno !== undefined ? tour.sno : '') +
                            '</td>';
                        html += '<td>' + ((tour.name !== null && tour.name !== undefined) ? tour.name :
                            '') + '</td>';
                        html += '<td>' + ((tour.email !== null && tour.email !== undefined) ? tour
                            .email : '') + '</td>';
                        html += '<td>' + ((tour.phone !== null && tour.phone !== undefined) ? tour
                            .phone : '') + '</td>';
                        html += '<td>' + ((tour.message !== null && tour.message !== undefined) ? tour
                            .message : '') + '</td>';
                        html += '<td>' + ((tour.time !== null && tour.time !== undefined) ? tour.time :
                            '') + '</td>';
                        html += '<td>' + ((tour.date !== null && tour.date !== undefined) ? tour.date :
                            '') + '</td>';
                        var createdAtDate = (tour.created_at !== null && tour.created_at !==
                            undefined) ? new Date(tour.created_at) : null;
                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                            createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td>' + formattedCreatedAt + '</td>';

                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);

                    createPaginationLinks(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $('#userSearch').keyup(function(event) {
            var query = $(this).val();
            if (event.keyCode === 13) {
                search(query);
            }
        });

        function search(query) {
            $.ajax({
                url: "{{ route('getsearchautoquerytour') }}",
                method: "GET",
                data: {
                    term: query
                },
                success: function(response) {
                    if (response.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
                    var html = '';
                    var serialNumber = 1; // Initialize serial number
                    $.each(response.data, function(index, tour) {
                        html += '<tr>';
                        html += '<td>' + serialNumber++ + '</td>';
                        html += '<td>' + ((tour.name !== null && tour.name !== undefined) ? tour.name :
                            '') + '</td>';
                        html += '<td>' + ((tour.email !== null && tour.email !== undefined) ? tour
                            .email : '') + '</td>';
                        html += '<td>' + ((tour.phone !== null && tour.phone !== undefined) ? tour
                            .phone : '') + '</td>';
                        
                        html += '<td>' + ((tour.message !== null && tour.message !== undefined) ? tour
                            .message : '') + '</td>';

                        html += '<td>' + ((tour.time !== null && tour.time !== undefined) ? tour.time :
                            '') + '</td>';
                        html += '<td>' + ((tour.date !== null && tour.date !== undefined) ? tour.date :
                            '') + '</td>';
                        var createdAtDate = (tour.created_at !== null && tour.created_at !==
                            undefined) ? new Date(tour.created_at) : null;
                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                            createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td>' + formattedCreatedAt + '</td>';

                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);
                    $('#pagination_wrap').css('display', 'none');

                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }



        $('#datetype').change(function() {
            var dateType = $(this).val();
            var datepicket = $('#demo-datepicker').val('');
            if (dateType != "custom" && dateType != "") {
                var endDate = new Date();
                var startDate = new Date();
                startDate.setDate(startDate.getDate() - dateType);
                startDateFormatted = startDate.toISOString().split('T')[0];
                endDateFormatted = endDate.toISOString().split('T')[0];
                console.log("endDate", endDateFormatted);
                console.log("startDate", startDateFormatted);
                getTourDataByDate(startDateFormatted, endDateFormatted);
            } else if (dateType == "") {
                nextPageData("gettourdata");
            }
        });
        $('#demo-datepicker').change(function() {
            var selectedDates = $(this).val().split(' to ');
            var startDate = selectedDates[0];
            var endDate = selectedDates[1];
            console.log("startDate", startDate);
            getTourDataByDate(startDate, endDate);
        });


        function getTourDataByDate(startDate, endDate) {
            if (endDate !== undefined) {
                $.ajax({
                    url: "{{ route('getTourDateData') }}",
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.data.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
                        var html = '';
                        if (response.data.length > 0) {
                            $.each(response.data, function(index, tour) {
                                html += '<tr>';
                                html += '<td>' + (tour.sno !== null && tour.sno !== undefined ? tour
                                    .sno : '') + '</td>';
                                html += '<td>' + (tour.name !== null && tour.name !== undefined ? tour
                                    .name : '') + '</td>';
                                html += '<td>' + (tour.email !== null && tour.email !== undefined ? tour
                                    .email : '') + '</td>';
                                html += '<td>' + (tour.phone !== null && tour.phone !== undefined ? tour
                                    .phone : '') + '</td>';
                                html += '<td>' + (tour.message !== null && tour.message !== undefined ?
                                    tour.message : '') + '</td>';
                                html += '<td>' + (tour.time !== null && tour.time !== undefined ? tour
                                    .time : '') + '</td>';
                                html += '<td>' + (tour.date !== null && tour.date !== undefined ? tour
                                    .date : '') + '</td>';

                                var createdAtDate = (tour.created_at !== null && tour.created_at !==
                                    undefined) ? new Date(tour.created_at) : null;
                                var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                                    createdAtDate)) ? createdAtDate.toDateString() : '';

                                html += '<td>' + formattedCreatedAt + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="8">No records found</td></tr>';
                        }

                        $('#agentTable tbody').html(html);
                        createPaginationLinks(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        }



        function createPaginationLinks(response) {
            var paginationHtml = '<ul class="pagination">';
            $.each(response.links, function(index, link) {
                if (link.url) {
                    paginationHtml +=
                        '<li class="page-item' + (link.active ? ' active' :
                            '') +
                        '"><a class="page-link" onClick="nextPageData(\'' +
                        link.url + '\')" href="javascript:void(0)">' +
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

        function sortTableByColumn(table, column, asc = true) {
            const dirModifier = asc ? 1 : -1;
            const tBody = table.tBodies[0];
            const rows = Array.from(tBody.querySelectorAll("tr"));

            const sortedRows = rows.sort((a, b) => {
                let aColText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
                let bColText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();

                // Parse numbers if the column contains numerical values
                if (!isNaN(parseFloat(aColText)) && isFinite(aColText)) {
                    aColText = parseFloat(aColText);
                    bColText = parseFloat(bColText);
                }

                return aColText > bColText ? 1 * dirModifier : -1 * dirModifier;
            });

            while (tBody.firstChild) {
                tBody.removeChild(tBody.firstChild);
            }

            tBody.append(...sortedRows);

            table.querySelectorAll("th").forEach(th => th.classList.remove("th-sort-asc", "th-sort-desc"));

            const currentHeaderCell = table.querySelector(`th:nth-child(${column + 1})`);
            currentHeaderCell.classList.toggle("th-sort-asc", asc);
            currentHeaderCell.classList.toggle("th-sort-desc", !asc);
        }


        document.querySelectorAll("#agentTable th[data-sort]").forEach(headerCell => {
            headerCell.addEventListener("click", () => {
                const tableElement = headerCell.closest("table");
                const column = Array.from(headerCell.parentNode.children).indexOf(headerCell);
                const currentIsAscending = headerCell.classList.contains("th-sort-asc");
                sortTableByColumn(tableElement, column, !currentIsAscending);
            });
        });



        document.addEventListener("DOMContentLoaded", function() {
            var datetypeSelect = document.getElementById("datetype");
            datetypeSelect.value = "";
            var dateFilterSelect = document.getElementById("datetype");
            var customDateContainer = document.getElementById("custom-date-container");

            dateFilterSelect.addEventListener("change", function() {
                if (this.value === "custom") {
                    customDateContainer.style.display = "block";
                } else {
                    customDateContainer.style.display = "none";
                }
            });
        });




        $(document).ready(function() {
            $('#reset-btn').click(function() {
             
                $('#userSearch').val('');
                $('#datetype').val('');
                var datePickerInput = document.getElementById('demo-datepicker');
                var flatpickrInstance = datePickerInput._flatpickr;
                var customDateContainer = document.getElementById("custom-date-container");

                flatpickrInstance.clear();
                nextPageData("gettourdata");
                customDateContainer.style.display = "none";
            });
        });
    </script>

</body>

</html>