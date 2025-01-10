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

    .btn-success {
    background-color: #07579F;
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

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'Contact Us'])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="leadsList">

                            <div class="row g-4 align-items-center">
                                        <div style="position: relative; padding:3%; margin-top:-5px;" class="col-sm-4">
                                            <div class="search-box">
                                                <input type="text" id="userSearch" name="userSearch"
                                                    class="form-control search" placeholder="Search for...">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                            <div id="userSuggestions"

                                            style="position: absolute; top: 67%; left: 40px; max-height: 150px; overflow-y: auto; z-index: 100; width: 290px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-sm-4 "  style="margin-top:-5px">
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
                                    <div class="col-sm-3" id="custom-date-container" style="display: none; margin-top:-10px;">
                                        <div>
                                            <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y"
                                                data-range-date="true" id="demo-datepicker" placeholder="Select Custom Date">
                                        </div>
                                    </div>
                                    <div class="col-sm-auto ms-auto">
                                       <button type="button" class="btn btn-success add-btn" id="reset-btn" style="margin-top: -30px; margin-left: -30%;">Reset</button>
                                  </div>
                                      
                                    </div>




                            
                                <div class="card-body">
                                    <div>
                                        <div class="table-responsive table-card">



                                            <table class="table align-middle table-sortable" id="agentTable">
                                                <thead class="table-light">
                                                  
                                                    <tr>

                                                        <th class="sort" data-sort="id">Sr.no</th>
                                                        <th class="sort" data-sort="first_name">Name</th>
                                                        <th class="sort" data-sort="email">Email</th>
                                                        <th class="sort" data-sort="phone">Phone</th>
                                                        <th class="sort" data-sort="role">Role</th>
                                                        <th class="sort" data-sort="comment">Comment</th>
                                                        <!-- <th class="sort" data-sort="time">Time</th> -->
                                                       <th  class="sort" data-sort="createdate">Created_at</th>
                                                       
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js">

    </script>
   <script>
   $('#userSearch').keyup(function() {
    var query = $(this).val();

    if (query !== '') {
        $.ajax({
            url: "{{ route('getAutoSuggestionContactus') }}",
            method: "GET",
            data: { term: query },
            success: function(data) {
                var nameSuggestions = new Set();
                var emailSuggestions = new Set();
                var phoneSuggestions = new Set();

                $.each(data, function(key, user) {
                    if (user.name && user.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !nameSuggestions.has(user.name)) {
                        nameSuggestions.add(user.name);
                    }
                    if (user.email && user.email.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !emailSuggestions.has(user.email)) {
                        emailSuggestions.add(user.email);
                    }
                    if (user.phone && user.phone.toString().indexOf(query) !== -1 && !phoneSuggestions.has(user.phone)) {
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

                createPaginationLinks(data);
            }
        });
    } else {
        $('#userSuggestions').fadeOut();
    }
});
$(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });

    
    $('#userSearch').on('input', function() {
            var inputValue = $(this).val();
            if (inputValue == '') {
                nextPageData("getcontactusdata");
            }
        })
        
    $(document).on('click', 'li', function() {
        $('#userSuggestions').fadeOut();
    });

    $('#userSearch').keyup(function(event) {
    var query = $(this).val();

    if (event.keyCode === 13) {
        search(query);
    }
});



function search(query) {
    $.ajax({
            url: "{{ route('getAutosearchQueryContactUs') }}",
            method: "GET",
            data: { term: query },
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
                $.each(response.data, function(index, contact) {
                    html += '<tr>';
                    html += '<td>' + (contact.sno !== null && contact.sno !== undefined ? contact.sno : '') + '</td>';
                    html += '<td>' + ((contact.first_name !== null && contact.first_name !== undefined) ? contact.first_name : '') + '</td>';
                    html += '<td>' + ((contact.email !== null && contact.email !== undefined) ? contact.email : '') + '</td>';
                    var formattedPhone = contact.phone ? contact.phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') : '';
                    html += '<td>' + formattedPhone + '</td>';
                    html += '<td>' + ((contact.role !== null && contact.role !== undefined) ? contact.role : '') + '</td>';

                    html += '<td style="max-width: 200px; word-wrap: break-word;">' + 
    ((contact.comment !== null && contact.comment !== undefined) ? contact.comment : '') + 
    '</td>';

                 // html += '<td>' + ((contact.time !== null && contact.time !== undefined) ? contact.time : '') + '</td>';

                    var createdAtDate = (contact.created_at !== null && contact.created_at !== undefined) ? new Date(contact.created_at) : null;
                    var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(createdAtDate)) ? createdAtDate.toDateString() : '';

                    html += '<td>' + formattedCreatedAt + '</td>';

                   
                    // html += '<td></td>';
                    html += '</tr>';
                });
                $('#agentTable tbody').html(html);
                $('.pagination').css('display', 'none');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
}



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
            url: "{{ route('getAutoQueryContactUs') }}",
            method: "GET",
            data: { name: name, email: email, phone: phone },
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
                $.each(response, function(index, contact) {
                    html += '<tr>';
                    html += '<td>' + (contact.sno !== null && contact.sno !== undefined ? contact.sno : '') + '</td>';
                    html += '<td>' + ((contact.first_name !== null && contact.first_name !== undefined) ? contact.first_name : '') + '</td>';
                    html += '<td>' + ((contact.email !== null && contact.email !== undefined) ? contact.email : '') + '</td>';
                    var formattedPhone = contact.phone ? contact.phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') : '';
                    html += '<td>' + formattedPhone + '</td>';
                    html += '<td>' + ((contact.role !== null && contact.role !== undefined) ? contact.role : '') + '</td>';

                    html += '<td style="max-width: 200px; word-wrap: break-word;">' + 
    ((contact.comment !== null && contact.comment !== undefined) ? contact.comment : '') + 
    '</td>';
                 // html += '<td>' + ((contact.time !== null && contact.time !== undefined) ? contact.time : '') + '</td>';

                    var createdAtDate = (contact.created_at !== null && contact.created_at !== undefined) ? new Date(contact.created_at) : null;
                    var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(createdAtDate)) ? createdAtDate.toDateString() : '';

                    html += '<td>' + formattedCreatedAt + '</td>';

                   
                    // html += '<td></td>';
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

      window.load = nextPageData("getcontactusdata")
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

                        $.each(response.data, function(index, contact) {
                            html += '<tr>';
        html += '<td>' + (contact.sno !== null && contact.sno !== undefined ? contact.sno : '') + '</td>';
        html += '<td>' + ((contact.first_name !== null && contact.first_name !== undefined) ? contact.first_name : '') + '</td>';
        html += '<td>' + ((contact.email !== null && contact.email !== undefined) ? contact.email : '') + '</td>';
        var formattedPhone = contact.phone ? contact.phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') : '';
        html += '<td>' + formattedPhone + '</td>';
        html += '<td>' + ((contact.role !== null && contact.role !== undefined) ? contact.role : '') + '</td>';
        html += '<td style="max-width: 200px; word-wrap: break-word;">' + 
    ((contact.comment !== null && contact.comment !== undefined) ? contact.comment : '') + 
    '</td>';

        // html += '<td>' + ((contact.time !== null && contact.time !== undefined) ? contact.time : '') + '</td>';

        var createdAtDate = (contact.created_at !== null && contact.created_at !== undefined) ? new Date(contact.created_at) : null;
        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(createdAtDate)) ? createdAtDate.toDateString() : '';

        html += '<td>' + formattedCreatedAt + '</td>';
        html += '</tr>';


                        });
                        $('#agentTable tbody').html(html);
                        createPaginationLinks(response)
                    
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
       }     


  $('#datetype').change(function(){
        var dateType = $(this).val();
        var datepicket = $('#demo-datepicker').val('');
        if(dateType!="custom" && dateType!=""){
         var endDate = new Date();
        var startDate = new Date();
        startDate.setDate(startDate.getDate() - dateType); // Convert to integer
        var startDateFormatted = startDate.toISOString().split('T')[0];
        var endDateFormatted = endDate.toISOString().split('T')[0];
        console.log("endDate", endDateFormatted);
        console.log("startDate", startDateFormatted);
        getcontactusDataByDate(startDateFormatted, endDateFormatted);
    } else if(dateType == ""){
        nextPageData("getcontactusdata");
        var datePickerInput = document.getElementById('demo-datepicker');
                var flatpickrInstance = datePickerInput._flatpickr;


                flatpickrInstance.clear();
    }
});

$('#demo-datepicker').change(function() {
    var selectedDates = $(this).val().split(' to ');
    var startDate = selectedDates[0];
    var endDate = selectedDates[1];
    console.log("startDate", startDate);
    getcontactusDataByDate(startDate, endDate);
});

function getcontactusDataByDate(startDate, endDate){
    if (endDate !== undefined) {
        $.ajax({
            url: "{{ route('getContactUsDateData') }}", // Corrected route name
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
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
                if (response.data.length > 0) {
                    $.each(response.data, function(index, contact) {
                        html += '<tr>';
                        html += '<td>' + (contact.sno !== null && contact.sno !== undefined ? contact.sno : '') + '</td>';
                        html += '<td>' + ((contact.first_name !== null && contact.first_name !== undefined) ? contact.first_name : '') + '</td>';
                        html += '<td>' + ((contact.email !== null && contact.email !== undefined) ? contact.email : '') + '</td>';
                        var formattedPhone = contact.phone ? contact.phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') : '';
                        html += '<td>' + formattedPhone + '</td>';
                        html += '<td>' + ((contact.role !== null && contact.role !== undefined) ? contact.role : '') + '</td>';
                        html += '<td style="max-width: 200px; word-wrap: break-word;">' + 
    ((contact.comment !== null && contact.comment !== undefined) ? contact.comment : '') + 
    '</td>';
                        // html += '<td>' + ((contact.time !== null && contact.time !== undefined) ? contact.time : '') + '</td>';

                        var createdAtDate = (contact.created_at !== null && contact.created_at !== undefined) ? new Date(contact.created_at) : null;
                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td>' + formattedCreatedAt + '</td>';

                        html += '</tr>';
                    });
                } else {
                     $('.noresult').show();
                    $('#pagination_wrap').hide();
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

        document.addEventListener("DOMContentLoaded", function () {
        var datetypeSelect = document.getElementById("datetype");
        datetypeSelect.value = "";
        var dateFilterSelect = document.getElementById("datetype");
        var customDateContainer = document.getElementById("custom-date-container");

        dateFilterSelect.addEventListener("change", function () {
            if (this.value === "custom") {
                customDateContainer.style.display = "block";
            } else {
                customDateContainer.style.display = "none";
            }
        });
    });


    $(document).ready(function(){
        var customDateContainer = document.getElementById("custom-date-container");

        $('#reset-btn').click(function(){
            $('#userSearch').val('');
            $('#datetype').val('');
            var datePickerInput = document.getElementById('demo-datepicker');
                var flatpickrInstance = datePickerInput._flatpickr;


                flatpickrInstance.clear();
            customDateContainer.style.display = "none";
            nextPageData("getcontactusdata");
        });
    });

    </script>

</body>

</html>