<div class="row">
                <div class="col-md-6">
                    <h4="text-muted"><b>BILL TO</b></h4>
                        <ul class="ml-2 px-3 list-unstyled">
                            <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                            <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                            <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                            <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                            <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
                        </ul>
                </div>
                <div class="col-md-6" style="text-align:right;">
                    </br>
                    <ul class="ml-2 px-3 list-unstyled">
                        <li><b>INVOICE DATE:</b> {{\Carbon\Carbon::parse($invoice->created_at)->format('d M Y')}}</li>
                        <li><b>DUE DATE:</b> {{\Carbon\Carbon::parse($invoice->duedate)->format('d M Y')}}</li>
                        <li></br></li>
                        @if( $invoice->status == 'paid' )
                            <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                        @elseif( $invoice->status == 'overpaid' )            
                            <div style="background-color:darkorange;font-size:17px" class="badge badge-opacity-warning"> OVER PAID</div>
                        @elseif ( $invoice->status == 'partiallypaid' )            
                            <div style="background-color:blue;font-size:17px;font-weight:800" class="badge badge-opacity-sucess"> PARTIALLY PAID</div>
                        @elseif ( $invoice->status == 'unpaid' )           
                            <div style="background-color:red;font-size:17px;font-weight:800"  class="badge badge-opacity-warning;font-size:17px">UN-PAID </div>
                        @endif            
                    </ul>
                </div>

            </div>