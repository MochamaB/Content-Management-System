<div class="row">
    <div class="col-md-7">

        <div class=" contwrapper">

            <h4><b> Unit Details</b>
                @if( Auth::user()->can('unitdetail.create') || Auth::user()->id === 1)
                <a href="" class="btn btn-primary btn-lg text-white mb-0 me-0 float-end" role="button" data-toggle="modal" data-target="#myModal">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    Add Unit Detail
                </a>
                @endif
            </h4></br>
            <hr>
            @if( $unitdetails->isempty())
            <div class="col-md-10" style="border-left: 10px solid red;padding:30px;background-color:#FBCEB1">
                <h5>Unit Details have not been added!.
                </h5>
            </div>
            @else
            <div class=" table-responsive " id="dataTable">
                <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered table-striped">
                    <thead>
                        <tr>

                            <th data-sortable="true"></th>
                            <th data-sortable="true">Slug</th>
                            <th data-sortable="true">Desc</th>
                            <th data-sortable="true">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unitdetails as $key=> $unitdetail)
                        <tr>
                            <td style="padding:15px;">{{$key+1}}</td>
                            <td style="padding:15px;">{{$unitdetail->slug}}</td>
                            <td style="padding:15px;">
                            @if($unitdetail->slug === 'photo')
                            <img class="" src="{{ asset('resources/uploads/images/property/'.$unitdetail->desc) }}" style="width: 150px;height:100px;">
                            @else
                                {{$unitdetail->desc}}
                            @endif
                            </td>
                            <td>
                            <form action="{{ url('unitdetail/'.$unitdetail->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                @if( Auth::user()->can('unitdetail.delete') || Auth::user()->id === 1)
                                <button type="submit" class="" style="border:0px;padding:5px"><i class="mdi mdi-delete mdi-24px text-danger"></i></button>
                                @endif
                            </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>


    <div class="col-md-5" style="padding-top: 55px;background-color: #dfebf3;border: 1px solid #7fafd0;">
        <h4><b> Unit Information </b> &nbsp;
            
        </h4>
        <hr>

        

    </div>
</div>
<!------------- Modal for adding properties ------------->

<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Photos</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Add your modal content here -->
                <form method="POST" action="{{ url('unitdetail') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-10">
                        <div class="form-group">
                            <input type="hidden" name="unit_id" class="form-control" value="{{$unit->id}}" />
                            <input type="hidden" name="property_id" class="form-control" value="{{$unit->property_id}}" />
                            <label class="label">Type (slug) <span class="requiredlabel">*</span></label>
                            <select name="slug" id="slug" class="formcontrol2" required>
                                <option value="photo">Photo (Photos of units)</option>
                                <option value="video">Video (Enter video url)</option>
                                <option value="title">Title (Listing title)</option>
                                <option value="listingdesc">Information (Listing Information)</option>
                                <option value="feature">Additional Listing feature</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="label">Description <span class="requiredlabel">*</span></label>
                            <input type="file" name="desc" class="form-control" id="logo" value="" />
                            <img id="logo-image-before-upload" src="{{ asset('resources/uploads/images/noimage.jpg') }}" style="height: 200px; width: 300px;">
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submit">Upload</button>
                </form>
            </div>


        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const slug = document.getElementById("slug");
        const desc = document.getElementById("logo");
        const imagePreview = document.getElementById("logo-image-before-upload");

            slug.addEventListener("change", function() {
                const slugtype = slug.value;
           //     alert(slugtype);
               
                if (slugtype === "photo") {
                    desc.type = "file";
                    imagePreview.style.display = "block";
                } else {
                    desc.type = "text";
                    imagePreview.style.display = "none";

                }
            });
    });
</script>