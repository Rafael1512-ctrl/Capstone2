@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6 d-flex justify-content-between align-items-center">
          <div>
            <h1 class="fs-3 mb-1">Review Draf Pengadaan #{{ $draft['id'] }}</h1>
            <p class="text-muted">Tinjau item pengadaan untuk Tahun Anggaran {{ $draft['tahun'] }}. Diajukan oleh: {{ $draft['pengaju'] }}.</p>
          </div>
          <a class="btn btn-outline-secondary" href="/ketua-prodi/review">Kembali</a>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body p-4 d-flex justify-content-between align-items-center bg-light rounded-2">
            <div class="w-100">
              <h5 class="mb-1">Keputusan Draf Pengadaan</h5>
              @if ($draft['status'] !== 'finalized' && $draft['status'] !== 'rejected')
                <form action="/ketua-prodi/review/{{ $draft['id'] }}/process" method="POST" onsubmit="return confirm('Apakah Anda yakin dengan keputusan ini?');">
                  @csrf
                  <div class="mb-3">
                    <label class="form-label fw-bold">Catatan / Alasan Penolakan (Opsional jika disetujui)</label>
                    <textarea class="form-control" name="alasan_penolakan" rows="2" placeholder="Masukkan catatan atau alasan jika menolak..."></textarea>
                  </div>
                  <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit" name="action" value="approve">
                      <i class="ti ti-check me-1"></i>Setuju
                    </button>
                    <button class="btn btn-danger" type="submit" name="action" value="reject">
                      <i class="ti ti-x me-1"></i>Tolak
                    </button>
                  </div>
                </form>
              @else
                <span class="badge fs-6 {{ $draft['status'] === 'finalized' ? 'bg-success' : 'bg-danger' }}">
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
                  <th class="px-4 py-3">Barang & Rasionalisasi</th>
                  <th>Tipe</th>
                  <th>Harga</th>
                  <th>Qty</th>
                  <th>Total</th>
                  <th>Status Item</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($items) && count($items) > 0)
                  @foreach ($items as $item)
                    <tr>
                      <td class="px-4 py-3">
                        <div class="fw-semibold">{{ $item['nama_barang'] }}</div>
                        <div class="small text-muted mb-1">{{ $item['rasionalisasi'] }}</div>
                        @if ($item['link_pembelian'])
                          <a class="small text-decoration-underline" href="{{ $item['link_pembelian'] }}" target="_blank">Link Pembelian</a>
                        @endif
                      </td>
                      <td class="text-capitalize">{{ $item['tipe_barang'] }}</td>
                      <td>${{ number_format($item['harga_satuan'], 2) }}</td>
                      <td>{{ $item['jumlah'] }}</td>
                      <td class="fw-semibold">${{ number_format($item['harga_satuan'] * $item['jumlah'], 2) }}</td>
                      <td>
                        @php
                          $badgeClass = $item['status_item'] === 'approved' ? 'bg-success' : ($item['status_item'] === 'rejected' ? 'bg-danger' : 'bg-warning');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                          {{ strtoupper($item['status_item']) }}
                        </span>
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
  </div>
@endsection
