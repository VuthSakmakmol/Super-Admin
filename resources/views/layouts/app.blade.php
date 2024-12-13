<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!--Vue CDN Link-->
    <script src="https://unpkg.com/vue@next"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome CDN Link --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Add hover effect */
        .navbar-nav .nav-link {
            transition: background-color 0.3s, color 0.3s;
        }
        .navbar-nav .nav-link:hover {
            background-color: yellow;
            color: black;
        }
        /* Add active state styles */
        .navbar-nav .nav-link.active {
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-rocket"></i> My Application
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Home Links -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>

                    <!-- Super Admin Link -->
                    @if(auth()->check() && auth()->user()->hasRole('super admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super-admin.index') ? 'active' : '' }}" href="{{ route('super-admin.index') }}">
                            <i class="fas fa-user-shield"></i> Super Admin
                        </a>
                    </li>
                    @endif

                    <!-- Admin Link -->
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">
                            <i class="fas fa-user-cog"></i> Admin
                        </a>
                    </li>
                    @endif

                    <!-- User Link -->
                    @if(auth()->check() && auth()->user()->hasRole('user'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.index') ? 'active' : '' }}" href="{{ route('user.index') }}">
                            <i class="fas fa-user"></i> User
                        </a>
                    </li>
                    @endif


                    <!-- Logout Link -->
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-white text-decoration-none" style="cursor: pointer;">
                                Logout
                            </button>
                        </form>                        
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        @yield('content')
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        function confirmDelete(event) {
            event.preventDefault(); // Prevent the form from submitting immediately
            const confirmation = confirm("Are you sure you want to delete this user?");
            if (confirmation) {
                event.target.submit(); // Submit the form if the user confirms
            }
        }
    </script>
    
</body>
</html>
