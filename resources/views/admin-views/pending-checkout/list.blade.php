@extends('layouts.back-end.app')

@section('title', translate('pending_Checkouts'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <div>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0">
                    <img src="{{asset('assets/back-end/img/all-orders.png')}}" class="mb-1 mr-1" alt="">
                    <span class="page-header-title">
                        {{translate('pending_Checkouts')}}
                    </span>
                </h2>
                <span class="badge badge-soft-dark radius-50 fz-14">{{$checkouts->total()}}</span>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{url()->current()}}" method="GET">
                        <div class="row gx-2">
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label class="title-color text-capitalize" for="status">{{translate('status')}}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>{{translate('all')}}</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{translate('pending')}}</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{translate('paid')}}</option>
                                        <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>{{translate('abandoned')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label class="title-color" for="from_date">{{translate('start_Date')}}</label>
                                    <input type="date" name="from_date" value="{{request('from_date')}}" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label class="title-color" for="to_date">{{translate('end_Date')}}</label>
                                    <input type="date" value="{{request('to_date')}}" name="to_date" id="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label class="title-color" for="search">{{translate('search')}}</label>
                                    <input type="text" name="search" value="{{request('search')}}" id="search" class="form-control" placeholder="{{translate('search_by_Name_Phone_Email')}}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="{{url()->current()}}" class="btn btn-secondary px-5">
                                        {{translate('reset')}}
                                    </a>
                                    <button type="submit" class="btn btn--primary px-5">
                                        {{translate('show_data')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="px-3 py-4 light-bg">
                        <div class="row g-2 align-items-center flex-grow-1">
                            <div class="col-md-6">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{translate('pending_Checkouts')}}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{$checkouts->total()}}</span>
                                </h5>
                            </div>
                            <div class="col-md-6 d-flex gap-3 flex-wrap flex-sm-nowrap justify-content-md-end">
                                <form action="{{url()->current()}}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" name="search" class="form-control"
                                            placeholder="{{translate('search_by_Name_Phone_Email')}}" value="{{ request('search') }}">
                                        <button type="submit" class="btn btn--primary input-group-text">{{translate('search')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100"
                            style="text-align: {{Session::get('direction') === 'rtl' ? 'right' : 'left'}}">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('customer_Name')}}</th>
                                    <th>{{translate('phone')}}</th>
                                    <th>{{translate('email')}}</th>
                                    <th>{{translate('shipping_Address')}}</th>
                                    <th>{{translate('total_Amount')}}</th>
                                    <th class="text-center">{{translate('status')}}</th>
                                    <th>{{translate('date')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($checkouts as $key => $checkout)
                                <tr>
                                    <td>{{$checkouts->firstItem() + $key}}</td>
                                    <td>
                                        <a class="text-body text-capitalize" href="{{route('admin.pending-checkout.details', $checkout->id)}}">
                                            <strong>{{$checkout->contact_person_name}}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:{{$checkout->phone}}">{{$checkout->phone}}</a>
                                    </td>
                                    <td>{{$checkout->email ?? translate('N/A')}}</td>
                                    <td>
                                        <div class="text-wrap" style="max-width: 200px;">
                                            {{ Str::limit($checkout->shipping_address, 60) }}
                                        </div>
                                    </td>
                                    <td>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($checkout->total_amount))}}
                                    </td>
                                    <td class="text-center">
                                        @if($checkout->status == 'pending')
                                            <span class="badge badge-soft-info fz-12">{{translate('pending')}}</span>
                                        @elseif($checkout->status == 'paid')
                                            <span class="badge badge-soft-success fz-12">{{translate('paid')}}</span>
                                        @elseif($checkout->status == 'abandoned')
                                            <span class="badge badge-soft-danger fz-12">{{translate('abandoned')}}</span>
                                        @endif
                                    </td>
                                    <td>{{date('d M Y', strtotime($checkout->created_at))}}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline--primary square-btn btn-sm mr-1" title="{{translate('view')}}"
                                                href="{{route('admin.pending-checkout.details', $checkout->id)}}">
                                                <img src="{{asset('assets/back-end/img/eye.svg')}}" class="svg" alt="">
                                            </a>
                                            <form action="{{route('admin.pending-checkout.destroy', $checkout->id)}}" method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger square-btn btn-sm" title="{{translate('delete')}}"
                                                    onclick="return confirm('{{translate('are_you_sure_delete')}}?')">
                                                    <i class="tio-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{$checkouts->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if(fr != ''){
                $('#to_date').attr('required','required');
            }
            if(to != ''){
                $('#from_date').attr('required','required');
            }
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{translate("invalid_date_range")}}!');
                }
            }
        })
    </script>
@endpush
