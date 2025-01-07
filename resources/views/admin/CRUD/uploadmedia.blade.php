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

    .dz-preview {
        display: inline-block;
        margin: 10px;
        width: 100px;
        height: 100px;
        position: relative;
    }

    .dz-preview img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 4px;
    }

    .dz-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 12px;
        color: #ff0000;
        /* Red color for visibility */
        text-decoration: none;
        cursor: pointer;
    }

    .dz-preview:hover .dz-remove {
        display: block;
        /* Ensure it's visible when preview is hovered */
    }
    .dz-preview .dz-progress {
    display: none; /* Hide the progress bar */
}
</style>
<div class="form-group">
    <label class="label pt-2" style="font-size: 0.872rem;line-height: 1.4rem;text-transform:capitalize">
        Upload {{ $routeParts[0] }} Files</label>

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
            dictRemoveFile: "Remove",
            previewsContainer: "#dropzone-area", // Use the dropzone area for previews
            clickable: "#browse-files-btn", // Make the browse button clickable
            acceptedFiles: "image/*,video/*,.pdf,.doc,.docx", // Adjust as needed
            dictDefaultMessage: "", // Remove Dropzone's default message (we use a custom message)
            
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
            file.previewElement.querySelector(".dz-remove").innerText = "Remove"; // Customize remove link
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