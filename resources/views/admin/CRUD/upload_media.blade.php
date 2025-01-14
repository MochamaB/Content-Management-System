<style>
    #dropzone-area {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background-color: #f9f9f9;
        cursor: pointer;
        position: relative;
    }

    #dropzone-area .dz-message {
        font-size: 16px;
        font-weight: bold;
        color: #888;
    }

    #browse-files-btn {
        margin-top: 10px;
        padding: 5px 15px;
        font-size: 14px;
    }

    .dropzone .dz-preview {
        position: relative;
        display: inline-block;
        margin: 10px;
        width: 100px;
        height: 120px;

    }

    .dropzone .dz-preview .dz-image {
        max-width: 100%;
        max-height: 80%;
        border-radius: 0px;
    }

    .dropzone .dz-preview .dz-remove {
        font-size: 0.9rem;
        color: #ff0000;
        /* Red color for visibility */
        text-decoration: none;
        cursor: pointer;
        margin-top: 5px;
        /* Add space above */
        margin-bottom: 10px;
        /* Add space below to avoid obstruction */
    }


    .dz-preview:hover .dz-remove {
        display: block;
        /* Ensure it's visible when preview is hovered */
    }

    .dz-preview .dz-progress {
        display: none;
        /* Hide the progress bar */
    }

    .remove-btn {
        display: block;
        color: #ff0000;
        border: none;
        cursor: pointer;
        text-align: center;
        margin-top: 5px;
        /* Add space above */
        margin-bottom: 10px;

    }
</style>

@if(isset($existingMedia))
<div class="form-group">
    <label class="label pt-2" style="font-size: 0.872rem;line-height: 1.4rem;text-transform:capitalize">
        Existing {{ $routeParts[0] }} Files</label>
</div>
<div id="preview-container">
    @foreach($existingMedia as $file)
    @php
        // Determine the appropriate thumbnail based on the MIME type
        $mimeType = $file->mime_type;
        $thumbnail = '';

        if (str_contains($mimeType, 'image')) {
            $thumbnail = $file->getUrl(); // Use the original image URL
        } elseif ($mimeType === 'application/pdf') {
            $thumbnail = url('uploads/pdf.png'); // Placeholder for PDFs
        } elseif (str_contains($mimeType, 'video')) {
            $thumbnail = url('uploads/video.png'); // Placeholder for videos
        } elseif (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            $thumbnail = url('uploads/doc.png'); // Placeholder for Word documents
        } elseif (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            $thumbnail = url('uploads/excel.png'); // Placeholder for Excel files
        } else {
            $thumbnail = url('uploads/nofile.png'); // Generic placeholder for unsupported types
        }
    @endphp
    <div class="image-wrapper" 
     style="position: relative; display: inline-block; margin: 0px; text-align: center;" 
     data-id="{{ $file->id }}">
    <!-- Image -->
    <img src="{{ $thumbnail }}" alt="Image" class="preview-image" 
         style="width: 150px; height: 150px; object-fit: cover; display: block; margin: 0 auto;">

    <!-- Text and Button Wrapper -->
    <div style="display: flex; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
        <!-- File Name -->
        <span class="text-small" style="font-size: 14px;">{{$file->name}}</span>

        <!-- Remove Button -->
        <button type="button" class="remove-btn" style="background-color: transparent; border: none; cursor: pointer;">
            <i class="mdi mdi-delete" style="font-size: 1.2rem; color: red; cursor: pointer;"></i>
        </button>
    </div>
</div>

    @endforeach
</div>

<!-- Hidden input field to store IDs of removed photos -->
<input type="hidden" id="deleted-files" name="deleted_files" value="">

<hr>
@endif
<div class="form-group">
    <label class="label pt-2" style="font-size: 0.872rem;line-height: 1.4rem;text-transform:capitalize">
        </label>

    <div id="dropzone-area" class="dropzone">
        <div class="dz-message d-flex align-items-center justify-content-center">
            <i class="mdi mdi-cloud-upload me-2" style="font-size:1.5rem"></i>
            <span class="text-muted" style="font-size:1em; margin-right: 10px;">Drag files here to upload or</span>
            <button type="button" id="browse-files-btn" class="btn btn-primary btn-lg text-white">Browse</button>
        </div>
    </div>

    <input type="file" name="uploaded_files[]" id="uploaded_files" multiple hidden>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Dropzone
        Dropzone.autoDiscover = false;

        const dropzone = new Dropzone("#dropzone-area", {
            url: "/dummy-upload-url", // Dummy URL (not used for uploading in this setup)
            autoProcessQueue: false, // Prevent auto-upload
            addRemoveLinks: true, // Show "Remove file" link
            previewsContainer: "#dropzone-area", // Use the dropzone area for previews
            clickable: "#browse-files-btn", // Make the browse button clickable
            acceptedFiles: "image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx", // Adjust as needed
            dictDefaultMessage: "", // Remove Dropzone's default message (we use a custom message)
            dictRemoveFile: '<i class="mdi mdi-delete" style="font-size: 1.2rem; color: red; cursor: pointer;"></i>',
        });

        // Hidden input to collect files for the parent form
        const uploadedFilesInput = document.getElementById("uploaded_files");

        dropzone.on("addedfile", function(file) {
            // Create a FileList-like object to store all files
            const dataTransfer = new DataTransfer();

            // Add previously collected files
            for (const oldFile of uploadedFilesInput.files) {
                dataTransfer.items.add(oldFile);
            }

            // Add the new file to the collection
            dataTransfer.items.add(file);

            // Update the hidden input
            uploadedFilesInput.files = dataTransfer.files;

            // Optionally customize preview behavior (e.g., restrict size or add custom buttons)
       //     file.previewElement.querySelector(".dz-remove").innerText = "Remove"; // Customize remove link
        });

        dropzone.on("removedfile", function(file) {
            // Remove file from the FileList
            const dataTransfer = new DataTransfer();

            // Keep all files except the removed one
            for (const oldFile of uploadedFilesInput.files) {
                if (oldFile.name !== file.name) {
                    dataTransfer.items.add(oldFile);
                }
            }

            // Update the hidden input
            uploadedFilesInput.files = dataTransfer.files;
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewContainer = document.getElementById('preview-container');
        const removedFilesInput = document.getElementById('deleted-files');

        // Array to store IDs of removed photos
        let removedFilesIds = [];

        // Attach event listener to the preview container
        previewContainer.addEventListener('click', function(e) {
            // Ensure that the clicked target is the remove button
            if (e.target.closest('.remove-btn')) {
                const imageWrapper = e.target.closest('.image-wrapper');

                if (imageWrapper) {
                    const fileId = imageWrapper.getAttribute('data-id');
                    // Just for debugging

                    // Add the ID to the removedFilesIds array
                    if (fileId) {
                        removedFilesIds.push(fileId);
                    }

                    // Update the hidden input field with the removed file IDs
                    removedFilesInput.value = removedFilesIds.join(',');

                    // Remove the image wrapper from the DOM
                    imageWrapper.remove();
                }
            }
        });
    });
</script>