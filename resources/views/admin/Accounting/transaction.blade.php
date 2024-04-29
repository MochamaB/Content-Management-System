@extends('layouts.admin.admin')

@section('content')

<ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home-1" role="tab" aria-controls="home-1" aria-selected="true">Home</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile-1" role="tab" aria-controls="profile-1" aria-selected="false" tabindex="-1">Profile</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact-1" role="tab" aria-controls="contact-1" aria-selected="false" tabindex="-1">Contact</a>
                    </li>
                  </ul>
<div class="tab-content">
                    <div class="tab-pane fade show active" id="home-1" role="tabpanel" aria-labelledby="home-tab">
                      <div class="media">
                        <img class="me-3 w-25 rounded" src="../../../assets/images/samples/tab.jpg" alt="sample image">
                        <div class="media-body">
                          <h4 class="mt-0">Why choose us?</h4>
                          <p>
                            Far curiosity incommode now led smallness allowance. Favour bed assure son things yet. She
                            consisted
                            consulted elsewhere happiness disposing household any old the. Widow downs you new shade
                            drift hopes
                            small. So otherwise commanded sweetness we improving. Instantly by daughters resembled
                            unwilling principle
                            so middleton.
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="profile-1" role="tabpanel" aria-labelledby="profile-tab">
                      <div class="media">
                        <img class="me-3 w-25 rounded" src="../../../assets/images/faces/face12.jpg" alt="sample image">
                        <div class="media-body">
                          <h4 class="mt-0">John Doe</h4>
                          <p>
                            Fail most room even gone her end like. Comparison dissimilar unpleasant six compliment two
                            unpleasing
                            any add. Ashamed my company thought wishing colonel it prevent he in. Pretended residence
                            are something
                            far engrossed old off.
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="contact-1" role="tabpanel" aria-labelledby="contact-tab">
                      <h4>Contact us </h4>
                      <p>
                        Feel free to contact us if you have any questions!
                      </p>
                      <p>
                        <i class="ti-headphone-alt text-info"></i>
                        +123456789
                      </p>
                      <p>
                        <i class="ti-email text-success"></i>
                        contactus@example.com
                      </p>
                    </div>
                  </div>




@endsection