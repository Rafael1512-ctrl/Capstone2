@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Kepala Lab - Riwayat & Kelola Draf</h1>
          <p class="text-muted">Kelola item draf pengadaan aktif Anda atau lihat riwayat draf yang sudah dikirim ke Ketua Program Studi.</p>
        </div>
      </div>
    </div>

    <!-- HISTORY SECTION -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Riwayat Pengajuan (Draf Terkirim / Selesai)</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID Draf</th>
                  <th>Tahun Anggaran</th>
                  <th>Jumlah Item</th>
                  <th>Catatan Kaprodi</th>
                  <th>Status Draf</th>
                  <th class="text-end px-4">Detail</th>
                </tr>
              </thead>
              <tbody>
                @if ($hasDrafts)
                  @foreach ($drafts as $d)
                    <tr>
                      <td class="px-4 py-3">#{{ $d['id'] }}</td>
                      <td class="fw-semibold">{{ $d['tahun'] }}</td>
                      <td>{{ $d['item_count'] }} items</td>
                      <td style="max-width: 200px; white-space: normal;">
                        @if ($d['alasan_penolakan'])
                          <span class="small text-danger">{{ $d['alasan_penolakan'] }}</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @php
                          $badgeClass = $d['status'] === 'submitted' ? 'bg-info' : ($d['status'] === 'reviewed' ? 'bg-primary' : ($d['status'] === 'finalized' ? 'bg-success' : ($d['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary')));
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                          {{ strtoupper($d['status']) }}
                        </span>
                      </td>
                      <td class="text-end px-4">
                        <span class="small text-muted">Draf Terkunci</span>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="6">Belum ada riwayat pengajuan draf.</td>
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
