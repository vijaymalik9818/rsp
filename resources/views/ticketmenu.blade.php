@include('layouts.session')
@include('layouts.main')

<head>
    @include('layouts.title-meta', ['title' => 'Ticket Form'])
    <!-- Sweet Alert css-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    @include('layouts.head-css')
</head>
<style>

.submitbtn, .submitbtn:hover, .submitbtn:active{
        background-color: #07579F !important;
        color: #fff !important;
    }
</style>

<body>
    <div id="layout-wrapper">
        @include('layouts.menu')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('layouts.page-title', ['pagetitle' => 'Ticket', 'title' => 'Ticket Form'])
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card p-4">
                                <form class="tablelist-form" id="tablelist" autocomplete="off" method="post"
                                action="{{ route('submit.ticket') }}" enctype="multipart/form-data">
                            
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="subject" class="form-label fw-bold" >Subject</label>
                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="query" class="form-label  fw-bold">Query</label>
                                            <textarea class="form-control" id="query" name="query" rows="5" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="attachment" class="form-label  fw-bold">Attachment</label>
                                            <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn submitbtn">Submit Ticket</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    @include('layouts.customizer')
    @include('layouts.vendor-scripts')

    <!-- list.js min js -->
    <script src="{{ asset('assets/libs/list.js/list.min.js') }}"></script>
    <!--list pagination js-->
    <script src="{{ asset('assets/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <!-- titcket init js -->
    <script src="{{ asset('assets/js/pages/tasks-list.init.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
</body>
</html>
