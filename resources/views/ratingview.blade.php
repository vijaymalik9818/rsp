@include('layouts.session')
@include('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => $agent->name])
    @include('layouts.head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="layout-wrapper">
        @include('layouts.menu')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('layouts.page-title', [
                        'pagetitle' => 'Realtor®',
                        'title' => 'Realtor® Ratings',
                    ])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Ratings</h4>
                                </div>

                                <div class="card-body">

                                    <div class="live-preview">
                                        <div class="table-responsive">
                                            @if ($reviews->isEmpty())
                                                <p>No reviews for this agent.</p>
                                            @else
                                                <table class="table align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Rating</th>
                                                            <th scope="col">Reviews</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($reviews as $review)
                                                            <tr>
                                                                <td>{{ $review->title }}</td>
                                                                <td>
                                                                    <div class="fs-16 align-middle text-warning">
                                                                        @for ($i = 0; $i < 5; $i++)
                                                                            @if ($review->rating - $i >= 1)
                                                                                <i class="ri-star-fill"></i>
                                                                            @elseif ($review->rating - $i > 0)
                                                                                <i class="ri-star-half-fill"></i>
                                                                            @else
                                                                                <i class="ri-star-line"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: normal;">
                                                                    {{ $review->review_feedback }}</td>
                                                                <td>
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
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-none code-view">
                                        <pre><code class="language-markup">&lt;div data-simplebar style=&quot;max-height: 220px;&quot;&gt; ... &lt;/div&gt;</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
    @include('layouts.customizer')
    @include('layouts.vendor-scripts')

    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>

    <script src="{{ asset('assets/libs/rater-js/index.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="{{ asset('assets/js/pages/rating.init.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <style>
        .fs-16 {
            font-size: 25px !important;
        }

        .success-btn .btn {
            font-size: 8px;
            padding: 5px 10px;
        }
    </style>
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
    </script>
</body>

</html>
