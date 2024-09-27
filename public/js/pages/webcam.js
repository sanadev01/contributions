document.getElementById('openUploadModalBtn').addEventListener('click', function (event) {
    event.preventDefault();
    $('#uploadModal').modal('show');
}); 
const webcamBtn = document.getElementById('webcamBtn');
const fileBtn = document.getElementById('fileBtn');
const fileInput = document.getElementById('fileInput');
const previewContainer = document.getElementById('previewContainer');
const webcamContainer = document.querySelector('.webcam-container');
const webcamVideo = document.getElementById('webcam-video');
const takePhotoBtn = document.getElementById('takePhotoBtn');
let stream;

webcamBtn.onclick = async function () {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        try {
            const constraints = {
                video: true,
                facingMode: 'environment' // Prefer environment-facing camera (rear camera)
            };

            stream = await navigator.mediaDevices.getUserMedia(constraints);
            webcamVideo.srcObject = stream;
            webcamContainer.style.display = 'block';
        } catch (error) {
            console.error('Error accessing webcam:', error);
        }
    } else {
        alert('Your browser does not support the webcam feature.');
    }
};

takePhotoBtn.onclick = function () {
    const randomString = getString(10);
    const canvas = document.createElement('canvas');
    const previewWidth = 170; // Preview size
    const previewHeight = 170;
    const saveWidth = 800; // Save size
    const saveHeight = 800;

    canvas.width = saveWidth;
    canvas.height = saveHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(webcamVideo, 0, 0, saveWidth, saveHeight);

    canvas.toBlob(function (blob) {
        const fileName = `${randomString}-${previewContainer.childElementCount + 1}.png`;
        const file = new File([blob], fileName, {
            type: 'image/png'
        });
        addImageToPreview(file, canvas, fileName, previewWidth, previewHeight);
    }, 'image/png', 1); // Adjust the image quality here (1 for highest quality)
};

fileBtn.onclick = function () {
    fileInput.click();
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    webcamContainer.style.display = 'none';
};

fileInput.onchange = function (event) {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const previewWidth = 170; // Preview size
                const previewHeight = 170;
                const saveWidth = 800; // Save size
                const saveHeight = 800;

                canvas.width = saveWidth;
                canvas.height = saveHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, saveWidth, saveHeight);
                addImageToPreview(file, canvas, file.name, previewWidth, previewHeight);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
};

function addImageToPreview(file, canvas, fileName, previewWidth, previewHeight) {
    const img = new Image();
    img.src = canvas.toDataURL('image/png', 1); // Adjust image quality here as well
    img.style.width = `${previewWidth}px`; // Display in preview size
    img.style.height = `${previewHeight}px`;
    img.classList.add('mr-2');

    const previewDiv = document.createElement('div');
    previewDiv.style.position = 'relative';
    previewDiv.style.display = 'inline-block';
    previewDiv.style.marginRight = '5px';
    previewDiv.style.marginBottom = '5px';
    previewDiv.appendChild(img);

    const removeBtn = document.createElement('button');
    removeBtn.style.display = 'block';
    removeBtn.style.marginTop = '5px';
    removeBtn.style.marginBottom = '5px';
    removeBtn.style.marginLeft = '-3px';
    removeBtn.style.backgroundColor = 'red'; // Red background
    removeBtn.style.color = 'white'; // White text
    removeBtn.style.border = '2px solid red'; // Red border
    removeBtn.style.borderRadius = '5px';
    removeBtn.style.padding = '2px 7px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.width = '100%';
    removeBtn.style.textAlign = 'center';
    removeBtn.innerHTML = 'Remove Photo';
    removeBtn.onclick = function () {
        previewDiv.remove();
        updateFileInput();
    };

    previewDiv.appendChild(removeBtn);
    previewContainer.appendChild(previewDiv);

    updateFileInput();
}

function addOldImageToPreview(file, canvas, fileName, previewWidth, previewHeight) {
    const img = new Image();
    img.src = file; // Adjust image quality here as well
    img.style.width = `${previewWidth}px`; // Display in preview size
    img.style.height = `${previewHeight}px`;
    img.classList.add('mr-2');

    const previewDiv = document.createElement('div');
    previewDiv.style.position = 'relative';
    previewDiv.style.display = 'inline-block';
    previewDiv.style.marginRight = '5px';
    previewDiv.style.marginBottom = '5px';
    previewDiv.appendChild(img);

    const removeBtn = document.createElement('button');
    removeBtn.style.display = 'block';
    removeBtn.style.marginTop = '5px';
    removeBtn.style.marginBottom = '5px';
    removeBtn.style.marginLeft = '-3px';
    removeBtn.style.backgroundColor = 'red'; // Red background
    removeBtn.style.color = 'white'; // White text
    removeBtn.style.border = '2px solid red'; // Red border
    removeBtn.style.borderRadius = '5px';
    removeBtn.style.padding = '2px 7px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.width = '100%';
    removeBtn.style.textAlign = 'center';
    removeBtn.innerHTML = 'Delete Photo';
    removeBtn.onclick = function () {
        previewDiv.remove();
        updateFileInput();
    };
    previewDiv.appendChild(removeBtn);
    previewContainer.appendChild(previewDiv);

}



function updateFileInput() {
    const dataTransfer = new DataTransfer();
    const previews = previewContainer.querySelectorAll('img');
    previews.forEach((img, index) => {
        const canvas = document.createElement('canvas');
        const saveWidth = 800; // Save size
        const saveHeight = 800;

        canvas.width = saveWidth;
        canvas.height = saveHeight;
        const ctx = canvas.getContext('2d');
        const src = img.src;
        const image = new Image();
        image.onload = function () {
            ctx.drawImage(image, 0, 0, saveWidth, saveHeight);
            canvas.toBlob(function (blob) {
                const randomString = getString(10);
                const fileName = `${randomString}.png`;
                const file = new File([blob], fileName, {
                    type: 'image/png'
                });
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
            }, 'image/png', 1); // Adjust image quality here as well
        };
        image.src = src;
    });
}

document.getElementById('closeModal').addEventListener('click', function (event) {
    event.preventDefault();
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    webcamContainer.style.display = 'none';
});

function getString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
} 
