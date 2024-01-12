@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">
    <h4>New Payment</h4>
    <hr>
    <form method="POST" action="{{ url('payment') }}" class="myForm" novalidate>
        @csrf
        <input type="hidden" name="invoice" value="{{ $invoice->id }}">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$invoice->property->id}}">{{$invoice->property->property_name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$invoice->unit->id}}">{{$invoice->unit->unit_number}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <input type="hidden" class="form-control" name="model_type" id="model_type" value="{{$className}}">
            <input type="hidden" class="form-control" name="model_id" id="model_id" value="{{$invoice->id}}">
            <input type="hidden" class="form-control" name="referenceno" id="referenceno" value="{{$referenceno}}">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Method<span class="requiredlabel">*</span></label>
                <select name="payment_method_id" id="payment_method_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Payment Method</option>
                    @foreach($PaymentMethod as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Code</label>
                <input type="text" class="form-control" name="payment_code" id="payment_code" value="">
            </div>
        </div>


        <!------- THIRD LEVEL INVOICE ITEMS -->
        <div class="col-md-6">
            <hr>
            <table class="table  table-bordered" style="font-size:12px;border:1px solid black;">
                <thead>
                    <tr class="tableheading" style="height:35px;">

                        <th>No.</th>
                        <th class="text-center">Charge </th>
                        <th class="text-center">Balance Due </th>
                        <th class="text-center">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->invoiceItems as $key=> $item)
                    <tr style="height:35px;">
                        <td class="text-center" style="background-color:#dae3fa;">{{$key+1}}</td>
                        <td class="text-center" style="text-transform: capitalize;background-color:#dae3fa;">
                            {{$item->charge_name}} Charge
                        </td>
                        <td class="text-center" style="background-color:#dae3fa;">{{ $sitesettings->site_currency }}. {{$item->amount}} </td>
                        <td class="text-center" style="padding:0px">
                            <div style="position: relative;">
                                <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                                </span>
                                <input type="text" class="form-control" name="amount[]" id="amount" value="{{$item->amount}}" style="text-align: left; padding-left: 45px; border:none">
                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></br>


        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment</button>
        </div>

    </form>

</div>

@endsection