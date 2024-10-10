      <!-- partial -->

          <!-- Detail View --->
          <div class=" col-md-12 mail-view " style="border:1px solid #dee2e6;">
                <div class="row">
                  <div class="col-md-12 mb-4 mt-4 pl-2" >
                    <div class="btn-toolbar">
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-arrow-left"></i></button>
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
                @php
                  $data = $notificationData['data'] ?? [];
                @endphp
                <div class="message-body">
                  <div class="sender-details">
                    <div class="details">
                      <p class="msg-subject">
                      {{ $data['subject'] ?? 'Subject' }} 
                      </p>
                      <p class="sender-email">
                        
                        <a href="#">{{ $data['user_email'] ?? 'Subject' }} </a>
                        &nbsp;<i class="ti-user"></i>
                      </p>
                    </div>
                  </div>
                  <div class="message-content">
                  {!! $emailContent !!}
                  </div>
                  <div class="attachments-sections">
   
                  </div>
                </div>

          </div>
     



      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->