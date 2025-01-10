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
.btn:hover {
    background-color: #07579F !important;
}
.btn-check:checked+.btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check)+.btn:active {
    background-color: #07579F;
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
</style>

<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'Leads'])

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
                                            <button type="button" class="btn btn-success add-btn"
                                                id="reset-btn">Reset</button>
                                        </div>
                                    </div>
                                </div>


                                <div class="card-body">
                                    <div>
                                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All"
                                                    href="#home1" role="tab" aria-selected="true">
                                                    <i class="ri-store-2-fill me-1 align-bottom"></i> All Leads
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

                                                        <th class="sort" data-sort="position">Role</th>

                                                        <th data-sort="action">Date</th>
                                                    </tr>

                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @if (sizeof($leads) == 0)
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
                                                        {{ $total_leads }}+ leads We
                                                        did not find any leads for you search.</p>
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
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });

        $('#userSearch').keyup(function(event) {
            var query = $(this).val();

            if (query !== '' && event.which !== 13) {
                $.ajax({
                    url: "{{ route('getautosuggestionleads') }}",
                    method: "GET",
                    data: {
                        term: query,
                    },
                    success: function(data) {

                        var nameSuggestions = new Set();
                        var emailSuggestions = new Set();
                        var phoneSuggestions = new Set();

                        $.each(data, function(key, user) {
                            if (user.name && user.name.toLowerCase().indexOf(query
                                .toLowerCase()) !== -1) {
                                nameSuggestions.add(user.name);
                            }
                            if (user.email && user.email.toLowerCase().indexOf(query
                                    .toLowerCase()) !== -1) {
                                emailSuggestions.add(user.email);
                            }
                            if (user.phone && user.phone.toString().indexOf(query) !== -1) {
                                phoneSuggestions.add(user.phone);
                            }
                        });

                        var allSuggestions = '';

                        if (nameSuggestions.size > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Name</strong></span><ul>';
                            nameSuggestions.forEach(function(name) {
                                allSuggestions += '<li class="searchs" data-name="' + name +
                                    '">' + name + '</li>';
                            });
                            allSuggestions += '</ul></div>';
                        }
                        if (emailSuggestions.size > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Email</strong></span><ul>';
                            emailSuggestions.forEach(function(email) {
                                allSuggestions += '<li class="searchs" data-email="' + email +
                                    '">' + email + '</li>';
                            });
                            allSuggestions += '</ul></div>';
                        }
                        if (phoneSuggestions.size > 0) {
                            allSuggestions +=
                                '<div class="category"><span class="category-heading"><strong>Phone</strong></span><ul>';
                            phoneSuggestions.forEach(function(phone) {
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

        function performSearch(query) {

            $.ajax({
                url: "{{ route('getautoqueryleads') }}",
                method: "GET",
                data: {
                    name: query,
                    email: query,
                    phone: query

                },
                success: function(response) {
                    console.log(response);
                    if (response.leads.data.length === 0) {
                        var html = '';
                        $('#pagination_wrap').hide();
                        $('.noresult').show();
                        $('#agentTable tbody').empty();
                        return;
                    }
                    $('.noresult').hide();
                    $.each(response.leads.data, function(index, agent) {
                        html += '<tr>';
                        html += '<td class="id">' + agent.ids + '</td>';
                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.name + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.email + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        html += '<td class="phone">' + (agent.phone !== null ?
                            agent.phone : '') + '</td>';

                        html += '<td class="role">' + (agent.role !== null ?
                            agent.role : '') + '</td>';



                        var createdAtDate = (agent.created_at !== null && agent.created_at !==
                            undefined) ? new Date(agent.created_at) : null;

                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                            createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td class="date">' + formattedCreatedAt + '</td>';



                        html += '</tr>';

                    });
                    $('#agentTable tbody').html(html);
                    createPaginationLinks(response.leads);

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

       

        $('#reset-btn').click(function() {
            $('#userSearch').val('');
            nextPageData("getleads");
        });




        window.load = nextPageData("getleads")

        function nextPageData(nextPageUrl) {
            var query = $('#userSearch').val();


            $.ajax({
                url: nextPageUrl,
                method: 'GET',
                data: {
                    name: query,
                    email: query,
                    phone: query

                },

                success: function(response) {
                    console.log('response: ', response);
                    var html = '';
                    // var idCounter = 1; 

                    if (response.leads.data.length === 0) {
                        $('.noresult').show();
                        $('#agentTable tbody').empty();
                        return;
                    }
                    $('.noresult').hide();
                    $.each(response.leads.data, function(index, agent) {


                        html += '<tr>';
                        html += '<td class="id">' + agent.ids + '</td>';
                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.name + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        if (agent.email !== null) {
                            html += '<td class="email">' + agent.email + '</td>';
                        } else {
                            html += '<td class="email"> </td>';

                        }

                        html += '<td class="phone">' + (agent.phone !== null ?
                            agent.phone : '') + '</td>';

                        html += '<td class="role">' + (agent.role !== null ?
                            agent.role : '') + '</td>';



                        var createdAtDate = (agent.created_at !== null && agent.created_at !==
                            undefined) ? new Date(agent.created_at) : null;

                        var formattedCreatedAt = (createdAtDate instanceof Date && !isNaN(
                            createdAtDate)) ? createdAtDate.toDateString() : '';

                        html += '<td class="date">' + formattedCreatedAt + '</td>';



                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);
                    createPaginationLinks(response.leads);

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


        $('#userSearch').on('input', function(e) {
            var inputValue = $(this).val();

            if (inputValue == '') {
                nextPageData('getleads');
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