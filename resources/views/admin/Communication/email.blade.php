
    <div class="row">
        <!---- mail summary ---->
        <div class="mail-list-container col-md-3 pt-4 pb-4 border-right bg-white">
            <div class="border-bottom pb-4 mb-3 px-3">
                <div class="form-group">
                    <input class="form-control w-100" type="search" placeholder="Search mail" id="Mail-rearch">
                </div>
            </div>
             <!--- Unread Notifications --->
             @foreach ($unreadNotifications as $notification)
             <div class="mail-list new_mail">
                <div class="form-check"> <label class="form-check-label"> <input type="checkbox" class="form-check-input" checked=""> <i class="input-helper"></i></label></div>
                <div class="content">
                    <p class="sender-name">Subject</p>
                    <p class="message_text">Heading of the email</p>
                </div>
                <div class="details">
                    <i class="mdi mdi-star favorite"></i>
                </div>
            </div>
            @endforeach
            <!--- Read Notifications --->
            <div class="mail-list">
                <div class="form-check"> <label class="form-check-label"> <input type="checkbox" class="form-check-input"> <i class="input-helper"></i></label></div>
                <div class="content">
                    <p class="sender-name">Subject</p>
                    <p class="message_text">Heading of the email</p>
                </div>
                <div class="details">
                    <i class="mdi mdi-star-outline"></i>
                </div>
            </div>
            
        </div>
        <!-- --------->
        <!-- mail view ----->
        <div class="mail-view d-none d-md-block col-md-8 bg-white" style="border:1px solid #dee2e6;">
            <div class="row">
                <div class="col-md-10 mb-4 mt-4">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-reply text-primary mr-1"></i> Reply</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-reply-all text-primary mr-1"></i>Reply All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-share text-primary mr-1"></i>Forward</button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-attachment text-primary mr-1"></i>Attach</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-delete text-primary mr-1"></i>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="message-body">
                <div class="sender-details">
                    
                    <div class="details">
                        <p class="msg-subject">
                            Subject (Date Created)
                        </p>
                        <p class="sender-email">
                            User Name
                            <a href="#">useremail@gmail.com</a>
                            &nbsp;<i class="mdi mdi-account-multiple-plus"></i>
                        </p>
                    </div>
                </div>
                <div class="message-content">
                    <p>Hi user->firstname,</p>
                    <p>This week has been a great week and the team is right on schedule with the set deadline. The team has made great progress and achievements this week. At the current rate we will be able to deliver the product right on time and meet the quality that is expected of us. Attached are the seminar report held this week by our team and the final product design that needs your approval at the earliest.</p>
                    <p>For the coming week the highest priority is given to the development for <a href="http://www.urbanui.com/" target="_blank">http://www.urbanui.com/</a> once the design is approved and necessary improvements are made.</p>
                    <p><br><br>Regards,<br>Sarah Graves</p>
                </div>
                <div class="attachments-sections">
                    <ul>
                        <li>
                            <div class="thumb"><i class="mdi mdi-file-pdf"></i></div>
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
                            <div class="thumb"><i class="mdi mdi-file-image"></i></div>
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
    </div>