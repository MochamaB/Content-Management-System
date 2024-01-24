      <!-- partial -->

          <!-- Detail View --->
          <div class=" col-md-9 mail-view " style="border:1px solid #dee2e6;">
                <div class="row">
                  <div class="col-md-12 mb-4 mt-4">
                    <div class="btn-toolbar">
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary"><i class="ti-share-alt text-primary me-1"></i> Reply</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"><i class="ti-share-alt text-primary me-1"></i>Reply All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"><i class="ti-share text-primary me-1"></i>Forward</button>
                      </div>
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary"><i class="ti-clip text-primary me-1"></i>Attach</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"><i class="ti-trash text-primary me-1"></i>Delete</button>
                      </div>
                    </div>
                  </div>
                </div>
                @php
                      $data = json_decode($notificationData->data, true);
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
                    <p>Hi Emily,</p>
                    <p>This week has been a great week and the team is right on schedule with the set deadline. The team has made great progress and achievements this week. At the current rate we will be able to deliver the product right on time and meet the quality that is expected of us. Attached are the seminar report held this week by our team and the final product design that needs your approval at the earliest.</p>
                    <p>For the coming week the highest priority is given to the development for <a href="http://www.bootstrapdash.com/" target="_blank">http://www.bootstrapdash.com/</a> once the design is approved and necessary improvements are made.</p>
                    <p><br><br>Regards,<br>Sarah Graves</p>
                  </div>
                  <div class="attachments-sections">
                    <ul>
                      <li>
                        <div class="thumb"><i class="ti-file"></i></div>
                        <div class="details">
                          <p class="file-name">Seminar Reports.pdf</p>
                          <div class="buttons">
                            <p class="file-size">678Kb</p>
                            <a href="#" class="view">View</a>
                            <a href="#" class="download">Download</a>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="thumb"><i class="ti-image"></i></div>
                        <div class="details">
                          <p class="file-name">Product Design.jpg</p>
                          <div class="buttons">
                            <p class="file-size">1.96Mb</p>
                            <a href="#" class="view">View</a>
                            <a href="#" class="download">Download</a>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

          </div>
     



      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->