@extends('layout.layout')
@php
    $title = 'Detail Pelanggan';
    $subTitle = 'Informasi Pelanggan';
    $script = '
        <script src="' . asset('assets/js/pages/customer/show.js') . '"></script>
    ';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Pelanggan</h5>
                    <span class="text-muted">Informasi lengkap pelanggan</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="f7:person"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $customer->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mage:email"></iconify-icon>
                                </span>
                                <input type="email" class="form-control" value="{{ $customer->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="solar:phone-calling-linear"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $customer->phone }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:map-marker"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $customer->address }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="2" readonly>{{ $customer->notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if($customer->debt_total > 0)
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Informasi Hutang</h5>
                        <span class="text-muted">Detail hutang pelanggan</span>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-money-bill-wave me-2"></i> Bayar Hutang
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4 class="alert-heading mb-2">Total Hutang</h4>
                        <h2 class="mb-0">Rp {{ number_format($customer->debt_total, 0, ',', '.') }},-</h2>
                    </div>

                    <h5 class="card-title mb-0">Riwayat Piutang</h5>
                    <span class="text-muted">Daftar transaksi dengan pembayaran tertunda</span>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total Transaksi</th>
                                <th>Sudah Dibayar</th>
                                <th>Sisa Hutang</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customer->accountReceivables->sortBy('created_at') as $ar)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $ar->sale_id) }}" class="text-primary">#{{ $ar->sale_id }}</a>
                                    </td>
                                    <td>{{ $ar->created_at->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($ar->sale->grand_total, 0, ',', '.') }},-</td>
                                    <td>Rp {{ number_format(($ar->sale->grand_total - $ar->pending_payment), 0, ',', '.') }},-</td>
                                    <td>Rp {{ number_format($ar->pending_payment, 0, ',', '.') }},-</td>
                                    <td>
                                        @if($ar->status === 'pending')
                                            <button type="button" class="btn btn-sm badge bg-warning show-payment-history"
                                                data-ar-id="{{ $ar->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paymentHistoryModal">
                                                Belum Lunas
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm badge bg-success show-payment-history"
                                                data-ar-id="{{ $ar->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paymentHistoryModal">
                                                Lunas
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Modal -->
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">Pembayaran Hutang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="paymentForm">
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <div class="mb-3">
                                    <label class="form-label">Total Hutang</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" value="{{ number_format($customer->debt_total, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pembayaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="amount" required min="0" max="{{ $customer->debt_total }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="cash">Tunai</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-primary" id="savePayment">Simpan Pembayaran</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Modal -->
            <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentHistoryModalLabel">Riwayat Pelunasan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="paymentHistoryTable">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Metode</th>
                                            <th>Catatan</th>
                                            <th>Diproses Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
