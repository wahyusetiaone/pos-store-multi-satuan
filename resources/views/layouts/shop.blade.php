<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head />

<body>
    <main class="dashboard">
        <!-- Navbar -->
        <nav class="navbar navbar-top fixed-top" style="background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div class="container-fluid">
                <div class="w-100 d-flex justify-content-center">
                    <form action="{{ route('shop.index') }}" method="GET" class="d-flex gap-2"
                        style="max-width: 800px; width: 100%;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <iconify-icon icon="heroicons:magnifying-glass" class="fs-5"></iconify-icon>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Cari produk..."
                                value="{{ request('search') }}">
                        </div>
                        @if(isset($categories))
                            <select name="category" class="form-select" style="width: auto;" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </form>
                </div>
            </div>
        </nav>

        <!-- Add margin top to compensate for fixed header -->
        <div style="margin-top: 80px;">
            <!-- Banner Section -->
            <div class="container py-4">
                <div id="mainBanner" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner rounded-4 overflow-hidden" style="max-height: 400px;">
                        <div class="carousel-item active">
                            <img src="https://placehold.co/1200x400?text=Special+Offer" class="d-block w-100" alt="Special Offer">
                        </div>
                        <div class="carousel-item">
                            <img src="https://placehold.co/1200x400?text=New+Arrivals" class="d-block w-100" alt="New Arrivals">
                        </div>
                        <div class="carousel-item">
                            <img src="https://placehold.co/1200x400?text=Best+Deals" class="d-block w-100" alt="Best Deals">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainBanner" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>

                <!-- Category Quick Access -->
                @if(isset($categories))
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Kategori Populer</h5>
                            <div class="d-flex gap-3 flex-wrap">
                                @foreach($categories as $category)
                                    <a href="{{ route('shop.index', ['category' => $category->id]) }}"
                                       class="btn {{ request('category') == $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-5 my-5" style="background-color: #2c3e50; color: white;">
            <div class="container">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <h5>Tentang Kami</h5>
                        <p class="text-white-50">Toko online terpercaya dengan berbagai produk berkualitas.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Link Cepat</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-white-50">Cara Belanja</a></li>
                            <li><a href="#" class="text-decoration-none text-white-50">Kebijakan Privasi</a></li>
                            <li><a href="#" class="text-decoration-none text-white-50">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Hubungi Kami</h5>
                        <ul class="list-unstyled text-white-50">
                            <li>Email: info@example.com</li>
                            <li>Phone: (123) 456-7890</li>
                            <li>Alamat: Jl. Example No. 123</li>
                        </ul>
                    </div>
                </div>
                <hr class="border-white-50 m-4">
                <div class="text-center text-white-50">
                    &copy; {{ date('Y') }} Your Company Name. All rights reserved.
                </div>
            </div>
        </footer>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    @stack('scripts')
</body>
</html>
