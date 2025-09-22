<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <!-- Meta -->
    <meta name="description" content="Medical Admin Dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/image/icon.png') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    
    @stack('styles')
</head>

<body>
    <!-- Loading starts -->
    <div id="loading-wrapper">
        <div class='spin-wrapper'>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
            <div class='spin'><div class='inner'></div></div>
        </div>
    </div>
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
                        @yield('page-actions')
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
                @include('components.footer')

            </div>
            <!-- App container ends -->

        </div>
        <!-- Main container ends -->

    </div>
    <!-- Page wrapper ends -->

    <!-- JavaScript Files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>