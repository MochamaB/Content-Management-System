<style>
    #drop-area {
        width: 400px;
        height: 250px;
        margin: 20px auto;
        text-align: center;
        border: 2px dashed #ccc;
        cursor: pointer;
    }

    #preview-container {
        text-align: center;
    }

    #drop-area.drag-over {
        background-color: #eee;
    }

    .preview-image {
        object-fit: cover;
        width: 200px;
        height: 120px;
        margin: 10px;
        border: 1px solid #ddd;
    }
</style>
@if(($routeParts[1] === 'create'))
<h5><b>Add Unit Photos</b></h5>
<hr>
<form method="POST" action="{{ url('listing') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div id="drop-area">
        <div class="text-center mt-1 mb-2">
            <img src="{{ url('uploads/upload.png') }}" alt="" class="img-fluid" style="max-width: 150px;">
            <h6 style="color:blue"> Drag and drop files or browse to Upload </h6>
        </div>
        <button type="button" id="browse-btn" class="btn btn-primary btn-lg text-white mb-0 me-0"> Browse Files</button>
    </div>
    <input type="file" id="file-input" name="photos[]" multiple hidden>
    <div id="preview-container"></div>

    @include('admin.CRUD.wizardbuttons')
</form>

@else
<h5 style="text-transform: capitalize;">Unit Photos &nbsp;
    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    @endif
</h5>
<hr>
<form method="POST" action="{{ route('listing.update', $listing->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    <div id="drop-area">
        <div class="text-center mt-1 mb-2">
            <img src="{{ url('uploads/upload.png') }}" alt="" class="img-fluid" style="max-width: 150px;">
            <h6 style="color:blue"> Drag and drop files or browse to Upload </h6>
        </div>

        <button type="button" id="browse-btn" class="btn btn-primary btn-lg text-white mb-0 me-0"> Browse Files</button>
    </div>
    <input type="file" id="file-input" name="photos[]" multiple hidden>
    <div id="preview-container">
        @foreach($photos as $photo)
        <div class="image-wrapper" style="position: relative; display: inline-block; margin: 0px;" data-id="{{ $photo->id }}">
            <img src="{{ $photo->getUrl() }}" alt="Image" class="preview-image" style="max-width: 150px; max-height: 150px;">
            <!-- Remove button -->
            <button type="button" class="remove-btn" style="
                position: absolute; top: 0; left: 0; background: red; color: white;
                border: none; cursor: pointer; border-radius: 50%; width: 25px; height: 25px;
            ">✕</button>
        </div>
        @endforeach
    </div>

    <!-- Hidden input field to store IDs of removed photos -->
    <input type="hidden" id="removed-photos" name="removed_photos" value="">

    <hr>
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Edit Unit Photos</button>
    </div>
</form>

@endif
<script>
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('file-input');
    const browseButton = document.getElementById('browse-btn');
    const previewContainer = document.getElementById('preview-container');

    const processedFiles = new Set(); // Set to track already processed file names

    // Browse button click triggers the hidden file input
    browseButton.addEventListener('click', function() {
        fileInput.click();
    });

    // Handle file selection from the browse input
    fileInput.addEventListener('change', function() {
        handleFiles(fileInput.files);
    });

    // Handle drag & drop logic
    dropArea.addEventListener('dragover', preventDefaults);
    dropArea.addEventListener('dragenter', preventDefaults);
    dropArea.addEventListener('dragleave', preventDefaults);
    dropArea.addEventListener('drop', handleDrop);

    // Prevent default browser behavior for drag events
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Handle dropping files into the drop area
    function handleDrop(e) {
        e.preventDefault();

        const files = e.dataTransfer.files; // Files from drag-and-drop
        const dataTransfer = new DataTransfer();

        // Add existing files already in the input
        if (fileInput.files.length > 0) {
            for (const existingFile of fileInput.files) {
                dataTransfer.items.add(existingFile);
            }
        }

        // Add new files from drag and drop
        for (const file of files) {
            dataTransfer.items.add(file);
        }

        // Update the input's files and process them
        fileInput.files = dataTransfer.files;
        handleFiles(fileInput.files);
    }

    // Process and preview files
    function handleFiles(files) {
        const dataTransfer = new DataTransfer();
        // Re-add existing files to preserve them
        for (let i = 0; i < fileInput.files.length; i++) {
            dataTransfer.items.add(fileInput.files[i]);
        }


        for (const file of files) {
            // Skip duplicate files
            if (processedFiles.has(file.name)) {
                continue;
            }

            // Validate file type
            if (!isValidFileType(file)) {
                alert(`Invalid file type: ${file.name}`);
                continue;
            }

            // Add the file to the Set to prevent duplicates
            processedFiles.add(file.name);

            // Add to new DataTransfer object to manage the input file list
            dataTransfer.items.add(file);

            // Use FileReader to generate preview
            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onload = function (e) {
                // Create a container for image and remove button
                const imageWrapper = document.createElement('div');
                imageWrapper.classList.add('image-wrapper');
                imageWrapper.style.position = 'relative';
                imageWrapper.style.display = 'inline-block';
                imageWrapper.style.margin = '10px';

                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.classList.add('preview-image');
                preview.style.maxWidth = '150px';
                preview.style.maxHeight = '150px';

                // Create the remove button
                const removeButton = document.createElement('button');
                removeButton.textContent = '✕';
                removeButton.classList.add('remove-btn');
                removeButton.style.position = 'absolute';
                removeButton.style.top = '0';
                removeButton.style.left = '0';
                removeButton.style.background = 'red';
                removeButton.style.color = 'white';
                removeButton.style.border = 'none';
                removeButton.style.cursor = 'pointer';
                removeButton.style.borderRadius = '50%';
                removeButton.style.width = '25px';
                removeButton.style.height = '25px';

                // Add remove functionality
                removeButton.addEventListener('click', function() {
                    imageWrapper.remove(); // Remove the image container
                    processedFiles.delete(file.name); // Remove the file name from the Set

                    // Update the input file list
                    const newDataTransfer = new DataTransfer();
                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (fileInput.files[i].name !== file.name) {
                            newDataTransfer.items.add(fileInput.files[i]);
                        }
                    }
                    fileInput.files = newDataTransfer.files;
                });

                // Append the remove button and preview image to the wrapper
                imageWrapper.appendChild(preview);
                imageWrapper.appendChild(removeButton);

                // Add the wrapper to the preview container
                previewContainer.appendChild(imageWrapper);
            };
            reader.readAsDataURL(file);
        }

        // Update the file input with the latest files
        fileInput.files = dataTransfer.files;
    }

    // File type validation function
    function isValidFileType(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        return allowedTypes.includes(file.type);
    }

    // Drag styling (optional: highlight the drop area on dragover)
    dropArea.addEventListener('dragover', () => {
        dropArea.classList.add('drag-over');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('drag-over');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewContainer = document.getElementById('preview-container');
        const removedPhotosInput = document.getElementById('removed-photos');

        // Array to store IDs of removed photos
        let removedPhotoIds = [];

        // Attach event listener to the preview container
        previewContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-btn')) {
                const imageWrapper = e.target.closest('.image-wrapper');
                if (imageWrapper) {
                    const photoId = imageWrapper.getAttribute('data-id');

                    // Add the ID to the removedPhotoIds array
                    if (photoId) {
                        removedPhotoIds.push(photoId);
                    }

                    // Update the hidden input field
                    removedPhotosInput.value = removedPhotoIds.join(',');

                    // Remove the image wrapper from the DOM
                    imageWrapper.remove();
                }
            }
        });
    });
</script>