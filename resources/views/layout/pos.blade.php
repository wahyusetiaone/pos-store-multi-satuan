<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f4f6f9;
        }
        .app-container {
            padding-bottom: 4.5rem; /* adjust for fixed navbar height at bottom */
            padding-top: 1rem;
        }
        .navbar {
            padding: 0.5rem 1rem;
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0,0,0,.1);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50;
        }
        .content-wrapper {
            padding: 1rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Main Content -->
    <div class="app-container">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <span class="navbar-brand d-flex align-items-center gap-2">
                <i class="ri-store-2-fill fs-4 text-primary"></i>
                @if(Auth::user()->hasGlobalAccess())
                    <form id="storeSwitchForm" method="POST" action="{{ route('store.switch') }}" class="d-flex align-items-center gap-2 mb-0">
                        @csrf
                        <select name="store_id" id="storeSwitcher" class="form-select form-select-sm" style="min-width: 180px;" onchange="document.getElementById('storeSwitchForm').submit()">
                            <option value="">Pilih Toko...</option>
                            @foreach(App\Models\Store::where('is_active', true)->get() as $store)
                                <option value="{{ $store->id }}" {{ Auth::user()->current_store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm">Switch</button>
                    </form>
                @else
                    {{ Auth::user()->currentStore->name ?? 'Toko' }}
                @endif
            </span>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    <i class="fas fa-user text-primary"></i> {{ Auth::user()->name ?? 'Guest' }}
                </span>
                <span class="navbar-text">
                    <i class="fas fa-clock text-primary"></i> <span id="currentTime"></span>
                </span>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
    @stack('scripts')
</body>
</html>
