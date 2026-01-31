// INVENTORY SYSTEM JAVASCRIPT - ENHANCED VERSION

// ============ ACTIVE MENU HIGHLIGHTING ============
// Fungsi ini akan berjalan setelah halaman selesai load
window.addEventListener('DOMContentLoaded', function() {
    // Ambil parameter page dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'home'; // default 'home' jika tidak ada parameter
    
    console.log('Current Page:', currentPage); // Debug: lihat halaman saat ini
    
    // Hapus class 'active' dari semua menu
    const allMenuItems = document.querySelectorAll('.list-item');
    allMenuItems.forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Tambahkan class 'active' ke menu yang sesuai
    const allLinks = document.querySelectorAll('.sidebar .list-item a');
    let activeMenuItem = null;
    
    allLinks.forEach(function(link) {
        // Ambil parameter page dari href link
        const href = link.getAttribute('href');
        if (href) {
            const linkParams = new URLSearchParams(href.split('?')[1]);
            const linkPage = linkParams.get('page');
            
            // Jika page pada link sama dengan current page
            if (linkPage === currentPage) {
                // Tambahkan class active ke parent (.list-item)
                link.parentElement.classList.add('active');
                activeMenuItem = link.parentElement;
                console.log('Active menu:', linkPage); // Debug
            }
        }
    });
    
    // AUTO-SCROLL SIDEBAR KE MENU YANG AKTIF
    if (activeMenuItem) {
        setTimeout(function() {
            const sidebar = document.getElementById('sidebar');
            const menuPosition = activeMenuItem.offsetTop;
            const sidebarHeight = sidebar.clientHeight;
            const menuHeight = activeMenuItem.clientHeight;
            
            // Scroll ke posisi menu - setengah tinggi sidebar untuk center
            const scrollTo = menuPosition - (sidebarHeight / 2) + (menuHeight / 2);
            
            sidebar.scrollTo({
                top: scrollTo,
                behavior: 'smooth'
            });
            
            console.log('Scrolled to active menu'); // Debug
        }, 100); // Delay kecil untuk memastikan DOM sudah siap
    }
});

// Toggle Menu Mobile
function toggleMenu() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// Close menu when click outside (mobile)
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (window.innerWidth <= 768) {
        if (menuToggle && !sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Auto close mobile menu after click link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.remove('active');
            }
        }
    });
});

// ============ SEARCH TABLE FUNCTION ============
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length - 1; j++) { // -1 to skip action column
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? "" : "none";
    }
}

// ============ FORM VALIDATION ============
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            input.classList.remove('success');
            isValid = false;
        } else {
            input.classList.remove('error');
            input.classList.add('success');
        }
    });

    return isValid;
}

// Real-time validation on blur and input
document.addEventListener('DOMContentLoaded', function() {
    const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('error');
                this.classList.remove('success');
            } else {
                this.classList.remove('error');
                this.classList.add('success');
            }
        });

        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('error');
                this.classList.add('success');
            }
        });
    });
});

// ============ SWEETALERT FUNCTIONS ============
function confirmDelete(url, message = 'Data akan dihapus permanen!') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = url;
        }
    }
    return false;
}

function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}

function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message
        });
    } else {
        alert(message);
    }
}

// ============ PRINT & EXPORT FUNCTIONS ============
function printPage() {
    window.print();
}

function exportToPDF() {
    const originalTitle = document.title;
    document.title = 'Laporan_' + new Date().toISOString().slice(0,10);
    window.print();
    setTimeout(() => {
        document.title = originalTitle;
    }, 1000);
}

// ============ UTILITY FUNCTIONS ============
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function generateCode(prefix, lastCode) {
    if (!lastCode) {
        return prefix + '-001';
    }
    const num = parseInt(lastCode.split('-')[1]) + 1;
    return prefix + '-' + num.toString().padStart(3, '0');
}

// Filter by date range
function filterByDate() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const table = document.querySelector('.data-table tbody');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const dateCell = rows[i].getElementsByTagName('td')[2]; // Assuming date is in 3rd column
        if (dateCell) {
            const rowDate = dateCell.textContent.split('/').reverse().join('-');
            
            if ((startDate && rowDate < startDate) || (endDate && rowDate > endDate)) {
                rows[i].style.display = 'none';
            } else {
                rows[i].style.display = '';
            }
        }
    }
}

function resetFilter() {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    const rows = document.querySelectorAll('.data-table tbody tr');
    rows.forEach(row => row.style.display = '');
}

console.log('✅ Inventory System Enhanced loaded successfully!');