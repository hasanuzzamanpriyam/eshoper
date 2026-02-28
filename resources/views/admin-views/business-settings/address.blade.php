@extends('layouts.back-end.app')

@section('title', translate('address_setup'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.bootstrap4.css"> --}}
    <style>
        .modal-bg{
            background-color: rgba(26, 26, 26, 0.5);
        }
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(90% - 1.75rem); /* Adjust if needed */
        }

        .address-menu{
            cursor: pointer;
            color: #0b498f;
            font-weight: bold;
            opacity: 0.7;
        }

        .address-menu:hover {
            opacity: 1;
        }

        .address-menu-active {
            opacity: 1;
        }

        .display-none{
            display:none;
        }
        .display-block{
            display: block;
        }

    </style>

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-4 pb-2">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{asset('assets/back-end/img/system-setting.png')}}" alt="">
            {{translate('system_Setup')}}
        </h2>
    </div>
    <!-- End Page Title -->

    <!-- Inlile Menu -->
    @include('admin-views.business-settings.system-settings-inline-menu')
    <!-- End Inlile Menu -->

    <div class="card">
        <div class="px-3 py-2">
            {{-- <h3 class="mx-2">{{ translate('insert_address') }}</h3> --}}
            <span class="address-menu address-menu-active pr-3" id="districtMenu" onclick="activeAddressMenu('districtMenu')">District Name</span>
            <span class="address-menu pr-3" id="thanaMenu" onclick="activeAddressMenu('thanaMenu')">Thana Name</span>
        </div>
        <hr class="mt-0 mb-3">

        {{-- district name --}}
        <div id="distName">
            <div class="row mt-2">
                <div class="col-7 px-2 px-md-3">
                    <h3 class="text-md-right">
                        District Names
                    </h3>
                </div>
                <div class="col-5 text-right px-2 px-md-3">
                    <span class="btn btn-sm btn-primary px-2 mx-1 mx-md-3" onclick="viewDistrictModal();">Add District</span>
                </div>
            </div>


            <div class="card-body">
                {{-- District table --}}
                <div class="py-2">
                    <div id="districtTableDiv">

                    </div>
                </div>
            </div>
        </div>

        {{-- Thana name --}}
        <div id="thName" hidden="hidden">
            <div class="row mt-2">
                <div class="col-7 px-2 px-md-3">
                    <h3 class="text-md-right">
                        Thana Names
                    </h3>
                </div>
                <div class="col-5 text-right px-2 px-md-3">
                    <span class="btn btn-sm btn-primary px-2 mx-1 mx-md-3" onclick="viewThanaModal();">Add Thana</span>
                </div>
            </div>


            <div class="card-body">
                {{-- Thana table --}}
                <div class="py-2">
                    <div id="thanaTableDiv">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- Add new or edit District name modal --}}

<div class="modal fade modal-bg"id="saveDistrictModal" tabindex="-1" role="dialog" aria-labelledby="newDistrictModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="newDistrictModalLabel">District Name (জেলার নাম)</h4>
          <button type="button" class="close"  onclick="hideDistrictModal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" id="districtForm" enctype="multipart/form-data">
            @csrf
            <div class="text-center mb-3 text-success">
              <h2>Save District</h2>
            </div>
            <div class="row mb-3">
              <div class="col-12 my-2">
                <input type="hidden" id="district_id" name="district_id">
                <label for="district_name_en" class="form-label mb-0">* District Name (English):</label>
                <small class="text-danger" id="districtNameEnError"></small>
                <input type="text" class="form-control" id="district_name_en" name="district_name_en" placeholder="District Name in English" required>
              </div>

              <div class="col-12 my-2">
                <label for="district_name_bn" class="form-label mb-0">* জেলার নাম (বাংলা):</label>
                <small class="text-danger" id="districtNameBnError"></small>
                <input type="text" class="form-control" id="district_name_bn" name="district_name_bn" placeholder="জেলার নাম বাংলায়" required>
              </div>

              <div class="col-md-7 my-2">
                <label for="district_shipping_charge" class="form-label mb-0">Shipping Charge (District): TK</label>
                <small class="text-danger" id="districtShippingChargeError"></small>
                <input type="text" class="form-control" id="district_shipping_charge" name="district_shipping_charge" placeholder="District Shipping Charge" value="0">
              </div>

              <div class="col-md-5 my-2">
                <label for="status" class="form-label mb-0">* Status:</label>
                <small class="text-danger" id="statusError"></small>
                <select class="form-control" id="status" name="status" required>
                  <option value="">Select Status</option>
                  <option value="Active">Active</option>
                  <option value="Draft">Draft</option>
                </select>
              </div>
            </div>
            <div class="mt-5 pe-md-5 text-end">
              <button type="button" class="btn btn-danger py-0 me-3" onclick="hideDistrictModal()">Close</button>
              <button type="submit" name="submit" class="btn btn-success py-0">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

{{-- delete District name modal --}}
<div class="modal fade modal-bg"id="deleteDistrictNameModal" tabindex="-1" role="dialog" aria-labelledby="deleteDistrictNameModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="deleteDistrictNameModalLabel">Delete District Name</h4>
          <button type="button" class="close"  data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-3 text-success">
              <h2 class="text-center">Really! Want to delete <i id="d_name"></i> permanetly?</h2>
            </div>
            <div class="my-2">
                <input type="hidden" name="d_id" id="d_id">
            </div>
            <div class="mt-5 pe-md-5 text-end">
              <button type="button" class="btn btn-danger py-0 me-3" data-dismiss="modal">Close</button>
              <button type="button" onclick="deleteDistrictName()" class="btn btn-success py-0">Delete</button>
            </div>
        </div>
      </div>
    </div>
</div>


{{-- Add new or edit Thana name modal --}}
<div class="modal fade modal-bg"id="saveThanaModal" tabindex="-1" role="dialog" aria-labelledby="newThanaModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="newThanaModalLabel">Thana Name (থানার নাম)</h4>
          <button type="button" class="close" onclick="hideThanaModal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" id="thanaForm" enctype="multipart/form-data">
            @csrf
            <div class="text-center mb-3 text-success">
              <h2>Save Thana</h2>
            </div>

            <input type="hidden" id="thana_id" name="thana_id">
            <div class="row mb-3">
                <div class="col-12 my-2">
                    <label for="dist_id" class="form-label mb-0">* District Name:</label>
                    <small class="text-danger" id="distIdError"></small>
                    <select class="form-control" id="dist_id" name="dist_id" required>


                    </select>
                </div>
              <div class="col-12 my-2">
                <label for="thana_name_en" class="form-label mb-0">* Thana Name (English):</label>
                <small class="text-danger" id="thanaNameEnError"></small>
                <input type="text" class="form-control" id="thana_name_en" name="thana_name_en" placeholder="Thana Name in English" required>
              </div>

              <div class="col-12 my-2">
                <label for="thana_name_bn" class="form-label mb-0">* থানার নাম (বাংলা):</label>
                <small class="text-danger" id="thanaNameBnError"></small>
                <input type="text" class="form-control" id="thana_name_bn" name="thana_name_bn" placeholder="থানার নাম বাংলায়" required>
              </div>

              <div class="col-md-7 my-2">
                <label for="thana_shipping_charge" class="form-label mb-0">Shipping Charge (Thana): TK</label>
                <small class="text-danger" id="thanaShippingChargeError"></small>
                <input type="text" class="form-control" id="thana_shipping_charge" name="thana_shipping_charge" placeholder="Thana Shipping Charge" value="0">
              </div>

              <div class="col-md-5 my-2">
                <label for="thStatus" class="form-label mb-0">* Status:</label>
                <small class="text-danger" id="thStatusError"></small>
                <select class="form-control" id="thStatus" name="status" required>
                  <option value="">Select Status</option>
                  <option value="Active">Active</option>
                  <option value="Draft">Draft</option>
                </select>
              </div>
            </div>
            <div class="mt-5 pe-md-5 text-end">
              <button type="button" class="btn btn-danger py-0 me-3" onclick="hideThanaModal()">Close</button>
              <button type="submit" name="submit" class="btn btn-success py-0">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

{{-- delete Thana name modal --}}
<div class="modal fade modal-bg"id="deleteThanaNameModal" tabindex="-1" role="dialog" aria-labelledby="deleteThanaNameModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="deleteThanaNameModalLabel">Delete Thana Name</h4>
          <button type="button" class="close"  data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-3 text-success">
              <h2 class="text-center">Really! Want to delete <i id="th_name"></i> permanetly?</h2>
            </div>
            <div class="my-2">
                <input type="hidden" name="th_id" id="th_id">
            </div>
            <div class="mt-5 pe-md-5 text-end">
              <button type="button" class="btn btn-danger py-0 me-3" data-dismiss="modal">Close</button>
              <button type="button" onclick="deleteThanaName()" class="btn btn-success py-0">Delete</button>
            </div>
        </div>
      </div>
    </div>
</div>


@endsection

@push('script')
{{-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script> --}}


<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>


<script>
    // active or deactive address menu
    function activeAddressMenu(menuId){
        $('.address-menu').removeClass('address-menu-active');
        $('#'+menuId).addClass('address-menu-active');
        if(menuId=="districtMenu"){
            $('#thName').attr('hidden', 'hidden');
            $('#distName').removeAttr('hidden');
        }else if(menuId=="thanaMenu"){
            thanaNameLists();
            $('#distName').attr('hidden', 'hidden');
            $('#thName').removeAttr('hidden');
        }

    }
// for district name ------------------------------------------------------
// view district name modal
  function viewDistrictModal(){
    $('#saveDistrictModal').modal('show');

  }
  function hideDistrictModal(){
    $('#saveDistrictModal').modal('hide');
      $('#districtForm').trigger("reset");
      $('#districtNameBnError').text('');
      $('#districtNameEnError').text('');
      $('#districtShippingChargeError').text('');
      $('#statusError').text('');
      $('#status').val('');
      $('#district_id').val('');
      $('#district_name_en').val('');
      $('#district_name_bn').val('');
      $('#district_shipping_charge').val('0');

  }

  districtNameLists();
// district name list
  function districtNameLists(){
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $("#districtTableDiv").html('<div class="preloader"></div><table class="table table-striped text-center pb-0 mb-0" id="districtTable" width="100%" cellspacing="0"><thead><tr><th>SL</th><th>District Name</th><th>জেলার নাম</th><th>Shipping Charge</th><th>Status</th><th>Action</th></tr></thead><tbody id="districtTbody"></tbody></table>');
    $.ajax({
        url: "{{ route('admin.address.district-names-list') }}",
        type: 'GET',
        success: function(dbData){
              var data = "";
              var i = 1;
              $.each(dbData, function(key, value){
                  data+="<tr>";
                  data+="<td class='py-1'>"+ i++ +"</td><td class='py-1'>"+value.district_name_en+"</td><td class='py-1'>"+value.district_name_bn+"</td><td class='py-1'>Tk "+value.district_shipping_charge+"</td><td class='py-1'>"+value.status+"</td><td class='py-1'>"+"<div class='text-center fw-bold'><span class='btn btn-sm btn-outline-primary p-1 mr-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit' onclick='editDistrictName("+value.id+")'><span class='tio-edit' style='font-size:10px;'></span></span><span class='btn btn-sm btn-outline-danger p-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Delete' onclick='viewDeleteDistrictNameModal("+value.id+", `"+value.district_name_en+"`)'><span class='tio-delete' style='font-size:10px;'></span></span></div>"+"</td>";
                  data+="</tr>";
              });
              $('#districtTbody').append(data);
              $('#districtTable').DataTable({scrollX: true});
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });
  }


  $('#districtForm').submit(function(e){
    e.preventDefault();
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

    $.ajax({
      url: "{{ route('admin.address.district-names-save') }}",
      data: $('#districtForm').serialize(),
      type: "POST",
      success: function(response){
        $('#districtForm').trigger("reset");
        $('#districtNameBnError').text('');
        $('#districtNameEnError').text('');
        $('#districtShippingEnError').text('');
        $('#statusError').text('');
        $('#status').val('');
        $('#district_id').val('');
        $('#district_name_en').val('');
        $('#district_name_bn').val('');
        $('#district_shipping_charge').val('0');
        $('#saveDistrictModal').modal('hide');
        districtNameLists();
        toastr.success(response.message);
      },
      error: function(error){
        $('#districtNameBnError').text(error.responseJSON.errors.district_name_en);
        $('#districtNameEnError').text(error.responseJSON.errors.district_name_bn);
        $('#districtShippingEnError').text(error.responseJSON.errors.district_shipping_charge);
        $('#statusError').text(error.responseJSON.errors.status);
      }
    });

  });

// edit single district name
function editDistrictName(id) {
    // Set up AJAX with CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('admin.address.district-name-edit', '') }}/"+id,
        type: 'GET',
        success: function(dbData) {
            $('#district_id').val(dbData.id);
            $('#district_name_en').val(dbData.district_name_en);
            $('#district_name_bn').val(dbData.district_name_bn);
            $('#district_shipping_charge').val(dbData.district_shipping_charge);
            $('#status').val(dbData.status);
            viewDistrictModal();
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}

// delete item
// open delete modal -----------------
function viewDeleteDistrictNameModal(id, name) {
        $('#deleteDistrictNameModal').modal('show');
        $('#d_id').val(id);
        $('#d_name').text(name);
        }
        // delete item acording to  url, id and allData fucntion
        function deleteDistrictName() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            var id=$("#d_id").val();

            $.ajax({
                method: "GET",
                dataType: 'json',
                url: "{{ route('admin.address.district-name-delete', '') }}/"+id,
                success:function(response){
                    districtNameLists();
                    $('#deleteDistrictNameModal').modal('hide');
                    toastr.success(response.message);
                }
            });
        }
// end district name --------------------------------------------------------


// for Thana name -------------------------------------
    // view Thana name modal
      function viewThanaModal(){
        $('#saveThanaModal').modal('show');

      }
      function hideThanaModal(){
        $('#saveThanaModal').modal('hide');
        $('#thanaForm').trigger("reset");
        $('#distIdError').text('');
        $('#thanaNameBnError').text('');
        $('#thanaNameEnError').text('');
        $('#thanaShippingChargeError').text('');
        $('#thStatusError').text('');
        $('#thStatus').val('');
        $('#dist_id').val('');
        $('#thana_id').val('');
        $('#thana_name_en').val('');
        $('#thana_name_bn').val('');
        $('#thana_shipping_charge').val('0');

      }

      thanaNameLists();
      selectDistrictName();

    // Thana name list
    function thanaNameLists(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $("#thanaTableDiv").html('<div class="preloader"></div><table class="table table-striped text-center pb-0 mb-0" id="thanaTable" width="100%" cellspacing="0"><thead><tr><th>SL</th><th>District Name</th><th>Thana Name</th><th>থানার নাম</th><th>Shipping Charge</th><th>Status</th><th>Action</th></tr></thead><tbody id="thanaTbody"></tbody></table>');
        $.ajax({
            url: "{{ route('admin.address.thana-names-list') }}",
            type: 'GET',
            success: function(dbData){
                var data = "";
                var i = 1;
                $.each(dbData, function(key, value){
                    data+="<tr>";
                    data+="<td class='py-1'>"+ i++ +"</td><td class='py-1'>"+value.district_name_en+" ("+value.district_name_bn+")</td><td class='py-1'>"+value.thana_name_en+"</td><td class='py-1'>"+value.thana_name_bn+"</td><td class='py-1'>Tk "+value.thana_shipping_charge+"</td><td class='py-1'>"+value.status+"</td><td class='py-1'>"+"<div class='text-center fw-bold'><span class='btn btn-sm btn-outline-primary p-1 mr-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit' onclick='editThanaName("+value.id+")'><span class='tio-edit' style='font-size:10px;'></span></span><span class='btn btn-sm btn-outline-danger p-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Delete' onclick='viewDeleteThanaNameModal("+value.id+", `"+value.thana_name_en+"`)'><span class='tio-delete' style='font-size:10px;'></span></span></div>"+"</td>";
                    data+="</tr>";
                });
                $('#thanaTbody').append(data);
                $('#thanaTable').DataTable({scrollX: true});
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    }


    // select district names
    function selectDistrictName(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('admin.address.select-district-names') }}",
            type: 'GET',
            success: function(dbData){
                var data = '<option value="">Select District Name</option>';
                $.each(dbData, function(key, value){
                    data+='<option value="'+value.id+'">'+value.district_name_en+' ( '+value.district_name_bn+' )</option>';
                });
                $('#dist_id').append(data);
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    }

// save thana
    $('#thanaForm').submit(function(e){
        e.preventDefault();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

        $.ajax({
          url: "{{ route('admin.address.thana-names-save') }}",
          data: $('#thanaForm').serialize(),
          type: "POST",
          success: function(response){
            $('#thanaForm').trigger("reset");
            $('#distIdError').text('');
            $('#thanaNameBnError').text('');
            $('#thanaNameEnError').text('');
            $('#thanaShippingChargeError').text('');
            $('#thStatusError').text('');
            $('#thStatus').val('');
            $('#dist_id').val('');
            $('#thana_id').val('');
            $('#thana_name_en').val('');
            $('#thana_name_bn').val('');
            $('#thana_shipping_charge').val('0');
            $('#saveThanaModal').modal('hide');
            thanaNameLists();
            toastr.success(response.message);
          },
          error: function(xhr, status, error) {
        // console.error('Error:', error);
        // console.error('Status:', status);
        // console.error('Response:', xhr.responseText);
            $('#distIdError').text(error.responseJSON.errors.dist_id);
            $('#thanaNameBnError').text(error.responseJSON.errors.thana_name_en);
            $('#thanaNameEnError').text(error.responseJSON.errors.thana_name_bn);
            $('#thanaShippingChargeError').text(error.responseJSON.errors.thana_shipping_charge);
            $('#thStatusError').text(error.responseJSON.errors.status);
          }
        });

      });

    // edit single Thana name
    function editThanaName(id) {
        // Set up AJAX with CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('admin.address.thana-name-edit', '') }}/"+id,
            type: 'GET',
            success: function(dbData) {
                $('#thana_id').val(dbData.id);
                $('#dist_id').val(dbData.dist_id);
                $('#thana_name_en').val(dbData.thana_name_en);
                $('#thana_name_bn').val(dbData.thana_name_bn);
                $('#thana_shipping_charge').val(dbData.thana_shipping_charge);
                $('#thStatus').val(dbData.status);
                viewThanaModal();
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    // delete item
    // open delete modal
    function viewDeleteThanaNameModal(id, name) {
            $('#deleteThanaNameModal').modal('show');
            $('#th_id').val(id);
            $('#th_name').text(name);
            }
            // delete item acording to  url, id and allData fucntion
            function deleteThanaName() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var id=$("#th_id").val();

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "{{ route('admin.address.thana-name-delete', '') }}/"+id,
                    success:function(response){
                        thanaNameLists();
                        $('#deleteThanaNameModal').modal('hide');
                        toastr.success(response.message);
                    }
                });
            }
    // end Thana name --------------------------------------------------------
    </script>

@endpush


