<div class="row">
    <div class="col-md-7">

        <div class=" contwrapper">
            @include('admin.CRUD.edit')

        </div>

    </div>


    <div class="col-md-5">
        <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">
            <h4><b> Process Requests</b>
            </h4>
            <hr>
            <h5><b>Status:</b></h5>
            @if ($tickets)
            @php
            $statusClasses = [
            'Completed' => 'active',
            'New' => 'warning',
            'OverDue' => 'error',
            'In Progress' => 'information',
            'Assigned' => 'dark',
            ];

            // Get the status and find its corresponding class
            $status = $tickets->status;
            $statusClass = $statusClasses[$status] ?? 'default';
            @endphp

            <span class="statusdot statusdot-{{ $statusClass }}"></span>
            <span>{{ $status }}&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                @if( Auth::user()->can('ticket.edit') || Auth::user()->id === 1)
                <a href="" class="editLink"><i class="mdi mdi-lead-pencil  text-primary"></i> Change Status</a>
                @endif
            </span>
            @else
            <!-- Handle the case when $modelrequest is null -->
            <span class="statusdot statusdot-default"></span>
            <span>N/A</span>
            @endif
            <br> <br>
            <form action="{{ url('ticket/'.$tickets->id) }}" method="POST" style="display: inline;">
                @csrf
                <div class="col-md-9" id="">
                    <div class="form-group">
                        <select name="status" id="status" class="formcontrol2 " placeholder="Select">
                            <option value="">Select Status</option>
                            <option value="in Progress">In Progress</option>
                            <option value="overDue">Over Due</option>
                            <option value="completed">Completed</option>
                            <option value="completed">Cancelled</option>
                            <option value="completed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9" id="expense"  style="display: none;">
                    <div class="form-group">
                        <label class="label"> Charge Expenses To<span class="requiredlabel">*</span></label>
                        <select name="charged_to" id="" class="formcontrol2 " placeholder="Select">
                            <option value="">Select Value</option>
                            <option value="tenant">Tenant</option>
                            <option value="company">Company</option>
                        </select>
                    </div>
                </div>
                <button id="submit" type="submit" style="display: none;" class="btn btn-primary btn-lg text-white mb-0 me-0">Edit Status</button>
            </form>
            <hr>
            <h5><b>Assigned:</b></h5>
            @if ($tickets->assigned)

            <!-- Assigned user or vendor exists -->
            @if ($tickets->assigned_type === 'App\Models\User')
            <!-- Assigned to a user -->
            <h5>Employee- {{ $tickets->assigned->firstname }} {{ $tickets->assigned->lastname }}</h5>


            @elseif ($tickets->assigned_type === 'App\Models\Vendor')
            <!-- Assigned to a vendor -->
            <h5>Vendor: {{ $tickets->assigned->vendor_name }}</h5>
            @endif
            @else
            <!-- No assigned user or vendor -->
            <a href="{{ url('ticket/assign/'.$tickets->id) }}">Assign Request</a>
            @endif
            <hr>
            <h5><b>Total Invoice Amount:</b> {{ $sitesettings->site_currency}}: {{$tickets->totalamount}}</h5>
            <h5><b>Total Paid Amount:</b> {{ $sitesettings->site_currency}}:</h5>
            <h4><b>Balance:</b></h4>
        </div>
    </div>
</div>
        <script>
              $(document).ready(function() {
                $('#status').on('change', function() {
                var value = this.value;
                //  alert(query);
                // Select the label element
                var $expense = $('#expense');
                var $submit = $('#submit');
                if (value === "completed") {
                    $expense.show();
                    $submit.show();
                } else {
                    $expense.hide();
                    $submit.show();
                }

            });
        });
        </script>
        @include('layouts.admin.scripts')