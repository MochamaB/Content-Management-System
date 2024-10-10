    @php
    $statusClasses = [
    'Completed' => 'active',
    'Pending' => 'warning',
    'Cancelled' => 'error',
    'In Progress' => 'information',
    'On Hold' => 'dark',
    ];

    // Get the status and find its corresponding class
    $statusvalue = $tickets->status;
    $assign = $tickets->assigned_type;
    $canAssign = Auth::user()->can('ticket.assign');
    $isAdmin = Auth::user()->id === 1;
    $status = $tickets->getStatusLabel();
    $statusClass = $statusClasses[$status] ?? 'default';
    @endphp
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-9 mb-3">
            <!-- Complete button: Only show if the ticket's workorders is not null -->
            @if((Auth::user()->can('ticket.edit') || $isAdmin) && $tickets->workorders->isNotEmpty() && $statusvalue !== \App\Models\Ticket::STATUS_COMPLETED)
            <a href="" class="btn btn-success btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;"
                data-toggle="collapse" data-target="#collapseClose" aria-expanded="false" aria-controls="collapseClose">
                <i class="mdi mdi-plus-circle-outline"></i>
                Complete Ticket
            </a>
            @endif

            <!-- Change Status button: Only show if status is not "Completed" -->
            @if((Auth::user()->can('ticket.edit') || $isAdmin) && $statusvalue !== \App\Models\Ticket::STATUS_COMPLETED)
            <a href="" class="btn btn-warning btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;"
                data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="mdi mdi-plus-circle-outline"></i>
                Change Status
            </a>
            @endif

            <!-- Re-Assign Ticket button: Show if user can assign or is admin, status is "In Progress", and ticket is already assigned -->
            @if(($canAssign || $isAdmin) && $statusvalue === \App\Models\Ticket::STATUS_IN_PROGRESS && !is_null($assign))
            <a href="{{ url('ticket/assign/'.$tickets->id) }}" class="btn btn-dark btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
                <i class="mdi mdi-plus-circle-outline"></i>
                Re-Assign Ticket
            </a>
            @endif
            <!-- Add Workorder  button: Show if user can edit or is admin, status is "In Progress", and ticket is already assigned -->
            @if((Auth::user()->can('ticket.edit') || $isAdmin) && $statusvalue === \App\Models\Ticket::STATUS_IN_PROGRESS && !is_null($assign))
            <a href="{{ url('work-order/create/'.$tickets->id) }}" class="btn btn-outline-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
                <i class="mdi mdi-plus-circle-outline"></i>
                Add Work order Item
            </a>
            @endif

            <!-- Assign Ticket button: Show if user can assign or is admin, status is "Pending", and ticket is not assigned -->
            @if(($canAssign || $isAdmin) && $statusvalue === \App\Models\Ticket::STATUS_PENDING && is_null($assign))
            <a href="{{ url('ticket/assign/'.$tickets->id) }}" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
                <i class="mdi mdi-plus-circle-outline"></i>
                Assign Ticket
            </a>
            @endif
        </div>
        <div class="col-md-7">
            <div class=" contwrapper">
                <h6><b> Process Requests</b>
                </h6>
                <hr>
                <h6><b>Status</b></h6>
                <span class="statusdot statusdot-{{ $statusClass }}"></span>
                <span class="defaulttext">{{ $status }}</span>
                <!--- Collapsible Change Status-->
                <div class="collapse collapseExample mt-2" id="collapseExample">
                    <div class="filterbody">
                        <form action="{{ url('ticket/'.$tickets->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <div class="col-md-10" id="">
                                <div class="form-group">
                                    <label class="label"> Change Status<span class="requiredlabel">*</span></label>
                                    <select name="status" id="status" class="formcontrolnoedit" placeholder="Select">
                                        <option value="">Select Status</option>
                                        <option value="{{ \App\Models\Ticket::STATUS_IN_PROGRESS }}" {{ old('status', $tickets->status) == \App\Models\Ticket::STATUS_IN_PROGRESS ? 'selected' : '' }}>In Progress</option>
                                        <option value="{{ \App\Models\Ticket::STATUS_PENDING }}" {{ old('status', $tickets->status) == \App\Models\Ticket::STATUS_PENDING ? 'selected' : '' }}>Pending</option>
                                        <option value="{{ \App\Models\Ticket::STATUS_ON_HOLD }}" {{ old('status', $tickets->status) == \App\Models\Ticket::STATUS_ON_HOLD ? 'selected' : '' }}>On Hold</option>
                                        <option value="{{ \App\Models\Ticket::STATUS_CANCELLED }}" {{ old('status', $tickets->status) == \App\Models\Ticket::STATUS_CANCELLED ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 pb-2">Edit Status</button>
                        </form>
                    </div>
                </div>
                <!-- Collapsible Close -->
                <div class="collapse collapseClose mt-2" id="collapseClose">
                    <div class="filterbody">
                        <form action="{{ url('ticket/'.$tickets->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <div class="col-md-10" id="">
                                <div class="form-group">
                                    <label class="label"> Status<span class="requiredlabel">*</span></label>
                                    <select name="status" id="status" class="formcontrolnoedit" placeholder="Select" required readonly>
                                        <option value="{{ \App\Models\Ticket::STATUS_COMPLETED }}">Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-10" id="expense">
                                <div class="form-group">
                                    <label class="label"> Charge Expenses To<span class="requiredlabel">*</span></label>
                                    <select name="charged_to" id="expenseSelect" class="formcontrolnoedit " placeholder="Select">
                                        <option value="">Select Value</option>
                                        <option value="nocharge">No Charge</option>
                                        <option value="tenant">Tenant</option>
                                        <option value="property">Property / Company</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-10" id="accounts" style="display: none;">
                                <div class="form-group">
                                    <label class="label"> Accounts<span class="requiredlabel">*</span></label>
                                    <select id="incomeaccount" class="formcontrolnoedit " placeholder="Select">
                                        <option value="">Select Account</option>
                                        @foreach($incomeAccounts as $accounttype => $account)
                                        <optgroup label="{{ $accounttype }}">
                                            @foreach($account as $item)
                                            <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>

                                    <select id="expenseaccount" class="formcontrolnoedit " placeholder="Select">
                                        <option value="">Select Account</option>
                                        @foreach($expenseAccounts as $accounttype => $account)
                                        <optgroup label="{{ $accounttype }}">
                                            @foreach($account as $item)
                                            <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 pb-2">Complete Ticket</button>

                        </form>
                    </div>
                </div>
                <hr>
                <h6><b>Assigned To</b></h6>
                @if ($tickets->assigned && $tickets->assigned_type === 'App\Models\User')
                <!-- Assigned to a user -->
                <span class="defaulttext">Employee - {{ $tickets->assigned->firstname }} {{ $tickets->assigned->lastname }}</span>
                @elseif ($tickets->assigned && $tickets->assigned_type === 'App\Models\Vendor')
                <!-- Assigned to a vendor -->
                <span class="defaulttext">Vendor - {{ $tickets->assigned->name }}</span>
                @else
                <span class="defaulttext"> Not Assigned </span>
                @endif
                <hr>
                <h6><b>Charged To</b></h6>
                <span class="defaulttext">{{$tickets->charged_to ?? 'Not charged'}}</span>
                <hr>
                <h6><b>Total Invoice Amount:</b> {{ $sitesettings->site_currency}}: {{$tickets->totalamount ?? 0}}</h6>
                <h6><b>Total Paid Amount:</b> {{ $sitesettings->site_currency}}:</h6>
                <h6><b>Balance:</b></h6>
            </div>
        </div>
        <div class="col-md-5">
            <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">
                @include('admin.CRUD.edit')

            </div>

        </div>

    </div>
    <script>
        $(document).ready(function() {
            $('#expenseSelect').on('change', function() {
                var value = this.value;
                //  alert(value);
                console.log(value);

                var $account = $('#accounts');
                var $incomeAccountsSelect = $('#incomeaccount');
                var $expenseAccountsSelect = $('#expenseaccount');
                // Remove the name attribute from both selects initially
                $incomeAccountsSelect.removeAttr('name');
                $expenseAccountsSelect.removeAttr('name');

                 // Hide both selects initially
                $incomeAccountsSelect.hide();
                $expenseAccountsSelect.hide();
                $account.hide();

                if (value === "tenant") {
                    $account.show();
                    $incomeAccountsSelect.show().attr('name', 'chartofaccount_id'); // Set the name attribute only for the visible select
                    $expenseAccountsSelect.hide();
                } else if (value === "property") {
                    $account.show();
                    $expenseAccountsSelect.show().attr('name', 'chartofaccount_id'); // Set the name attribute only for the visible select
                    $incomeAccountsSelect.hide();
                } else if (value === "nocharge") {
                    // When "No Charge" is selected, both selects remain hidden
                    $account.hide(); // Hide the accounts container
                    $incomeAccountsSelect.hide();
                    $expenseAccountsSelect.hide();
                } else {
                    $account.hide(); // Hide the accounts container
                    $incomeAccountsSelect.hide();
                    $expenseAccountsSelect.hide();
                }


            });
        });
    </script>

    @include('layouts.admin.scripts')