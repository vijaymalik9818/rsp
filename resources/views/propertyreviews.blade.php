@include('layouts.session')
@include('layouts.main')

<head>

    @include('layouts.title-meta', ['title' => 'Realtor&#174;'])
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha384-z1E2d6vS+7N4waUuqzd0xyBdJRXTIz5W8pmnArOqkKjIwePEnQ/PPCzDYO+Mfpn8" crossorigin="anonymous">

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
</style>

<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'Property Reviews'])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="leadsList">

                                <div class="row g-4 align-items-center">
                                    <div style="position: relative; padding:3%; margin-top:-5px" class="col-sm-4">
                                        <div class="search-box">
                                            <input type="text" id="userSearch" name="userSearch"
                                                class="form-control search" placeholder="Search for...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                        <div id="userSuggestions"
                                            style="position: absolute; top: 67%; left: 40px; max-height: 150px; overflow-y: auto; z-index: 100; width: 290px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">
                                        </div>
                                     

                                    </div>


                                    <div class="col-sm-auto ms-auto">
                                        <button type="button" class="btn btn-success add-btn" id="reset-btn"
                                            style="margin-top: -50px; margin-left: -100%; margin-right:-22px;">Reset</button>
                                    </div>

                                </div>





                                <div class="card-body">
                                    <div>
                                        <div class="table-responsive table-card">



                                            <table class="table align-middle table-sortable" id="agentTable">
                                                <thead class="table-light">

                                                    <tr>

                                                        <th class="sort" data-sort="id">Sr.No</th>
                                                        <th class="sort" data-sort="review_from">Review From</th>
                                                        <th class="sort" data-sort="email">Email</th>
                                                        <th class="sort" data-sort="address">Address</th>

                                                        <th class="sort" data-sort="rating">Rating</th>
                                                        <th class="sort" data-sort="review">Review</th>
                                                        <th class="sort" data-sort="listing_id">Listing id</th>
                                                        <!-- <th  class="sort" data-sort="createdat">Created_at</th> -->
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

            if (query !== '') {
                $.ajax({
                    url: "{{ route('getAutoSuggestionPropertyReview') }}",
                    method: "GET",
                    data: {
                        term: query
                    },
                    success: function(data) {
                        if (data.data && data.data.length > 0) {
                            var reviewFromSuggestions = '';
                            var emailSuggestions = '';
                            var addedSuggestions = {}; // Object to keep track of added suggestions

                            $.each(data.data, function(index, propertyreview) {
                                if (propertyreview.review_from && !addedSuggestions[
                                        propertyreview.review_from]) {
                                    reviewFromSuggestions +=
                                        '<li class="searchs" data-email="' + propertyreview
                                        .review_from + '">' + propertyreview.review_from +
                                        '</li>';
                                    addedSuggestions[propertyreview.review_from] =
                                    true; // Marking suggestion as added
                                }
                                if (propertyreview.email && !addedSuggestions[propertyreview
                                        .email]) {
                                    emailSuggestions += '<li class="searchs" data-email="' +
                                        propertyreview.email + '">' + propertyreview.email +
                                        '</li>';
                                    addedSuggestions[propertyreview.email] =
                                    true; // Marking suggestion as added
                                }
                            });

                            var allSuggestions = '';
                            if (reviewFromSuggestions !== '') {
                                allSuggestions +=
                                    '<div class="category"><span class="category-heading"><strong>Review From</strong></span><ul>' +
                                    reviewFromSuggestions + '</ul></div>';
                            }
                            if (emailSuggestions !== '') {
                                allSuggestions +=
                                    '<div class="category"><span class="category-heading"><strong>Email</strong></span><ul>' +
                                    emailSuggestions + '</ul></div>';
                            }
                            if (allSuggestions !== '') {
                                $('#userSuggestions').fadeIn();
                                $('#userSuggestions').html(allSuggestions);
                            } else {
                                $('#userSuggestions').fadeOut();
                            }
                        } else {
                            $('#userSuggestions').fadeOut();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching property review suggestions:", error);
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
                nextPageData("getPropertyReviewsdata");
            }
        })
        $(document).on('click', 'li', function() {
            $('#userSuggestions').fadeOut();
        });

        $('#userSearch').keyup(function(event) {
            var query = $(this).val();
            // var email = $('.searchs').data('email');
            if (event.keyCode === 13) {
                search(query);
            }
        });

        function search(query) {
            $.ajax({
                url: "{{ route('getsearchautoquerypropertyreview') }}",
                method: "GET",
                data: {
                    term: query
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
                    var serialNumber = 1; // Initialize serial number
                    $.each(response, function(index, propertyreview) {
                        html += '<tr>';
                        html += '<td>' + serialNumber++ +
                        '</td>'; // Increment serial number for each row
                        html += '<td>' + (propertyreview.review_from ? propertyreview.review_from :
                            '') + '</td>';
                        html += '<td>' + (propertyreview.email ? propertyreview.email : '') + '</td>';
                        html += '<td>' + (propertyreview.address && propertyreview.slug_url ?
                            '<a href="https://repincbeta.site/property-detail/' + propertyreview
                            .slug_url.replace(/,/g, '') + '" target="_blank">' + propertyreview
                            .address + '</a>' : '') + '</td>';
                        html += '<td>';
                        html += displayStars(propertyreview.rating);
                        html += '</td>';
                        html += '<td>' + (propertyreview.review ? propertyreview.review : '') + '</td>';
                        html += '<td>' + (propertyreview.listing_id ? propertyreview.listing_id : '') +
                            '</td>';
                        html += '</tr>';
                    });
                    $('#agentTable tbody').html(html);
                    $('.pagination').css('display', 'none');


                },
                error: function(xhr, status, error) {
                    console.error("Error fetching property review data:", error);
                }
            });
        }


        $(document).on('click', '.searchs', function() {
            var email = $(this).data('email');
            if (email) {
                $('#userSearch').val(email);
                $.ajax({
                    url: "{{ route('getAutoQueryPropertyReview') }}", // Corrected route name
                    method: "GET",
                    data: {
                        term: email
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
                        $.each(response.data, function(index, propertyreview) {

                            html += '<tr>';
                            html += '<td>' + (propertyreview.sno !== null && propertyreview
                                .sno !== undefined ? propertyreview.sno : '') + '</td>';
                            html += '<td>' + ((propertyreview.review_from !== null &&
                                    propertyreview.review_from !== undefined) ?
                                propertyreview.review_from : '') + '</td>';
                            html += '<td>' + ((propertyreview.email !== null && propertyreview
                                .email !== undefined) ? propertyreview.email : '') + '</td>';
                            //   html += '<td>' + ((propertyreview.address !== null && propertyreview.address !== undefined) ? '<a href="https://mukeshswami.com/property-detail/' + propertyreview.address + '" target="_blank">' + propertyreview.address + '</a>' : '') + '</td>';
                            html += '<td>' + ((propertyreview.address !== null && propertyreview
                                    .address !== undefined && propertyreview.slug_url) ?
                                '<a href="https://repincbeta.site/property-detail/' + (
                                    propertyreview.slug_url.replace(/,/g, '') ||
                                    propertyreview.ListingId) + '" target="_blank">' +
                                propertyreview.address + '</a>' : '') + '</td>';

                            html += '<td>';
                            html += displayStars(propertyreview.rating);
                            html += '</td>';
                            html += '<td>' + ((propertyreview.review !== null && propertyreview
                                    .review !== undefined) ? propertyreview.review : '') +
                                '</td>';
                            html += '<td>' + ((propertyreview.listing_id !== null &&
                                    propertyreview.listing_id !== undefined) ?
                                propertyreview.listing_id : '') + '</td>';
                            html += '</tr>';
                        });
                        $('#agentTable tbody').html(html);
                        createPaginationLinks(response);
                    },
                    error: function(xhr, propertyreview, error) {
                        console.error("Error fetching property review data:",
                        error); // Improved error message
                    }
                });
            }
        });

        function displayStars(rating) {
            var wholeStars = Math.floor(rating);
            var hasHalfStar = rating - wholeStars >= 0.5;
            var starsHtml = '';
            for (var i = 1; i <= 5; i++) {
                if (i <= wholeStars) {
                    starsHtml += '<i class="ri-star-fill align-bottom"  style=" color:orange;"></i>';
                } else if (i == wholeStars + 1 && hasHalfStar) {
                    starsHtml += '<i class="ri-star-half-line align-bottom"></i>';
                } else {
                    starsHtml += '<i class="ri-star-line align-bottom"></i>';
                }
            }
            return starsHtml;
        }


        window.load = nextPageData("getPropertyReviewsdata")

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
                    $.each(response.data, function(index, propertyreview) {

                        html += '<tr>';
                        html += '<td>' + (propertyreview.sno !== null && propertyreview.sno !==
                            undefined ? propertyreview.sno : '') + '</td>';
                        html += '<td>' + ((propertyreview.review_from !== null && propertyreview
                            .review_from !== undefined) ? propertyreview.review_from : '') + '</td>';
                        html += '<td>' + ((propertyreview.email !== null && propertyreview.email !==
                            undefined) ? propertyreview.email : '') + '</td>';
                        html += '<td>' + ((propertyreview.address !== null && propertyreview.address !==
                                undefined && propertyreview.slug_url) ?
                            '<a href="https://repincbeta.site/property-detail/' + (propertyreview
                                .slug_url.replace(/,/g, '') || propertyreview.ListingId) +
                            '" target="_blank">' + propertyreview.address + '</a>' : '') + '</td>';
                        html += '<td>';
                        html += displayStars(propertyreview.rating);
                        html += '</td>';
                        html += '<td>' + ((propertyreview.review !== null && propertyreview.review !==
                            undefined) ? propertyreview.review : '') + '</td>';
                        html += '<td>' + ((propertyreview.listing_id !== null && propertyreview
                            .listing_id !== undefined) ? propertyreview.listing_id : '') + '</td>';

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



        $(document).ready(function() {
            $('#reset-btn').click(function() {
                $('#userSearch').val('');
                $('#userSuggestions').val('');

                nextPageData("getPropertyReviewsdata");
            });
        });
    </script>

</body>

</html>
