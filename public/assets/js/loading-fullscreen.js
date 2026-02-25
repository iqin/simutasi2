/**
 * Fullscreen Loading Handler untuk SIMUTASI
 * File terpisah untuk loading overlay fullscreen
 */

const FullscreenLoading = {
    overlay: document.getElementById('fullscreenLoading'),
    title: document.getElementById('loadingTitle'),
    message: document.getElementById('loadingMessage'),
    subMessage: document.getElementById('loadingSubMessage'),
    spinner: document.querySelector('.loading-spinner'),
    timeoutId: null,
    
    /**
     * Tampilkan loading overlay
     * @param {Object} options - Konfigurasi tampilan
     * @param {string} options.title - Judul utama
     * @param {string} options.message - Pesan utama
     * @param {string} options.subMessage - Pesan tambahan
     * @param {string} options.type - Tipe warna (default/success/warning/danger)
     * @param {number} options.timeout - Auto hide setelah sekian ms (default 30000)
     */
    show: function(options = {}) {
        const {
            title = 'SIMUTASI',
            message = 'Sedang Memproses Data',
            subMessage = 'Harap tunggu, sistem sedang bekerja...',
            type = 'default',
            timeout = 30000
        } = options;
        
        // Update teks
        if (this.title) this.title.textContent = title;
        if (this.message) this.message.textContent = message;
        if (this.subMessage) this.subMessage.textContent = subMessage;
        
        // Update warna spinner jika diperlukan
        if (this.spinner) {
            this.spinner.className = 'loading-spinner';
            if (type !== 'default') {
                this.spinner.classList.add(type);
            }
        }
        
        // Tampilkan overlay
        if (this.overlay) {
            this.overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Nonaktifkan scroll
            document.body.style.pointerEvents = 'none'; // Nonaktifkan semua interaksi
        }
        
        // Auto hide fallback (jika ada error)
        if (this.timeoutId) clearTimeout(this.timeoutId);
        this.timeoutId = setTimeout(() => {
            console.warn('Loading timeout - forced hide');
            this.hide();
        }, timeout);
    },
    
    /**
     * Sembunyikan loading overlay
     */
    hide: function() {
        if (this.overlay) {
            this.overlay.style.display = 'none';
            document.body.style.overflow = ''; // Aktifkan scroll kembali
            document.body.style.pointerEvents = ''; // Aktifkan interaksi kembali
        }
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }
    },
    
    /**
     * Attach ke form submit
     * @param {string} formSelector - Selector form (misal '#formKirim')
     * @param {Object} options - Opsi tampilan
     */
    attachToForm: function(formSelector, options = {}) {
        const form = document.querySelector(formSelector);
        if (!form) {
            console.warn(`Form dengan selector "${formSelector}" tidak ditemukan`);
            return;
        }
        
        form.addEventListener('submit', (e) => {
            // Cegah double submit
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }
            
            form.dataset.submitting = 'true';
            
            // Nonaktifkan semua tombol submit di form
            form.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.disabled = true;
            });
            
            // Tampilkan loading
            this.show(options);
            
            // Form akan tetap disubmit secara normal
        });
    },
    
    /**
     * Untuk proses AJAX - tampilkan loading
     */
    showForAjax: function(options = {}) {
        this.show(options);
    },
    
    /**
     * Untuk proses AJAX - sembunyikan loading
     */
    hideForAjax: function() {
        this.hide();
    },
    
    /**
     * Inisialisasi
     */
    init: function() {
        // Sembunyikan loading saat halaman selesai load
        window.addEventListener('load', () => {
            this.hide();
            
            // Reset semua form
            document.querySelectorAll('form').forEach(form => {
                form.dataset.submitting = 'false';
                form.querySelectorAll('button[type="submit"]').forEach(btn => {
                    btn.disabled = false;
                });
            });
        });
        
        // Tangani jika user klik back/forward
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                this.hide();
                document.querySelectorAll('form').forEach(form => {
                    form.dataset.submitting = 'false';
                    form.querySelectorAll('button[type="submit"]').forEach(btn => {
                        btn.disabled = false;
                    });
                });
            }
        });
    }
};

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    FullscreenLoading.init();
});

// Export ke global
window.FullscreenLoading = FullscreenLoading;