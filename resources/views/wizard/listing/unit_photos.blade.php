<style>
    #drop-area {
  width: 400px;
  height: 200px;
  margin: 20px auto;
  text-align: center;
  line-height: 200px;
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
  width: 100px;
  height: 100px;
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

@elseif(($routeParts[1] === 'edit'))
@endif
<script>
    const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('file-input');

// Utility function to prevent default browser behavior
function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

// Preventing default browser behavior when dragging a file over the container
dropArea.addEventListener('dragover', preventDefaults);
dropArea.addEventListener('dragenter', preventDefaults);
dropArea.addEventListener('dragleave', preventDefaults);

// Handling dropping files into the area
dropArea.addEventListener('drop', handleDrop);

// We’ll discuss `handleDrop` function down the road
function handleDrop(e) {
  e.preventDefault();

  // Getting the list of dragged files
  const files = e.dataTransfer.files;

  // Checking if there are any files
  if (files.length) {
    // Assigning the files to the hidden input from the first step
    fileInput.files = files;

    // Processing the files for previews (next step)
    handleFiles(files);
  }
}

// We’ll discuss `handleFiles` function down the road
function handleFiles(files) {
  for (const file of files) {
    // Initializing the FileReader API and reading the file
    const reader = new FileReader();
    reader.readAsDataURL(file);

    // Once the file has been loaded, fire the processing
    reader.onloadend = function (e) {
      const preview = document.createElement('img');

      if (isValidFileType(file)) {
        preview.src = e.target.result;
      }

      // Apply styling
      preview.classList.add('preview-image');
      const previewContainer = document.getElementById('preview-container');
      previewContainer.appendChild(preview);
    };
  }
}

// We’ll discuss `isValidFileType` function down the road
function isValidFileType(file) {
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  return allowedTypes.includes(file.type);
}


dropArea.addEventListener('dragover', () => {
  dropArea.classList.add('drag-over');
});

dropArea.addEventListener('dragleave', () => {
  dropArea.classList.remove('drag-over');
});
</script>