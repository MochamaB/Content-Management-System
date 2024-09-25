      <!-- partial -->

          <!-- summary View --->

          <div class="table-responsive" style="border:1px solid #dee2e6;padding:0px 0px 0px 0px">
              <table id="table" 
                     data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="right" data-search-align="right" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" 
                     class="table ">
                  <tbody>
                      @foreach ($notifications as $notification)
                      @php
                      $data = json_decode($notification->data, true);
                      @endphp
                      @if($notification->read_at == null)
                      <tr style="height:50px; background: #F4F5F7;border-bottom: 1px solid #ccc;" class="clickable-row"  data-id="{{ $notification->id }}" >
                          <td class="action"><input type="checkbox" /></td>
                          <td class="name"><a href="#" class="email">{{ $data['subject'] ?? 'Subject' }}</a></td>
                          <td class="subject"><a href="#" class="email pb-1">{{ $data['heading'] ?? 'Heading' }}</a>
                          </br>
                          {{ $data['linkmessage'] ?? 'Message Details' }}</td>
                          <td class="time">{{\Carbon\Carbon::parse($notification->created_at)->format('d M Y') }}</td>
                      </tr>
                      @else
                      <tr style="height:50px;background: #fff;border-bottom: 1px solid #ccc;" class="clickable-row" data-href="{{ url('email/'.$notification->id) }}">
                          <td class="action"><input type="checkbox" /></td>
                          <td class="name"><a href="#" class="reademail">{{ $data['subject'] ?? 'Subject' }}</a></td>
                          <td class="subject"><a href="#" class="reademail">{{ $data['heading'] ?? 'Heading' }}</a></td>
                          <td class="time">{{\Carbon\Carbon::parse($notification->created_at)->format('d M Y') }}</td>
                      </tr>
                      @endif
                      @endforeach

                  </tbody>
              </table>

          </div>

      <script>
        $(document).ready(function () {
            $(".clickable-row").click(function () {
                window.location = $(this).data("href");
            });
        });
    </script>
    <script>
    $(document).ready(function() {
        $('.clickable-row').on('click', function() {
            var notificationId = $(this).data('id');
            
            // Perform the AJAX call
            $.ajax({
                url: '/notification/email/' + notificationId, // Your route to fetch email details
                method: 'GET',
                success: function(response) {
                    // Replace the current content with the new email details
                    $('#v-pills-tabContent').html(response);
                },
                error: function() {
                    alert('Unable to load email details.');
                }
            });
        });
    });
</script>




      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->