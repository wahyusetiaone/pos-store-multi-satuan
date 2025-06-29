<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('index') }}">
                    <iconify-icon icon="solar:home-2-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin' || auth()->user()->role === 'cashier')
            <li class="sidebar-menu-group-title">POS</li>
            <li>
                <a href="{{ route('pos') }}">
                    <iconify-icon icon="solar:home-2-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard POS</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin' || auth()->user()->role === 'purchasing')
            <li>
                <a href="{{ route('products.index') }}">
                    <iconify-icon icon="mdi:package-variant" class="menu-icon"></iconify-icon>
                    <span>Produk</span>
                </a>
            </li>
            <li>
                <a href="{{ route('categories.index') }}">
                    <iconify-icon icon="mdi:shape-outline" class="menu-icon"></iconify-icon>
                    <span>Kategori</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin' || auth()->user()->role === 'cashier')
            <li>
                <a href="{{ route('customers.index') }}">
                    <iconify-icon icon="mdi:account-group-outline" class="menu-icon"></iconify-icon>
                    <span>Pelanggan</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin')
            <li>
                <a href="{{ route('cod.index') }}">
                    <iconify-icon icon="mdi:truck-delivery" class="menu-icon"></iconify-icon>
                    <span>Cash On Delivery</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin' || auth()->user()->role === 'cashier')
            <li>
                <a href="{{ route('sales.index') }}">
                    <iconify-icon icon="mdi:cart-outline" class="menu-icon"></iconify-icon>
                    <span>Penjualan</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'purchasing')
                <li>
                    <a href="{{ route('purchases.index') }}">
                        <iconify-icon icon="mdi:truck-outline" class="menu-icon"></iconify-icon>
                        <span>Pembelian</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'store_admin' || auth()->user()->role === 'purchasing')
            <li>
                <a href="{{ route('shippings.index') }}">
                    <iconify-icon icon="mdi:truck-delivery-outline" class="menu-icon"></iconify-icon>
                    <span>Pengiriman</span>
                </a>
            </li>
            <li>
                <a href="{{ route('finances.index') }}">
                    <iconify-icon icon="mdi:cash-multiple" class="menu-icon"></iconify-icon>
                    <span>Keuangan</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{ route('gallery.index') }}">
                    <iconify-icon icon="mdi:image-multiple" class="menu-icon"></iconify-icon>
                    <span>Galeri</span>
                </a>
            </li>

            @if(auth()->user()->role === 'owner' || auth()->user()->role === 'purchasing')
            <li>
                <a href="{{ route('stores.index') }}">
                    <iconify-icon icon="mdi:store" class="menu-icon"></iconify-icon>
                    <span>Manajemen Toko</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->role === 'owner')
            <li>
                <a href="{{ route('users.index') }}">
                    <iconify-icon icon="mdi:account-key-outline" class="menu-icon"></iconify-icon>
                    <span>Pengguna</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</aside>
