@include('layouts.session')
@include('layouts.main')


<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
    .error{
        color:red;
        font-size:10px;
    }
    .pagination {
        float: right;
        margin-top: 20px;

    }
 
    .edit-link{
        padding:15px;
    }

.form-check{
    padding:25px;

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
  
.btn-success {
    background-color: #07579F;
}


a {
    color: #07579F;
    text-decoration: none;
}
.update-btn, .update-btn:hover, .update-btn:active, .add-btn:hover{
    background-color: #07579F !important;  
}
</style>

<body>

    <div id="layout-wrapper">

        @include('layouts.menu')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @include('layouts.page-title', ['pagetitle' => 'CRM', 'title' => 'All City'])

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="leadsList">

                            <div class="row g-4 align-items-center justify-content-between">
                                        <div style="position: relative; padding:3%; margin-top:-5px;"  class="col-sm-4">
                                        <div class="search-box">
                                                <input type="text" id="userSearch" name="userSearch"
                                                    class="form-control search" placeholder="Search for...">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                            <div id="userSuggestions"

                                            style="position: absolute; top: 67%; left: 40px; max-height: 150px; overflow-y: auto; z-index: 100; width: 290px; background-color: white; padding: 5px; white-space: normal; margin: 13px; border:1px solid #ccc; border-top: none; display: none;">                                            </div>
                                        </div>
                                        <div class="col-sm-auto ms-auto">
                                       <button type="button" class="btn btn-success add-btn" id="reset-btn" style="margin-top: -33px;  margin-right:2rem;">Reset</button>
                                  </div>
                                    </div>
         
                                <div class="card-body">
                                    <div>
                                        <div class="table-responsive table-card">



                                            <table class="table align-middle table-sortable" id="agentTable">
                                                <thead class="table-light">
                                            
                                             <tr>

                                                <th class="sort" data-sort="id">Sr.No</th> 
                                                <th class="sort" data-sort="city">City name</th>
                                                <th class="sort" data-sort="no_of_properties ">No of Properties</th>
                                                <th>Mark as featured</th>
                                                <th>Action</th>

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
                                                            <div class="modal fade" id="showModal" tabindex="-1"
                                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                                                                        <div class="modal-content">
                                                                    <div class="modal-header bg-light p-3">
                                                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close" id="close-modal"></button>
                                                                    </div>
                                          <!-- formmm -->
                                                   <form class="tablelist-form" id="tablelist" autocomplete="off"  enctype="multipart/form-data" novalidate>
                                                          @csrf
                                                                <div class="modal-body">
                                                                    <input type="hidden" id="id-field" name="id-field" value=""/>
                                                                    <div class="text-center"></div>
                                                                    <div class="row g-3">
                                                                        <div class="col-lg-12">
                                                                            <div class="mb-3">
                                                                                <label for="city_name" class="form-label">City/Community Name<span style="color: red">*</span></label>
                                                                                <input type="text" id="city_name" name="city_name" class="form-control"  placeholder="City/Community Name" value="" readonly>
                                                                                <span class="error" id="error_city_name"></span>

                                                                            </div>
                                                                        </div>
                                                                                                                                              <div class="col-lg-12">
                                                                            <div class="mb-3">
                                                                                <label for="image" class="form-label">Image<span style="color: red">*</span></label>
                                                                                <input class="form-control" id="city_image" name="image" type="file" accept="image/png, image/gif, image/jpeg" required />
                                                                                <input type="hidden" value=""
                                                                                        id="old_city_image" name="old_city_image">
                                                                                    <img id="city_image_display" width="100"
                                                                                        alt="city Picture" style="display: none;">
                                                                                        <span class="error" id="error_city_image"></span>

                                                                            </div>
                                                                            <span class="error" id="imageSizeError"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <div class="hstack gap-2 justify-content-end">
                                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                        <button type="button" class="btn btn-success" id="updatecity"  value="Update" >Update</button>
                                                                    </div>
                                                                </div>
                                                            </form>

                                              <!-- endformm -->

                                              <div id="loader" class="loader"></div>
                                            </div>
                                        </div>
                                    </div>

       
                               <div class="modal fade zoomIn" id="statusShowModal" tabindex="-1"
                                        aria-labelledby="deleteRecordLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close" id="btn-close"></button>
                                                </div>
                                                <div class="modal-body p-5 text-center">
                                               
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="90" height="90">
                                            <path fill="#28a745" d="M0 0h24v24H0z" fill-opacity="0"/>
                                            <path fill="#F06548" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.5 8.5l-6 6-3.5-3.5 1.41-1.41L10 14.17l4.09-4.09 1.41 1.41z"/>
                                            </svg>
                                               
                                            <div class="mt-4 text-center">
                                                    <h4 class="fs-semibold">Are you sure you want to mark this City as a featured?</h4>
                                                        <p class="text-muted fs-14 mb-4 pt-1">Marking the City as featured will highlight it as a featured City.</p>
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                            <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                                id="deleteRecord-close" data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Close</button>
                                                            <button class="btn btn-success"
                                                                id="yes-btn">Yes</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.lordicon.com/libs/frhvbuzj/lord-icon-2.0.2.js"></script>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

   <script>
     const loader = document.getElementById('loader');
        loader.style.display = 'none';
        $('#userSearch').keyup(function() {
    var query = $(this).val();

    if (query !== '') {
        $.ajax({
            url: "{{ route('getAutoSuggestionCity') }}",
            method: "GET",
            data: { term: query },
            success: function(data) {
                if (data && data.length > 0) {
                    var citySet = new Set();

                    $.each(data, function(index, city) {
                        citySet.add(city);
                    });

                    var citySuggestions = '';
                    citySet.forEach(function(city) {
                        citySuggestions += '<li class="searchs" data-city="' + city + '">' + city + '</li>';
                    });

                    var allSuggestions = '';
                    if (citySuggestions !== '') {
                        allSuggestions += '<div class="category"><span class="category-heading"><strong>City</strong></span><ul>' + citySuggestions + '</ul></div>';
                    }
                    if (allSuggestions !== '') {
                        $('#userSuggestions').fadeIn();
                        $('#userSuggestions').html(allSuggestions);
                    } else {
                        $('#userSuggestions').fadeOut();
                    }

                    createPaginationLinks(response); 
                } else {
                    $('#userSuggestions').fadeOut();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching city suggestions:", error);
            }
            
        });
    } else {
        $('#userSuggestions').fadeOut();
    }
});



$('#userSearch').on('input', function() {
    var inputValue = $(this).val();
    if (inputValue === '') {
        nextPageData("{{ route('getcityData') }}");
    }
});
$('#userSearch').keyup(function(event) {
            var query = $(this).val();
            if (event.keyCode === 13) {
                search(query);
            }
        });

$(document).on('click', '.searchs', function() {
    var city = $(this).data('city');
    $('#userSearch').val(city);
    $('#userSuggestions').fadeOut();
    fetchCityData(city);
});

function search(query){
    $.ajax({
        url: "{{ route('getAutoQuerysearchCity') }}",
        method: "GET",
        data: { city_name: query },
        success: function(response) {
            if (response.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
                        if (response && response.length > 0) {
                var html = '';
                response.forEach(function(cityData) {
                    html += '<tr>';
                    html += '<td>' + (cityData.id !== null && cityData.id !== undefined ? cityData.id : '') + '</td>';
                    html += '<td>' + (cityData.City !== null && cityData.City !== undefined ? cityData.City : '') + '</td>'; 
                    html += '<td>' + (cityData.properties_count !== null && cityData.properties_count !== undefined ? cityData.properties_count : '') + '</td>'; // Corrected 'properties_count' key
                    html += '<td><input type="checkbox" name="featureCheckbox" data-city-id="' + cityData.id + '" id="property_status_' + cityData.id + '" class="form-check"   value="' + cityData.status + '" ' + (cityData.status == 1 ? 'checked' : '') + '></td>';
                    html += '<td><a href="#" class="edit-link" id="prefilldata" onclick="openEditModal(' + cityData.id + ')"><i class="fas fa-edit"></i></a></td>';                
                    html += '</tr>';
                });
                $('#agentTable tbody').html(html);
                createPaginationLinks(response); 
            } else {
                console.error("No city data found for:", city);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching city data:", error);
        }
    });
}
function fetchCityData(city) {
    $.ajax({
        url: "{{ route('getAutoQueryCity') }}",
        method: "GET",
        data: { city_name: city },
        success: function(response) {
            if (response.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
            if (response && response.length > 0) {
                var html = '';
                response.forEach(function(cityData) {
                    html += '<tr>';
                    html += '<td>' + (cityData.id !== null && cityData.id !== undefined ? cityData.id : '') + '</td>';
                    html += '<td>' + (cityData.City !== null && cityData.City !== undefined ? cityData.City : '') + '</td>'; 
                    html += '<td>' + (cityData.properties_count !== null && cityData.properties_count !== undefined ? cityData.properties_count : '') + '</td>'; // Corrected 'properties_count' key
                    html += '<td><input type="checkbox" name="featureCheckbox" data-city-id="' + cityData.id + '" id="property_status_' + cityData.id + '" class="form-check"   value="' + cityData.status + '" ' + (cityData.status == 1 ? 'checked' : '') + '></td>';
                    html += '<td><a href="#" class="edit-link" id="prefilldata" onclick="openEditModal(' + cityData.id + ')"><i class="fas fa-edit"></i></a></td>';                
                    html += '</tr>';
                });
                $('#agentTable tbody').html(html);
                createPaginationLinks(response); 
            } else {
                console.error("No city data found for:", city);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching city data:", error);
        }
    });
}

window.onload = function() {
    nextPageData("{{ route('getcityData') }}");
};

function nextPageData(nextPageUrl) {
    $.ajax({
        url: nextPageUrl,
        method: 'GET',
        success: function(response) {
            if (response.length === 0) {
                            $('.noresult').show();
                            $('#pagination_wrap').hide();
                            $('#agentTable tbody').empty();
                            return;
                        }
                   
                        $('.noresult').hide();
                        $('#pagination_wrap').show();
            console.log('response: ', response);
            var html = '';
            $.each(response.data, function(index, city) {
                html += '<tr>';
                html += '<td>' + ((city.id!== null && city.id !== undefined) ? city.id : '') + '</td>';
                html += '<td>' + ((city.city !== null && city.city !== undefined) ? city.city : '') + '</td>';
                html += '<td>' + ((city.properties_count !== null && city.properties_count !== undefined) ? city.properties_count : '') + '</td>';
                html += '<td><input type="checkbox" name="featureCheckbox" data-city-id="' + city.id + '" id="property_status_' + city.id + '" class="form-check"  value="' + city.status + '" ' + (city.status == 1 ? 'checked' : '') + '></td>';
                html += '<td><a href="javascript:void(0)" class="edit-link" id="prefilldata" onclick="openEditModal(' + city.id + ')"><i class="fas fa-edit"></i></a></td>';                
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


    function prefillform(city) {
        $("#city_image").val("");
        $('#city_name').val(city.city_name);
        if(city.image && city.image!=""){
            $('#city_image_display').attr('src', city.image);
            document.getElementById('city_image_display').style.display = 'inline-block';
        }else{
            $('#city_image_display').attr('src','');
        }

    }

    function openEditModal(cityId) {
        $('#showModal').modal('show');
        $('#id-field').val(cityId);
        getPrefillData(cityId);
    }
    function openStatusConfirmModal(cityId) {
        $('#statusShowModal').modal('show');
    }
  function getPrefillData(cityId) {
        $.ajax({
            url: '/prefill-data/' + cityId,
            method: 'GET',
            success: function(response) {
                prefillform(response.city);
            },
            error: function(xhr, status, error) {
                console.error(error);
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
            console.log("table", table);
            console.log("column", column);
            const tBody = table.tBodies[0];
            console.log("asc", asc);
            let column_name = 'id';
            let order_by = 'asc';
            if(asc!==true){
                order_by = 'desc';
            }
            if (column == 0) {
                column_name = "id";
            } else if (column == 1) {
                column_name = "city_name";
            } else if (column == 2) {
                column_name = "properties_count";
            } else {
                column_name = "id";
            }
            let endpoint = `getcityData?column=${column_name}&order_by=${order_by}`;
            nextPageData(endpoint);
            console.log(column);
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


      function updateCityStatus(cityId, cityStatus) {
            $.ajax({
                type: "POST",
                url: "api/admin/updatecitystatus",
                data: {
                    id: cityId,
                    status: cityStatus
                },
                success: function(response) {
                    console.log(response);

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        
      $('#city_image').change(function() {
      $('#imageSizeError').text("");
    document.getElementById('city_image_display').style.display = 'inline-block';
    var file = this.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
          $('#imageSizeError').text("File size exceeds the limit of 2MB.");
            $(this).val('');
            return;
        }
        var fileType = file.type.split('/').pop().toLowerCase();
        if (fileType !== 'jpg' && fileType !== 'png' && fileType !== 'jpeg') {
          $('#imageSizeError').text("Only JPG, PNG, and JPEG file types are allowed.");
            $(this).val('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            $('#city_image_display').attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
    }
});
    $('#updatecity').click(function() {
    var cityId = $('#id-field').val();
    var cityname = $('#city_name').val();
    var cityImage = $('#city_image')[0].files[0];

    if(cityname.trim() === ""){
        $('#error_city_name').text('City name is required.');
        $('#city_name').focus();
        return false;
    } else {
        $('#error_city_name').text('');
    }

    if(!cityImage){
        $('#error_city_image').text('City image is required.');
        $('#city_image').focus();
        return false;
    } else {
        $('#error_city_image').text('');
    }

    var formData = new FormData();

    formData.append('id', cityId);
    formData.append('city_name', cityname);
    formData.append('city_image', cityImage);

    $('#loader').show();
    
    $.ajax({
        url: 'api/admin/updatecity',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log(response);
            Swal.fire({
                icon: 'success',
                title: 'Update Successful!',
                showConfirmButton: false,
                timer: 1500 
            });

            $('#loader').hide();

            $('#showModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            // Hide loader in case of error
            $('#loader').hide();
        }
    });
});

$(document).on('click', function(event) {
            if (!$(event.target).closest('#userSuggestions').length) {
                $('#userSuggestions').fadeOut().empty();
            }
        });

$(document).ready(function(){
    var currentCityId;
    $(document).on('change', 'input[name="featureCheckbox"]', function(){    
        currentCityId = $(this).data('city-id');
        if($(this).is(':checked')){
            $('#statusShowModal').modal('show');
        } else {
            $('#statusShowModal').modal('hide');
            updateCityStatus(currentCityId,0);
        }
    });

    
    $('#yes-btn').click(function(){
    var cityId = currentCityId;
            var cityStatus = 1;
            updateCityStatus(cityId, cityStatus);
            $('#statusShowModal').modal('hide');
    });
    $('#deleteRecord-close').click(function(){
        var cityId = currentCityId;
        $('#property_status_' + cityId).prop('checked', false);
    });
});


$(document).ready(function(){
          $('#reset-btn').click(function(){
            $('#userSearch').val('');
            $('#updatecity').val('');
            $('#property_status_').val('');
            $('#statusShowModal').val('');

            nextPageData("getcityData");
        });
    });
    

    </script>

</body>

</html>
