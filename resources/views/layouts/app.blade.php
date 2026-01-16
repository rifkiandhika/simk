<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Meta -->
    <meta name="description" content="Medical Admin Dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/image/icon.png') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/tablericon/tabler.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"> --}}
    

    
    <link rel="stylesheet" href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    {{-- Select2 --}}
    <link href="{{ asset('assets/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/select2/select2-bootstrap.min.css') }}" rel="stylesheet" />

    


    <style>
    *{
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .spin .inner {
    border: 3px solid #3498db;
    border-top: 3px solid transparent;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 0.7s linear infinite; /* pas, tidak terlalu lama */
}

@keyframes spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


    </style>
    @stack('styles')
</head>



<body>
    @include('sweetalert::alert')
    @include('components.pin-modal')

    <!-- Loading starts -->
    {{-- <div id="loading-wrapper">
        <div class='spin-wrapper'>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
        </div>
    </div> --}}
    <!-- Loading ends -->

    <!-- Page wrapper starts -->
    <div class="page-wrapper">

        <!-- Include Header Component -->
        @include('components.header')

        <!-- Main container starts -->
        <div class="main-container">

            <!-- Include Sidebar Component -->
            @include('components.sidebar')

            <!-- App container starts -->
            <div class="app-container">

                <!-- App hero header starts -->
                <div class="app-hero-header d-flex align-items-center">
                    <!-- Breadcrumb starts -->
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
                            <a href="#">Home</a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                    <!-- Breadcrumb ends -->

                    <!-- Page actions -->
                    <div class="ms-auto d-lg-flex d-none flex-row">
                        <div class="d-flex flex-row gap-1 day-sorting">
                            <button class="btn btn-sm btn-primary">Today</button>
                            <button class="btn btn-sm">7d</button>
                            <button class="btn btn-sm">2w</button>
                            <button class="btn btn-sm">1m</button>
                            <button class="btn btn-sm">3m</button>
                            <button class="btn btn-sm">6m</button>
                            <button class="btn btn-sm">1y</button>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                                    <i class="ri ri-logout-box-line"></i>
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
                <!-- App Hero header ends -->

                <!-- App body starts -->
                <div class="app-body">
                    <!-- Display Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>
                <!-- App body ends -->

                <!-- Include Footer Component -->
                {{-- @include('components.footer') --}}

            </div>
            <!-- App container ends -->

        </div>
        <!-- Main container ends -->

    </div>
    <!-- Page wrapper ends -->

    @include('components.notification')

    <!-- JavaScript Files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    {{-- SweetAlert --}}
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
    {{-- Select2 --}}
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>
    
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js')}}"></script>
    {{-- <script src="{{ asset('assets/tablericon/tabler.min.js')}}"></script> --}}

    {{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Loader akan hilang setelah 5 detik
        setTimeout(function () {
            document.getElementById("loading-wrapper").style.display = "none";
        }, 1000); // 5000ms = 5 detik
    });
</script> --}}
       <!-- Ganti seluruh bagian script ini di layouts/app.blade.php -->

<script>
    // Set initial PIN status
    window.PIN_VERIFIED = {{ session('pin_verified') ? 'true' : 'false' }};
</script>

<script>
// Inactivity Detection & PIN Modal System - FIXED VERSION
(function() {
    let inactivityTimer;
    const INACTIVITY_TIMEOUT = 10 * 60 * 1000; // 30 detik untuk testing (ubah ke 10 * 60 * 1000 untuk 10 menit)
    
    console.log('🔐 PIN System initialized. Timeout:', INACTIVITY_TIMEOUT / 1000, 'seconds');
    
    // Fungsi untuk reset timer
    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        
        console.log('⏱️ Timer reset');
        
        // Set timer baru
        inactivityTimer = setTimeout(function() {
            console.log('⚠️ Timeout reached! Showing PIN modal...');
            showPinModalDueToInactivity();
        }, INACTIVITY_TIMEOUT);
    }
    
    // Fungsi untuk menampilkan modal PIN karena inactivity
    function showPinModalDueToInactivity() {
        console.log('🚫 Setting PIN_VERIFIED to false');
        window.PIN_VERIFIED = false; 
        
        // Disable semua protected actions
        disableAllProtectedActions();
        
        // Clear PIN session di backend
        fetch('/pin/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ PIN session cleared:', data);
            
            // Panggil function dari pin-modal untuk show dengan info message
            if (typeof window.showPinModalForInactivity === 'function') {
                console.log('📱 Calling showPinModalForInactivity()');
                window.showPinModalForInactivity();
            } else {
                console.log('⚠️ showPinModalForInactivity not found, using fallback');
                
                // Fallback - langsung show modal
                const pinModalElement = document.getElementById('pinVerificationModal');
                if (pinModalElement) {
                    let modalInstance = bootstrap.Modal.getInstance(pinModalElement);
                    
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(pinModalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                    
                    modalInstance.show();
                    console.log('✅ Modal shown via fallback');
                    
                    // Clear PIN inputs dan show info
                    setTimeout(() => {
                        if (typeof window.clearAllPin === 'function') {
                            window.clearAllPin();
                        }
                        
                        if (typeof window.showPinInfo === 'function') {
                            window.showPinInfo('Sesi Anda tidak aktif. Silakan masukkan PIN untuk melanjutkan.');
                        }
                    }, 300);
                } else {
                    console.error('❌ Modal element not found!');
                }
            }
        })
        .catch(error => {
            console.error('❌ Error clearing PIN session:', error);
            // Tetap tampilkan modal meskipun error
            if (typeof window.showPinModalForInactivity === 'function') {
                window.showPinModalForInactivity();
            }
        });
    }

    // Fungsi untuk disable semua protected actions
    function disableAllProtectedActions() {
        console.log('🔒 Disabling all protected actions');
        document.querySelectorAll('[data-require-pin]').forEach(el => {
            el.classList.add('disabled');
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.5';
        });
    }

    // Event listeners untuk mendeteksi aktivitas user
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    
    events.forEach(function(event) {
        document.addEventListener(event, function() {
            // Hanya reset timer jika modal TIDAK sedang ditampilkan
            const modalElement = document.getElementById('pinVerificationModal');
            if (modalElement && !modalElement.classList.contains('show')) {
                resetInactivityTimer();
            }
        }, true);
    });
    
    // Mulai timer saat halaman dimuat
    resetInactivityTimer();
    console.log('✅ Initial timer started');
    
    // PENTING: Reset timer ketika modal ditutup (PIN berhasil diverifikasi)
    document.addEventListener('DOMContentLoaded', function() {
        const pinModal = document.getElementById('pinVerificationModal');
        if (pinModal) {
            pinModal.addEventListener('hidden.bs.modal', function() {
                console.log('✅ Modal hidden - resetting timer');
                resetInactivityTimer();
            });
            
            console.log('✅ Modal event listeners attached');
        }
    });
    
    // PENTING: Handle AJAX requests yang ditolak karena PIN
    if (typeof $ !== 'undefined') {
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (xhr.status === 403) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.pin_required) {
                        console.log('⚠️ AJAX blocked - PIN required');
                        showPinModalDueToInactivity();
                    }
                } catch (e) {
                    // Bukan JSON response
                }
            }
        });
        
        console.log('✅ AJAX handlers attached');
    }
    
    // Expose functions untuk debugging
    window.resetPinTimer = resetInactivityTimer;
    window.showPinNow = showPinModalDueToInactivity;
    window.getTimeRemaining = function() {
        // Hitung sisa waktu (approximate)
        return 'Check console for timer status';
    };
    
    console.log('✅ PIN System ready!');
    console.log('💡 Debug commands:');
    console.log('   - window.resetPinTimer() - Reset timer');
    console.log('   - window.showPinNow() - Show modal now');
})();
</script>

<!-- Focus handler untuk modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pinModal = document.getElementById('pinVerificationModal');
    if (pinModal) {
        pinModal.addEventListener('shown.bs.modal', function() {
            console.log('🎯 Modal shown - focusing input');
            const firstPinBox = document.querySelector('.pin-box');
            if (firstPinBox) {
                setTimeout(() => firstPinBox.focus(), 100);
            }
        });
    }
});
</script>

<!-- DataTable initialization -->
<script>
$(document).ready(function() {
    $("#myTable").DataTable({
        ordering: false
    });
});
</script>

<!-- Delete confirmation -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-confirm').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<!-- AJAX PIN check - Block requests when PIN not verified -->
<script>
if (typeof $ !== 'undefined') {
    $(document).ajaxSend(function (e, xhr, settings) {
        // Jangan block request ke /pin/verify dan /pin/logout
        if (settings.url.includes('/pin/verify') || settings.url.includes('/pin/logout')) {
            return;
        }
        
        if (window.PIN_VERIFIED === false) {
            console.log('🚫 AJAX blocked - PIN not verified');
            xhr.abort();

            // Tampilkan modal PIN
            if (typeof window.showPinModalForInactivity === 'function') {
                window.showPinModalForInactivity();
            } else {
                const modalElement = document.getElementById('pinVerificationModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }
        }
    });
    
    console.log('✅ AJAX PIN guard active');
}
</script>


    @stack('scripts')
</body>
</html>