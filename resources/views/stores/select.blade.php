<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Toko</title>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 800px; /* mengubah max-width agar lebih kecil */
            padding: 2rem;
            margin: 0 auto;
        }

        .store-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, 150px); /* mengubah dari auto-fill ke auto-fit dengan width tetap */
            gap: 2rem;
            justify-content: center; /* menambahkan justify-content center */
        }

        .store-card {
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .store-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .store-icon {
            font-size: 48px;
            color: #1a73e8;
            margin-bottom: 1rem;
        }

        .store-name {
            font-size: 0.9rem;
            color: #495057;
            margin: 0;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
        }

        .store-card form {
            display: contents;
        }

        .store-card button {
            background: none;
            border: none;
            padding: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .page-title {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: 500;
        }

        @if(session('error'))
        .alert {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        @endif
    </style>
</head>
<body>
<div class="container">
    <h1 class="page-title">Pilih Toko</h1>
    <p style="text-align: center; color: #6b7280; margin-bottom: 2rem; font-size: 1rem;">
        Silakan pilih toko yang ingin Anda kelola.
    </p>
    @if(session('error'))
        <div class="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="store-grid">
        @foreach($stores as $store)
            <div class="store-card">
                <form action="{{ route('store.switch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $store->id }}">
                    <button type="submit">
                        <span class="iconify store-icon" data-icon="mdi:store"></span>
                        <p class="store-name">{{ $store->name }}</p>
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    @if($stores->isEmpty())
        <p style="text-align: center; color: #6b7280; margin-top: 2rem;">
            Tidak ada toko yang tersedia untuk Anda.
        </p>
    @endif
</div>
</body>
</html>
