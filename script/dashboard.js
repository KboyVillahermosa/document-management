document.addEventListener("DOMContentLoaded", function () {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const uploadButton = document.getElementById('uploadButton');
    let files = [];

    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.classList.add('border-blue-500');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-blue-500'));
    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropzone.classList.remove('border-blue-500');
        const droppedFiles = event.dataTransfer.files;
        handleFiles(droppedFiles);
    });

    fileInput.addEventListener('change', () => {
        const files = fileInput.files;
        handleFiles(files);
    });

    function handleFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            files.push(file);
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                <div class="file-name">${file.name}</div>
                <div class="file-actions">
                    <button type="button" onclick="removeFile(${files.length - 1})">Remove</button>
                </div>
            `;
            fileList.appendChild(listItem);
        }
    }

    function removeFile(index) {
        files.splice(index, 1);
        const fileList = document.getElementById('fileList');
        fileList.removeChild(fileList.childNodes[index]);
    }

    function uploadFiles() {
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            alert(result.message);
            files = [];
            fileList.innerHTML = '';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});