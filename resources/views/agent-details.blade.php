@include('layouts.session')
@include('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => $agent->name])
    <link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.head-css')
</head>

<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @include('layouts.page-title', [
                    'pagetitle' => 'REALTOR&#174;',
                    'title' => 'REALTOR&#174; Details',
                ])

                <div class="row">
                    <div class="col-md-3">
                        <div class="card position-relative">
                            <div class="card-body  mx-auto">
                                <div class="details">
                                    <?php $role = Auth::user()->role; ?>
                                    @if ($role != 1)
                                        <a href="javascript:void(0);" onclick="redirectToEdit('{{ $agent->id }}')"
                                            class="btn btn-sm edit-item-btn position-absolute top-0 end-0 mt-2 me-2"
                                            data-agent-id="{{ $agent->id }}">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    @endif



                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            @if ($agent->profile_picture)
                                                <img src="{{ asset($agent->profile_picture) }}" alt=""
                                                    height="50" />
                                            @else
                                                <img src="{{ asset('assets/images/No-Image-Placeholder.png') }}"
                                                    alt="" height="50" />
                                            @endif
                                        </div>

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td class="name">{{ $agent->name }}</td>
                                                </tr>

                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td class="email">{{ $agent->email }}</td>
                                                </tr>

                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td class="phone">
                                                        <?php if ($agent->phone): ?>
                                                        <?php
                                                        $formatted_phone = preg_replace('/[^0-9]/', '', $agent->phone);
                                                        echo '(' . substr($formatted_phone, 0, 3) . ') ' . substr($formatted_phone, 3, 3) . '-' . substr($formatted_phone, 6);
                                                        ?>
                                                        <?php else: ?>
                                                        -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                           
                                                <tr>
                                                    <th><span class="fw-medium">Office</span></th>
                                                    <td class="office">
                                                        <?php if ($agent->office_no): ?>
                                                        <?php
                                                        $formatted_office = preg_replace('/[^0-9]/', '', $agent->office_no);
                                                        echo '(' . substr($formatted_office, 0, 3) . ') ' . substr($formatted_office, 3, 3) . '-' . substr($formatted_office, 6);
                                                        ?>
                                                        <?php else: ?>
                                                        -
                                                        <?php endif; ?>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Job Title</span></th>
                                                    <td class="office">
                                                        @if ($agent->position)
                                                            {{ $agent->position }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">REALTOR&#174; Id</span></th>
                                                    <td class="mls_id">
                                                        @if (!empty($agent->mls_id))
                                                            {{ $agent->mls_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Language Spoken</span></th>
                                                    <td>
                                                        @if (!empty($agent->language))
                                                            {{ str_replace(',', ', ', $agent->language) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td class="address">
                                                        @if (!empty($agent->address))
                                                            {{ $agent->address }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ isset($agent->facebook) && $agent->facebook != '' ? $agent->facebook : 'javascript:void(0);' }}"
                                                    class="btn btn-primary waves-effect waves-light me-2"{{ isset($agent->facebook) && $agent->facebook != '' ? ' target="_blank"' : '' }}><i
                                                        class="bx bxl-facebook"></i></a>
                                                <a href="{{ isset($agent->linkedin) && $agent->linkedin != '' ? $agent->linkedin : 'javascript:void(0);' }}"
                                                    class="btn btn-outline-primary btn-icon waves-effect waves-light me-2"{{ isset($agent->linkedin) && $agent->linkedin != '' ? ' target="_blank"' : '' }}><i
                                                        class="bx bxl-linkedin-square"></i></a>
                                                <a href="{{ isset($agent->twitter) && $agent->twitter != '' ? $agent->twitter : 'javascript:void(0);' }}"
                                                    class="btn btn-outline-primary btn-icon waves-effect waves-light me-2"{{ isset($agent->twitter) && $agent->twitter != '' ? ' target="_blank"' : '' }}><i
                                                        class="bx bxl-twitter"></i></a>
                                                <a href="{{ isset($agent->instagram) && $agent->instagram != '' ? $agent->instagram : 'javascript:void(0);' }}"
                                                    class="btn btn-instagram btn-icon waves-effect me-2"{{ isset($agent->instagram) && $agent->instagram != '' ? ' target="_blank"' : '' }}><i
                                                        class="bx bxl-instagram"></i></a>
                                                <a href="{{ isset($agent->youtube) && $agent->youtube != '' ? $agent->youtube : 'javascript:void(0);' }}"
                                                    class="btn btn-danger btn-icon waves-effect waves-light"{{ isset($agent->youtube) && $agent->youtube != '' ? ' target="_blank"' : '' }}><i
                                                        class="bx bxl-youtube"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Customer Reviews</h6>
                                    <div>
                                        <div class="bg-light px-3 py-2 rounded-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="fs-16 align-middle text-warning">
                                                        @for ($i = 0; $i < 5; $i++)
                                                            @if ($averageRating - $i >= 1)
                                                                <i class="ri-star-fill"></i>
                                                            @elseif ($averageRating - $i > 0)
                                                                <i class="ri-star-half-fill"></i>
                                                            @else
                                                                <i class="ri-star-line"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <h6 class="mb-0">{{ number_format($averageRating, 1) }} out of 5
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-muted">Total <span
                                                    class="fw-medium">{{ $reviews->count() }}</span> reviews</div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        @php
                                            $stars = [
                                                ['label' => '5 star', 'count' => 0],
                                                ['label' => '4 star', 'count' => 0],
                                                ['label' => '3 star', 'count' => 0],
                                                ['label' => '2 star', 'count' => 0],
                                                ['label' => '1 star', 'count' => 0],
                                            ];

                                            foreach ($reviews as $review) {
                                                if ($review->rating > 0) {
                                                    $stars[5 - $review->rating]['count']++;
                                                }
                                            }
                                        @endphp

                                        @foreach ($stars as $star)
                                            <div class="row align-items-center g-2">
                                                <div class="col-auto">
                                                    <div class="p-1">
                                                        <h6 class="mb-0">{{ $star['label'] }}</h6>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="p-1">
                                                        <div class="progress animated-progress progress-sm">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $reviews->count() > 0 ? ($star['count'] / $reviews->count()) * 100 : 0 }}%"
                                                                aria-valuenow="{{ $reviews->count() > 0 ? ($star['count'] / $reviews->count()) * 100 : 0 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="p-1">
                                                        <h6 class="mb-0 text-muted">{{ $star['count'] }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>



                            <div class="card-body p-4 border-top border-top-dashed">
                                <h6 class="text-muted text-uppercase fw-semibold mb-4">REALTOR&#174; Reviews</h6>

                                <div class="swiper vertical-swiper" style="height: 310px;">
                                    <div class="swiper-wrapper">
                                        @if ($reviews->isEmpty())
                                            <div class="swiper-slide">
                                                <div class="card border border-dashed shadow-none">
                                                    <div class="card-body">
                                                        <p class="text-muted">No review for this REALTOR&#174;</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @foreach ($reviews as $review)
                                                <div class="swiper-slide">
                                                    <div class="card border border-dashed shadow-none">
                                                        <div class="card-body">
                                                            <div class="d-flex">
                                                                <div class="flex-shrink-0">
                                                                    <img src="/assets/images/d_img.jpg" alt=""
                                                                        class="avatar-sm rounded">

                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <div>
                                                                        <p class="text-muted mb-1 fst-italic">
                                                                            {{ $review->review_feedback }}</p>
                                                                        <div class="fs-11 align-middle text-warning">
                                                                            @for ($i = 0; $i < 5; $i++)
                                                                                @if ($review->rating - $i >= 1)
                                                                                    <i class="ri-star-fill"></i>
                                                                                @elseif($review->rating - $i > 0)
                                                                                    <i class="ri-star-half-fill"></i>
                                                                                @else
                                                                                    <i class="ri-star-line"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-end mb-0 text-muted">
                                                                        - by <cite
                                                                            title="Source Title">{{ $review->title }}</cite>
                                                                    </div>
                                                                    <div class="d-flex gap-2 success-btn">
                                                                        <button type="button"
                                                                            class="btn btn-success waves-effect waves-light confirm-btn"
                                                                            data-review-id="{{ $review->id }}"
                                                                            data-status="approve"
                                                                            style="{{ $review->status === 1 || $review->status === 2 ? 'display: none;' : '' }} ">Approve</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger waves-effect waves-light confirm-btn"
                                                                            data-review-id="{{ $review->id }}"
                                                                            data-status="decline"
                                                                            style="{{ $review->status === 1 || $review->status === 2 ? 'display: none;' : '' }}">Decline</button>
                                                                        <span class="confirmation-message"
                                                                            style="{{ $review->status === 1 || $review->status === 2 ? '' : 'display: none;' }}">
                                                                            @if ($review->status === 1)
                                                                                <span class="text-success"> Review
                                                                                    confirmed</span>
                                                                            @elseif($review->status === 2)
                                                                                <span class="text-danger"> Review
                                                                                    declined</span>
                                                                            @endif
                                                                        </span>
                                                                        <button type="button"
                                                                            class="btn btn-secondary reset-btn"
                                                                            data-review-id="{{ $review->id }}"
                                                                            style="{{ $review->status === 1 || $review->status === 2 ? '' : 'display: none;' }}">Reset</button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    @if (!$reviews->isEmpty())
                                        <a href="{{ route('ratingview', ['id' => base64_encode($agent->id)]) }}"
                                            class="link-primary">View All Reviews <i
                                                class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-9">

                        <div class="row g-4 mb-3">
                            <div class="col-sm">
                                <div class="d-flex justify-content-sm-end">
                                    <div style="position: relative" class="col-sm-3">
                                        <div class="search-box">
                                            <input type="text" id="userSearch" name="userSearch"
                                                class="form-control search" placeholder="Search for...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                        <div id="userSuggestions"
                                            style="position: absolute; top: 67%; left: -13px; max-height: 150px; overflow-y: auto; z-index: 100; width: 235px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table align-middle table-sortable" id="agentTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort" data-sort="name">MLS&#174;</th>
                                                <th class="sort" data-sort="email">Address</th>
                                                <th class="sort" data-sort="phone">City</th>
                                                <th class="sort" data-sort="address">Zip Code</th>
                                                <th class="sort" data-sort="mls_id">Price</th>
                                                <th class="sort" data-sort="position">Property</th>
                                                <th class="sort" data-sort="status">Sub Type</th>
                                                <th class="sort" data-sort="status">Status</th>
                                                <th class="sort" data-sort="status">Diamond</th>
                                                {{-- <th data-sort="action">Action</th> --}}
                                            </tr>

                                        </thead>
                                        <tbody class="list form-check-all">

                                        </tbody>
                                    </table>
                                </div>
                                <div id="pagination_wrap"></div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>

        @include('layouts.footer')
    </div>
</div>
<style>
    .btn-instagram {
        background-color: #d62976;
        border-color: pink;
        color: white;
    }

    .details {
        margin: 0px;
    }

    .avatar-md {
        height: 8.5rem;
        width: 8.5rem;
    }


    .avatar-title img {
        object-fit: contain;
        height: 100%;
        width: 100%;
    }

    .position-relative {
        position: relative;
    }

    .position-absolute {
        position: absolute;
    }

    .top-0 {
        top: 0;
    }

    .end-0 {
        right: 0;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    .me-2 {
        margin-right: 0.5rem;
    }

    .success-btn .btn {
        font-size: 8px;
        padding: 5px 10px;
    }

    .swiper-slide {
        height: auto !important;
    }

    .pagination {
        float: right;
        margin-top: 20px;
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
    
     .edit-item-btn{
        background-color: #07579F !important;
        color: #fff !important;
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
</style>
@include('layouts.customizer')
@include('layouts.vendor-scripts')

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>
<script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('assets/js/pages/seller-details.init.js') }}"></script>

<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    document.querySelectorAll('.reset-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const approveBtn = document.querySelector(
                `.confirm-btn[data-review-id="${reviewId}"][data-status="approve"]`);
            const declineBtn = document.querySelector(
                `.confirm-btn[data-review-id="${reviewId}"][data-status="decline"]`);
            const resetBtn = document.querySelector(`.reset-btn[data-review-id="${reviewId}"]`);
            // console.log(resetBtn);
            var container = $(this).closest('.success-btn');

            container.find('.confirm-btn').show();
            container.find('.confirmation-message').hide();
            approveBtn.style.display = 'inline-block';
            declineBtn.style.display = 'inline-block';
            resetBtn.style.display = 'none';
        });
    });

    $('.confirm-btn').click(function() {
        var reviewId = $(this).data('review-id');
        var status = $(this).data('status');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var container = $(this).closest('.success-btn');
        $.ajax({
            url: '/review/confirm/' + btoa(reviewId),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                status: status
            },
            success: function(response) {
                container.find('.confirm-btn').hide();
                container.find('.reset-btn').show();
                if (status === 'approve') {
                    container.find('.confirmation-message').text('Review confirmed').addClass(
                        'text-success').show();
                } else if (status === 'decline') {
                    container.find('.confirmation-message').text('Review declined').addClass(
                        'text-danger').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating review');
            }
        });
    });

    $('#userSearch').keyup(function(event) {
        var query = $(this).val();
        var agentId = {{ $agent->id }};

        if (query != '' && event.which !== 13) {
            $.ajax({
                url: "/getpropertysuggestion/" + btoa(agentId),
                method: "GET",
                data: {
                    term: query,
                },
                success: function(response) {
                    var data = response.properties;
                    var mlsidSuggestion = '';
                    var addressSuggestion = '';
                    var citySuggestion = '';

                    if (data && data.length > 0) {
                        $.each(data, function(key, user) {
                            if (user && user.ListingId && user.ListingId.toLowerCase()
                                .indexOf(query.toLowerCase()) !== -1) {
                                mlsidSuggestion += '<li class="searchs" data-listingid="' +
                                    user
                                    .ListingId + '">' + user.ListingId + '</li>';
                            }
                            if (user && user.UnparsedAddress && user.UnparsedAddress
                                .toLowerCase().indexOf(query.toLowerCase()) !== -1) {
                                addressSuggestion += '<li class="searchs" data-address="' +
                                    user
                                    .UnparsedAddress + '">' + user.UnparsedAddress +
                                    '</li>';
                            }
                            if (user && user.City && user.City.toLowerCase().indexOf(query
                                    .toLowerCase()) !== -1) {
                                citySuggestion += '<li class="searchs" data-city="' + user
                                    .City + '">' + user.City + '</li>';
                            }
                        });
                    }

                    var allSuggestions = '';
                    if (mlsidSuggestion !== '') {
                        allSuggestions +=
                            '<div class="category"><span class="category-heading"><strong>MLS&#174;</strong></span><ul>' +
                            mlsidSuggestion + '</ul></div>';
                    }
                    if (addressSuggestion !== '') {
                        allSuggestions +=
                            '<div class="category"><span class="category-heading"><strong>Address</strong></span><ul>' +
                            addressSuggestion + '</ul></div>';
                    }
                    if (citySuggestion !== '') {
                        allSuggestions +=
                            '<div class="category"><span class="category-heading"><strong>City</strong></span><ul>' +
                            citySuggestion + '</ul></div>';
                    }
                    if (allSuggestions !== '') {
                        $('#userSuggestions').html(allSuggestions).fadeIn();
                    } else {
                        $('#userSuggestions').fadeOut().empty();
                    }
                }
            });
        } else {
            $('#userSuggestions').fadeOut().empty();
        }
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#userSuggestions').length) {
            $('#userSuggestions').fadeOut().empty();
        }
    });

    $(document).on('click', 'li.searchs', function() {
        $('#userSearch').val($(this).text());
        $('#userSuggestions').fadeOut().empty();
    });


    $(document).on('click', '.searchs', function() {
        var query = $(this).data('listingid') || $(this).data('address') || $(this).data(
            'city');
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
    $('#userSearch').on('input', function(e) {
        var inputValue = $(this).val();
        var agentId = {{ $agent->id }};
        if (inputValue == '') {
            nextPageData("/properties/" + btoa(agentId));
        }
    })

    function redirectToEdit(agentId) {
        const encodedAgentId = btoa(agentId.toString());
        const editUrl = 'edit/' + encodedAgentId;
        window.location.href = editUrl;
    }



    window.onload = function() {
        var agentId = {{ $agent->id }};
        nextPageData("/properties/" + btoa(agentId));
    };


    function performSearch(query) {
        var agentId = {{ $agent->id }};
        $.ajax({
            url: "/getpropertyquery/" + btoa(agentId),
            method: "GET",
            data: {
                listingid: query,
                address: query,
                city: query,
            },
            success: function(response) {
                var html = '';

                if (!response || !response.data || response.data.length === 0) {
                    html = '<tr><td colspan="9" class="text-center">No properties found</td></tr>';
                } else {
                    $.each(response.data, function(index, property) {
                        var columnData = property.OtherColumns;
                        var data = JSON.parse(columnData);
                        var postalCode = data.PostalCode;

                        html += '<tr>';
                        html += '<td class="mls">' + property.ListingId + '</td>';
                        html += '<td class="address">' + property.UnparsedAddress + '</td>';
                        html += '<td class="address">' + property.City + '</td>';
                        html += '<td class="address">' + postalCode + '</td>';
                        var formattedPrice = '$' + Number(property.ListPrice).toLocaleString();
                        html += '<td class="listprice">' + formattedPrice + '</td>';
                        html += '<td class="listprice">' + property.PropertyType + '</td>';
                        html += '<td class="subtype">' + property.PropertySubType + '</td>';
                        html += '<td class="status">';
                        html += property.MlsStatus == 'Active' ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                        html += '<td class="diamond">';
                        html += property.diamond == 1 ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Yes</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">No</span>';
                        html += '</tr>';
                    });
                }

                $('#agentTable tbody').html(html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });


    }

    function nextPageData(url) {

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                var html = '';

                if (!response || !response.property || !response.property.data || response.property.data
                    .length === 0) {
                    html = '<tr><td colspan="9" class="text-center">No properties found</td></tr>';
                } else {
                    $.each(response.property.data, function(index, property) {
                        var columnData = property.OtherColumns;
                        var data = JSON.parse(columnData);
                        var postalCode = data.PostalCode;

                        html += '<tr>';
                        html += '<td class="mls">' + property.ListingId + '</td>';
                        html += '<td class="address">' + property.UnparsedAddress + '</td>';
                        html += '<td class="address">' + property.City + '</td>';
                        html += '<td class="address">' + postalCode + '</td>';
                        var formattedPrice = '$' + Number(property.ListPrice).toLocaleString();
                        html += '<td class="listprice">' + formattedPrice + '</td>';
                        html += '<td class="listprice">' + property.PropertyType + '</td>';
                        html += '<td class="subtype">' + property.PropertySubType + '</td>';
                        html += '<td class="status">';
                        html += property.MlsStatus == 'Active' ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Active</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">Inactive</span>';
                        html += '<td class="diamond">';
                        html += property.diamond == 1 ?
                            '<span class="badge bg-success-subtle text-success text-uppercase">Yes</span>' :
                            '<span class="badge bg-danger-subtle text-danger text-uppercase">No</span>';
                        html += '</tr>';
                    });
                }
                $('#agentTable tbody').html(html);
                createPaginationLinks(response);
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