@extends('layouts.admin.admin')

@section('content')


<div class="row">
    <div class="col-md-9">
        <div class=" contwrapper">
            @include('admin.lease.document_header')
            <!----------  SECOND LEVEL ---------------->
            @include('admin.lease.document_details')
            <!------- THIRD LEVEL -->
            @include('admin.lease.document_table')
            <!------- FOURTH LEVEL -->
            @include('admin.lease.document_totals')
            <!------- FOOTER-->
            <hr>

            <div class="col-md-12" style="text-align:center;">
                <h6>Terms & Condition</h6>
                <p>Refer to the terms and conditions on Lease agreement.</p>
                <p><a href="www.bridgetech.co.ke">POWERED BY BRIDGE PROPERTIES</a></p>
            </div>

        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4>ACTIONS</h4>
            </div>
            <div class="card-body">
            <a href="" class="btn btn-primary btn-lg text-white float-end" data-toggle="modal" data-target="#sendemail"><i class="ti-email"></i> Email</a>

            <a href="{{ url('invoice/'.$invoice->id.'/pdf') }}" onclick="printDiv('printMe')" class="btn btn-warning btn-lg text-white float-end"><i class="icon-printer" style="color:white"></i> Print</a>


            </div>
        </div>

    </div>

</div>





@endsection