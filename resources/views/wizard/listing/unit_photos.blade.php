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
        Drag here to preview
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
        <img src="{{ $photo->getUrl() }}" alt="Image" class="preview-image">
        @endforeach
    </div>

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

            // Use FileReader to generate preview
            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onloadend = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.classList.add('preview-image');
                previewContainer.appendChild(preview);
            };
        }
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

