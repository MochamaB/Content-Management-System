<style>
    #drop-area {
        width: 100%;
        max-width: 400px; /* Limit maximum width */
        height: auto; /* Allow height to adjust naturally */
        min-height: 250px; /* Maintain minimum height for usability */
        margin: 20px auto;
        text-align: center;
        border: 2px dashed #ccc;
        cursor: pointer;
        box-sizing: border-box;
    }
    #drop-area h6 {
    font-size: 1rem; /* Make text responsive */
    color: blue;
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
        margin: 0px;
        border: 1px solid #ddd;
    }
</style>
<div class="form-group">
    <label class="label pt-2" style="font-size: 0.872rem;line-height: 1.4rem;text-transform:capitalize">
        Upload {{ $routeParts[0] }} Files</label>

    <div id="drop-area">
        <div class="text-center mt-1 mb-2">
            <img src="{{ url('uploads/upload.png') }}" alt="" class="img-fluid" style="max-width: 150px;">
            <h6> Drag and drop files or browse to Upload </h6>
        </div>
        <button type="button" id="browse-btn" class="btn btn-primary btn-lg text-white mb-0 me-0"> Browse Files</button>
    </div>
    <input type="file" id="file-input" name="files[]" multiple hidden>

    @if(!($routeParts[1] === 'create'))
        <!-- Media Collections Tabs - Only show in edit if there are existing files -->
        @if($model->getMedia('images')->count() > 0 || 
            $model->getMedia('documents')->count() > 0 || 
            $model->getMedia('videos')->count() > 0)
            
            <ul class="nav nav-tabs mt-4" id="mediaCollectionTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="images-tab" data-bs-toggle="tab" href="#images" role="tab">Images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab">Documents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="videos-tab" data-bs-toggle="tab" href="#videos" role="tab">Videos</a>
                </li>
            </ul>

            <div class="tab-content" id="mediaCollectionContent">
                <!-- Images Tab -->
                <div class="tab-pane fade show active" id="images" role="tabpanel">
                    <div class="preview-container">
                        @foreach($model->getMedia('images') as $media)
                            <div class="file-wrapper" style="position: relative; display: inline-block; margin: 10px;" data-id="{{ $media->id }}">
                                <img src="{{ $media->getUrl() }}" alt="Image" class="preview-image" style="max-width: 150px; max-height: 150px;">
                                @if(Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                                    <button type="button" class="remove-btn" style="position: absolute; top: 0; left: 0; background: red; color: white; border: none; cursor: pointer; border-radius: 50%; width: 25px; height: 25px;">✕</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="preview-container">
                        @foreach($model->getMedia('documents') as $media)
                            <div class="file-wrapper" style="position: relative; display: inline-block; margin: 10px;" data-id="{{ $media->id }}">
                                <div class="file-preview" style="width: 150px; height: 150px; border: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <i class="mdi mdi-file-document" style="font-size: 48px;"></i>
                                    <span style="font-size: 12px; margin-top: 10px; word-break: break-word; padding: 0 5px;">{{ $media->file_name }}</span>
                                    <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-info mt-2" target="_blank">View</a>
                                </div>
                                @if(Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                                    <button type="button" class="remove-btn" style="position: absolute; top: 0; left: 0; background: red; color: white; border: none; cursor: pointer; border-radius: 50%; width: 25px; height: 25px;">✕</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Videos Tab -->
                <div class="tab-pane fade" id="videos" role="tabpanel">
                    <div class="preview-container">
                        @foreach($model->getMedia('videos') as $media)
                            <div class="file-wrapper" style="position: relative; display: inline-block; margin: 10px;" data-id="{{ $media->id }}">
                                <video width="200" height="150" controls>
                                    <source src="{{ $media->getUrl() }}" type="{{ $media->mime_type }}">
                                    Your browser does not support the video tag.
                                </video>
                                @if(Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                                    <button type="button" class="remove-btn" style="position: absolute; top: 0; left: 0; background: red; color: white; border: none; cursor: pointer; border-radius: 50%; width: 25px; height: 25px;">✕</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Hidden input for removed media -->
            <input type="hidden" id="removed-media" name="removed_media" value="">
        @endif
    @endif
</div>

<script>
 document.addEventListener('DOMContentLoaded', function() {
    // Common elements for both create and edit
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('file-input');
    const browseButton = document.getElementById('browse-btn');
    
    // Check if we're in edit or create mode
    const isEditMode = window.location.href.includes('/edit');
    
    // If these essential elements don't exist, don't proceed
    if (!dropArea || !fileInput || !browseButton) {
        console.log('Required elements not found');
        return;
    }

    console.log('Elements found, initializing upload functionality');

    // Initialize tabs for both create and edit modes
    if (!document.getElementById('mediaCollectionTabs')) {
        // Create and append tabs structure if it doesn't exist
        const tabsStructure = `
            <ul class="nav nav-tabs mt-4" id="mediaCollectionTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="images-tab" data-bs-toggle="tab" href="#images" role="tab">Images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab">Documents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="videos-tab" data-bs-toggle="tab" href="#videos" role="tab">Videos</a>
                </li>
            </ul>
            <div class="tab-content" id="mediaCollectionContent">
                <div class="tab-pane fade show active" id="images" role="tabpanel">
                    <div class="preview-container"></div>
                </div>
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="preview-container"></div>
                </div>
                <div class="tab-pane fade" id="videos" role="tabpanel">
                    <div class="preview-container"></div>
                </div>
            </div>
        `;
        
        // Insert tabs after drop area
        dropArea.insertAdjacentHTML('afterend', tabsStructure);
    }

    const processedFiles = new Set();

    browseButton.addEventListener('click', function(e) {
        console.log('Browse button clicked');
        fileInput.click();
    });

    fileInput.addEventListener('change', function(e) {
        console.log('Files selected:', this.files);
        handleFiles(this.files);
    });

    // Drag and drop handlers
    dropArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.classList.add('drag-over');
    });

    dropArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.classList.remove('drag-over');
    });

    dropArea.addEventListener('drop', function(e) {
        console.log('Files dropped');
        e.preventDefault();
        e.stopPropagation();
        dropArea.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    function handleFiles(files) {
        console.log('Processing files:', files);
        
        Array.from(files).forEach(file => {
            if (processedFiles.has(file.name)) {
                console.log('File already processed:', file.name);
                return;
            }

            if (!isValidFileType(file)) {
                console.log('Invalid file type:', file.name);
                alert(`Invalid file type: ${file.name}`);
                return;
            }

            processedFiles.add(file.name);
            
            const reader = new FileReader();
            reader.onload = function(e) {
                createPreviewElement(file, e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    function getCollectionName(mimeType) {
        if (mimeType.startsWith('image/')) return 'images';
        if (mimeType.startsWith('video/')) return 'videos';
        return 'documents';
    }

    function createPreviewElement(file, dataUrl) {
        const collectionName = getCollectionName(file.type);
        const previewContainer = document.querySelector(`#${collectionName} .preview-container`);
        
        if (!previewContainer) {
            console.error(`Preview container for ${collectionName} not found`);
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'file-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';
        wrapper.style.margin = '10px';

        let preview;
        if (file.type.startsWith('image/')) {
            preview = document.createElement('img');
            preview.src = dataUrl;
            preview.className = 'preview-image';
            preview.style.maxWidth = '150px';
            preview.style.maxHeight = '150px';
        } else if (file.type.startsWith('video/')) {
            preview = document.createElement('video');
            preview.src = dataUrl;
            preview.controls = true;
            preview.style.maxWidth = '200px';
            preview.style.maxHeight = '150px';
        } else {
            preview = document.createElement('div');
            preview.className = 'file-preview';
            preview.style.width = '150px';
            preview.style.height = '150px';
            preview.style.border = '1px solid #ddd';
            preview.style.display = 'flex';
            preview.style.flexDirection = 'column';
            preview.style.alignItems = 'center';
            preview.style.justifyContent = 'center';

            const icon = document.createElement('i');
            icon.className = getFileIcon(file.type);
            icon.style.fontSize = '48px';

            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            fileName.style.fontSize = '12px';
            fileName.style.marginTop = '10px';
            fileName.style.wordBreak = 'break-word';
            fileName.style.padding = '0 5px';

            preview.appendChild(icon);
            preview.appendChild(fileName);
        }

        // Add remove button
        const removeButton = document.createElement('button');
        removeButton.textContent = '✕';
        removeButton.className = 'remove-btn';
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

        removeButton.addEventListener('click', function() {
            wrapper.remove();
            processedFiles.delete(file.name);
        });

        wrapper.appendChild(preview);
        wrapper.appendChild(removeButton);
        
        // Show the appropriate tab when adding a file
        const tabElement = document.querySelector(`#${collectionName}-tab`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
        
        previewContainer.appendChild(wrapper);
    }

    function isValidFileType(file) {
        const allowedTypes = {
            images: ['image/jpeg', 'image/png', 'image/gif'],
            videos: ['video/mp4', 'video/quicktime', 'video/mpeg'],
            documents: [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        };

        return Object.values(allowedTypes).flat().includes(file.type);
    }

    function getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return 'mdi mdi-file-image';
        if (fileType.startsWith('video/')) return 'mdi mdi-file-video';
        if (fileType.includes('pdf')) return 'mdi mdi-file-pdf';
        if (fileType.includes('word')) return 'mdi mdi-file-word';
        if (fileType.includes('excel')) return 'mdi mdi-file-excel';
        return 'mdi mdi-file-document';
    }

    // Handle existing media removal in edit mode
    if (isEditMode) {
        const removedMediaInput = document.getElementById('removed-media');
        if (removedMediaInput) {
            const removedMediaIds = [];
            document.querySelectorAll('.remove-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const wrapper = this.closest('[data-id]');
                    if (wrapper) {
                        const mediaId = wrapper.getAttribute('data-id');
                        removedMediaIds.push(mediaId);
                        removedMediaInput.value = removedMediaIds.join(',');
                        wrapper.remove();
                    }
                });
            });
        }
    }
});
</script>