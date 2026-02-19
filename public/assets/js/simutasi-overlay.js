document.addEventListener("DOMContentLoaded", function () {
    let loadingOverlay = document.getElementById("loadingOverlay");

    // 🔹 Sembunyikan overlay loading saat halaman selesai dimuat
    if (loadingOverlay) {
        window.addEventListener("load", function () {
            loadingOverlay.style.opacity = "0";
            setTimeout(() => { loadingOverlay.style.display = "none"; }, 500);
        });
    }

    // 🔹 Menampilkan overlay saat form dikirimkan
    let form = document.querySelector("form");
    let submitBtn = document.querySelector("button[type='submit']");

    if (form) {
        form.addEventListener("submit", function () {
            if (loadingOverlay) {
                loadingOverlay.style.display = "flex";
            }
            if (submitBtn) {
                submitBtn.disabled = true; // Nonaktifkan tombol submit agar tidak bisa diklik berulang kali
            }
        });
    }
});

// 🔹 Fungsi untuk menampilkan overlay loading di tabel
function showTableLoading() {
    let tableContainer = document.querySelector(".table-responsive");
    let overlay = document.getElementById("tableLoadingOverlay");

    if (!overlay && tableContainer) {
        overlay = document.createElement("div");
        overlay.id = "tableLoadingOverlay";
        overlay.classList.add("table-loading-overlay");

        let loader = document.createElement("div");
        loader.classList.add("loader");
        let loaderSpan = document.createElement("span");
        loader.appendChild(loaderSpan);
        overlay.appendChild(loader);
        
        tableContainer.appendChild(overlay);
    }

    if (overlay) {
        overlay.style.display = "flex";
    }
}

// 🔹 Fungsi untuk menyembunyikan overlay loading di tabel
function hideTableLoading() {
    let overlay = document.getElementById("tableLoadingOverlay");
    if (overlay) {
        overlay.style.display = "none";
    }
}
