function showSuccessToast(message) {
var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-success border border-success transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body d-flex align-items-start">
        <div style="width: 24px; text-align: center;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="w-100 mx-2 text-start" id="toast-message">
            ${message}
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>`;
var toastElement = $(toastHTML);
$("#toastContainer").append(toastElement); // Make sure there's a container with id `toastContainer`
var toast = new bootstrap.Toast(toastElement);
toast.show();
}

function showFailedToast(message) {
var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body d-flex align-items-start">
        <div style="width: 24px; text-align: center;">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="w-100 mx-2 text-start" id="toast-message">
            ${message}
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>`;
var toastElement = $(toastHTML);
$("#toastContainer").append(toastElement); // Make sure there's a container with id `toastContainer`
var toast = new bootstrap.Toast(toastElement);
toast.show();
}

function showFailedPrintToast(message, url) {
var toastId = 'toast-' + Date.now();
var toastHTML = `<div id="${toastId}" class="toast fade align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body d-flex align-items-start">
        <div style="width: 24px; text-align: center;">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="w-100 mx-2 text-start" id="toast-message">
            <div class="mb-1">${message}</div>
            <div>
                <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-light btn-sm bg-gradient open-pdf-btn">
                        Buka PDF
                    </button>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>`;

var toastElement = $(toastHTML);
$("#toastContainer").append(toastElement);

// Tambahkan event listener secara aman
toastElement.find('.open-pdf-btn').on('click', function () {
window.open(url, 'MsgWindow', 'width=1024,height=576');
});

var toast = new bootstrap.Toast(toastElement[0]);
toast.show();
}