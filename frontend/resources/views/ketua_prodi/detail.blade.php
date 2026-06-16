@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="mb-6 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fs-3 mb-1">Review Draf Pengadaan (Tahun {{ $draft['tahun'] }})</h1>
                        <p class="text-muted">Tinjau item pengadaan untuk Tahun Anggaran {{ $draft['tahun'] }}. Diajukan
                            oleh: {{ $draft['pengaju'] }}.</p>
                    </div>
                    @php
                        $backUrl = ($draft['status'] === 'finalized' || $draft['status'] === 'rejected') ? '/ketua-prodi/history' : '/ketua-prodi/review';
                    @endphp
                    <a class="btn btn-outline-secondary" href="{{ $backUrl }}">Kembali</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4 bg-light rounded-2">
                        <div class="w-100">
                            <h5 class="mb-3">Keputusan Draf Pengadaan</h5>
                            @if ($draft['status'] !== 'finalized' && $draft['status'] !== 'rejected')
                                <form id="itemsReviewForm" action="/ketua-prodi/review/{{ $draft['id'] }}/process"
                                    method="POST">
                                    @csrf
                                    <div class="alert alert-info" role="alert">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <strong>Petunjuk:</strong> Pilih status untuk setiap item barang di bawah. Anda
                                        dapat menerima atau menolak item secara individual. Tambahkan catatan untuk item
                                        yang ditolak.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Catatan Umum (Opsional)</label>
                                        <textarea class="form-control" name="alasan_penolakan" rows="2"
                                            placeholder="Masukkan catatan umum untuk draft ini..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success" type="button" onclick="showConfirmDialog('Apakah Anda yakin dengan keputusan ini?', document.getElementById('itemsReviewForm'), validateReviewForm)">
                                            <i class="ti ti-check me-1"></i>Simpan Semua Keputusan
                                        </button>
                                        <a class="btn btn-outline-secondary" href="/ketua-prodi/review">Batal</a>
                                    </div>
                                </form>
                            @else
                                <span
                                    class="badge fs-6 {{ $draft['status'] === 'finalized' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $draft['status'] === 'finalized' ? 'APPROVED' : strtoupper($draft['status']) }}
                                </span>
                                @if ($draft['alasan_penolakan'])
                                    <div class="mt-2 p-2 bg-white rounded border">
                                        <strong>Catatan Keputusan:</strong>
                                        <p class="mb-0 text-muted">{{ $draft['alasan_penolakan'] }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent px-4 py-3 border-bottom">
                        <h5 class="mb-0">Daftar Barang Pengadaan</h5>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3" style="width: 25%;">Barang & Rasionalisasi</th>
                                    <th style="width: 10%;">Kategori</th>
                                    <th style="width: 10%;">Jenis</th>
                                    <th style="width: 8%;">Tipe</th>
                                    <th style="width: 11%;">Harga</th>
                                    <th style="width: 6%;">Qty</th>
                                    <th style="width: 11%;">Total</th>
                                    <th style="width: 19%;">Keputusan & Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($items) && count($items) > 0)
                                    @foreach ($items as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="fw-semibold text-dark">{{ $item['nama_barang'] }}</div>
                                                <div class="small text-muted mb-1">{{ $item['rasionalisasi'] }}</div>
                                                @if (isset($item['inventaris_digantikan_id']) && $item['inventaris_digantikan_id'])
                                                    <div class="mt-1 mb-2">
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-2 py-1 fs-8 text-wrap text-start d-inline-flex align-items-center">
                                                            <i class="ti ti-replace me-1.5 fs-6"></i>Menggantikan: {{ $item['label_digantikan'] }} - {{ $item['nama_digantikan'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                                @if ($item['link_pembelian'])
                                                    <a class="small text-decoration-underline"
                                                        href="{{ $item['link_pembelian'] }}" target="_blank">Link
                                                        Pembelian</a>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-light text-dark border px-2 py-1 fs-8">{{ $item['kategori'] ?? '-' }}</span></td>
                                            <td>{{ $item['jenis'] ?? '-' }}</td>
                                            <td class="text-capitalize">{{ $item['tipe_barang'] }}</td>
                                            <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                            <td>{{ $item['jumlah'] }}</td>
                                            <td class="fw-semibold">Rp
                                                {{ number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') }}
                                            </td>
                                            <td class="py-3">
                                                @if ($draft['status'] !== 'finalized' && $draft['status'] !== 'rejected')
                                                    <div class="d-flex gap-2 mb-2">
                                                        <input type="radio" class="btn-check"
                                                            name="decision[{{ $item['id'] }}]"
                                                            id="approve_{{ $item['id'] }}" value="approved"
                                                            form="itemsReviewForm"
                                                            @if ($item['status_item'] === 'approved') checked @endif>
                                                        <label class="btn btn-sm btn-outline-success flex-fill rounded-3 d-flex justify-content-center align-items-center gap-1"
                                                            for="approve_{{ $item['id'] }}" style="font-size: 12px; min-width: 82px;">
                                                            <i class="ti ti-check"></i> Acc
                                                        </label>

                                                        <input type="radio" class="btn-check"
                                                            name="decision[{{ $item['id'] }}]"
                                                            id="reject_{{ $item['id'] }}" value="rejected"
                                                            form="itemsReviewForm"
                                                            @if ($item['status_item'] === 'rejected') checked @endif>
                                                        <label class="btn btn-sm btn-outline-danger flex-fill rounded-3 d-flex justify-content-center align-items-center gap-1"
                                                            for="reject_{{ $item['id'] }}" style="font-size: 12px; min-width: 82px;">
                                                            <i class="ti ti-x"></i> Tolak
                                                        </label>

                                                        <input type="radio" class="btn-check"
                                                            name="decision[{{ $item['id'] }}]"
                                                            id="pending_{{ $item['id'] }}" value="pending"
                                                            form="itemsReviewForm"
                                                            @if ($item['status_item'] === 'pending') checked @endif>
                                                        <label class="btn btn-sm btn-outline-warning flex-fill rounded-3 d-flex justify-content-center align-items-center gap-1"
                                                            for="pending_{{ $item['id'] }}" style="font-size: 12px; min-width: 82px;">
                                                            <i class="ti ti-help"></i> Pending
                                                        </label>
                                                    </div>
                                                    <textarea class="form-control form-control-sm" name="catatan_item[{{ $item['id'] }}]" form="itemsReviewForm"
                                                        rows="1" placeholder="Catatan untuk item ini..." style="font-size: 12px;"></textarea>
                                                @else
                                                    <span
                                                        class="badge {{ $item['status_item'] === 'approved' ? 'bg-success' : ($item['status_item'] === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                        {{ strtoupper($item['status_item']) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center py-4" colspan="6">Tidak ada barang pengadaan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Validation function for review form
            function validateReviewForm() {
                const items = document.querySelectorAll('input[type="radio"][name*="decision"]');
                const itemIds = new Set();

                items.forEach(item => {
                    const match = item.name.match(/decision\[(\d+)\]/);
                    if (match) {
                        itemIds.add(match[1]);
                    }
                });

                let allSelected = true;
                let missingReason = false;

                itemIds.forEach(itemId => {
                    const selected = document.querySelector(`input[name="decision[${itemId}]"]:checked`);
                    const catatanInput = document.querySelector(`textarea[name="catatan_item[${itemId}]"]`);
                    
                    if (!selected) {
                        allSelected = false;
                    } else if (selected.value === 'rejected') {
                        if (!catatanInput || !catatanInput.value.trim()) {
                            missingReason = true;
                            if (catatanInput) {
                                catatanInput.classList.add('is-invalid');
                                catatanInput.placeholder = 'Catatan wajib diisi untuk item yang ditolak!';
                            }
                        } else {
                            if (catatanInput) catatanInput.classList.remove('is-invalid');
                        }
                    } else {
                        if (catatanInput) catatanInput.classList.remove('is-invalid');
                    }
                });

                if (!allSelected) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap pilih status untuk semua item barang!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                if (missingReason) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap isi catatan/alasan penolakan untuk semua item barang yang ditolak!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                return true;
            }

            // Real-time invalid outline removal
            document.addEventListener('input', function(e) {
                if (e.target.tagName === 'TEXTAREA' && e.target.name.startsWith('catatan_item[')) {
                    if (e.target.value.trim()) {
                        e.target.classList.remove('is-invalid');
                    }
                }
            });
        </script>

    </div>
@endsection
