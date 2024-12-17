<div class="row">
    <div class="col-md-7">

        <div class=" contwrapper">

            <h5><b> Unit Details</b>
                
            </h4></br>
            <hr>
            @if( $unitdetails->isempty())
           
            @else
            <div class=" table-responsive " id="dataTable">
           
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