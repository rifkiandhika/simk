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


    <script>
        $(document).ready(function() {
            $("#myTable").DataTable({
                ordering: false
            });
            

        })
    </script>

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

    @stack('scripts')
</body>
</html>