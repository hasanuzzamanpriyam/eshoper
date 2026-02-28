@extends('layouts.back-end.app')

@section('title', 'Delivery Charge Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Delivery Charge Settings</h4>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.delivery-charge.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label>Local Delivery Charge</label>
                            <input type="number" step="0.01" name="local_delivery_charge"
                                   class="form-control"
                                   value="{{ old('local_delivery_charge', $deliveryCharge->local_delivery_charge) }}"
                                   required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Country Delivery Charge</label>
                            <input type="number" step="0.01" name="country_delivery_charge"
                                   class="form-control"
                                   value="{{ old('country_delivery_charge', $deliveryCharge->country_delivery_charge) }}"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Delivery Charge
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
