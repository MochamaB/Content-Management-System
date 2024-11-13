<style>
    /* Limit the width of the SMS content column */
    .sms-content-column {
        max-width: 300px;
        /* Adjust the width as per your design */
        word-wrap: break-word;
        white-space: normal !important;
        /* Ensures text wraps onto the next line */
        line-height: 1.6 !important;
    }

    .fixed-table-toolbar {
        padding-left: 20px;
    }

    .pagination-info {
        line-height: 34px;
        margin-right: 5px;
        font-size: 14px;
        padding-left: 20px;
    }
</style>

<div class="table-responsive" style="border: 1px solid #ccc;padding:0px 0px 10px 0px;background-color:#fff;margin-top:-4px;">
    <table id="table"
        data-toggle="table"
        data-icon-size="sm"
        data-buttons-class="outline-primary"
        data-toolbar-align="right"
        data-buttons-align="right"
        data-search-align="left"
        data-sort-order="asc"
        data-search="true"
        data-mobile-responsive="true"
        data-sticky-header="true"
        data-pagination="true"
        data-page-list="[100, 200, 250, 500, ALL]"
        data-page-size="100"
        data-show-footer="false"
        data-side-pagination="client"
        data-checkbox="true"
        class="table ">
        <thead style="background-color: #fff !important;">
            <tr>
                <th data-checkbox="true"></th>
                <th>To</th>
                <th>Text Content</th>
                <th>Date</th>
                <th>Status</th>
                <th>From</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($textContent as $notification)
            @php
                $user = ($notification->notifiable->firstname ?? '') . ' ' . ($notification->notifiable->lastname ?? '');
                $phonenumber = $notification->notifiable->phonenumber ?? 'N/A'; // default value if phonenumber is null
                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                $backgroundColor = $notification->read_at ? 'white' : '#F4F5F7';

                $statusClasses = [
                'sent' => 'active',
                'pending' => 'warning',
                'failed' => 'danger',
                ];
                $status = $notification->status ?? 'pending'; // default to 'pending' if status is null
                $statusClass = $statusClasses[$status] ?? 'warning'; // fallback if status is not found in the classes array
            @endphp
            <tr
                style="height:50px; background-color: {{$backgroundColor}}; border-bottom: 1px solid #ccc;"
                class="clickable-row"
                data-id="{{ $notification->id }}">
                <td></td>
                <td class=""><span class="pb-2" style="font-weight: 600;">{{$user}}</span> </br></br>
                    {{ $phonenumber ?? 'Unknown' }}
                </td>
                <td class="sms-content-column">{{ $notification['sms_content'] ?? 'Content' }}</td>
                <td class="time">{{\Carbon\Carbon::parse($notification->created_at)->format('d M Y') }}</td>
                <td> <span class="badge badge-{{ $statusClass }}">{{ ucfirst($status) }} </span></td>
                <td>{{ $data['from'] ?? 'Unknown' }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

</div>
<script>
    $(document).ready(function() {
        // Delegate the click event handler to the document for dynamically loaded rows
        $(document).on('click', '.clickable-row', function(event) {
            event.preventDefault(); // Prevent default behavior of the row click

            var notificationId = $(this).data('id'); // Retrieve the notification ID from the data-id attribute
            var rowElement = $(this); // Store the row element to update the background later


            // Perform the AJAX call to mark the notification as read
            $.ajax({
                url: '/notification/mark-as-read/' + notificationId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Optionally, change the row background color to indicate it has been read
                        rowElement.css('background-color', 'white');
                    }
                },
                error: function(xhr, status, error) {
                    // Log the full error details to the console for debugging
                    console.error('Error Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                }
            });
        });
    });
</script>





<!-- content-wrapper ends -->
<!-- partial:../../partials/_footer.html -->