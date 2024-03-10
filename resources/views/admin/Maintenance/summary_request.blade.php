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
            @if ($modelrequests)
            @php
            $statusClasses = [
            'Completed' => 'active',
            'New' => 'warning',
            'OverDue' => 'error',
            'In Progress' => 'information',
            'Assigned' => 'dark',
            ];

            // Get the status and find its corresponding class
            $status = $modelrequests->status;
            $statusClass = $statusClasses[$status] ?? 'default';
            @endphp

            <span class="statusdot statusdot-{{ $statusClass }}"></span>
            <span>{{ $status }}</span>
            @else
            <!-- Handle the case when $modelrequest is null -->
            <span class="statusdot statusdot-default"></span>
            <span>N/A</span>
            @endif
            <hr>
            <h5><b>Assigned:</b></h5>
            @if ($modelrequests->assigned)

            <!-- Assigned user or vendor exists -->
                @if ($modelrequests->assigned_type === 'App\Models\User')
                <!-- Assigned to a user -->
                <h5>Employee- {{ $modelrequests->assigned->firstname }} {{ $modelrequests->assigned->lastname }}</h5>
               

                @elseif ($modelrequests->assigned_type === 'App\Models\Vendor')
                <!-- Assigned to a vendor -->
                <h5>Vendor: {{ $modelrequests->assigned->vendor_name }}</h5>
                @endif
            @else
            <!-- No assigned user or vendor -->
            <a href="{{ url('request/assign/'.$modelrequests->id) }}">Assign Request</a>
            @endif
            <hr>
            <h5><b>Total Invoice Amount:</b> {{ $sitesettings->site_currency}}: {{$modelrequests->totalamount}}</h5>
            <h5><b>Total Paid Amount:</b> {{ $sitesettings->site_currency}}:</h5>
            <h4><b>Balance:</b></h4>


        </div>

    </div>
</div>
@include('layouts.admin.scripts')