@extends('layout.layout')
@php
    $title = 'Daftar Variant Produk';
    $subTitle = 'Tabel Variant Produk';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div></div>
                    <a href="{{ route('product-variants.create') }}" class="btn btn-success">Tambah Variant</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Nama Variant</th>
                                <th>Satuan</th>
                                <th>Harga Jual</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($variants as $variant)
                            <tr>
                                <td>{{ ($variants->currentPage() - 1) * $variants->perPage() + $loop->iteration }}</td>
                                <td>{{ $variant->product->name ?? '-' }}</td>
                                <td>{{ $variant->name }}</td>
                                <td>{{ $variant->productUnit->unit->name ?? '-' }}</td>
                                <td>Rp {{ number_format($variant->price, 0, ',', '.') }}</td>
                                <td>{{ $variant->qty }}</td>
                                <td>
                                    <span class="badge bg-{{ $variant->status ? 'success' : 'secondary' }}">
                                        {{ $variant->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('product-variants.show', $variant->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="{{ route('product-variants.edit', $variant->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Edit">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('product-variants.destroy', $variant->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus variant ini?')" title="Hapus">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $variants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
