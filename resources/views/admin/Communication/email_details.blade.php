      <!-- partial -->

      <!-- Detail View --->
      <div class=" col-md-12 mail-view " style="border:1px solid #dee2e6;">
        <div class="row">
          <div class="col-md-12 mb-4 mt-4">
            <div class="btn-toolbar">
              <div class="btn-group pl-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="back-to-summary"><i class="mdi mdi-arrow-left"></i> Back</button>
                <button type="button" class="btn btn-sm btn-outline-primary"><i class="ti-share-alt text-primary me-1"></i> Reply</button>
                <button type="button" class="btn btn-sm btn-outline-primary"><i class="ti-share-alt text-primary me-1"></i>Reply All</button>

              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary"><i class="ti-clip text-primary me-1"></i>Attach</button>
                <button type="button" class="btn btn-sm btn-outline-primary"><i class="ti-trash text-primary me-1"></i>Delete</button>
              </div>
            </div>
          </div>
        </div>

        <div class="message-body">
          <div class="sender-details">
            <div class="details">
              <p class="msg-subject">
                <!-- Access subject from notificationData -->
                {{ $notificationData['subject'] ?? 'Subject' }}
              </p>
              <p class="sender-email">
                <a href="#"><i class="ti-user"></i>{{ $notificationData['user_email'] ?? 'email' }} </a>
                &nbsp;
              </p>
            </div>
          </div>
          <div class="message-content">
          @include($templateView, $templateData)
          </div>
          <div class="attachments-sections">

          </div>
        </div>

      </div>
      <script>
        $(document).ready(function() {
          $('#back-to-summary').on('click', function() {
            // Update the URL to return to the email summary
            window.history.pushState({}, '', '/notification/email');

            // Perform AJAX to reload the email summary
            $.ajax({
              url: '/notification/email', // This URL should return the email summary view
              method: 'GET',
              success: function(response) {
                // Replace the content with the email summary view
                $('#v-pills-tabContent').html(response);
              },
              error: function() {
                alert('Unable to load email summary.');
              }
            });
          });
        });
      </script>



      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->