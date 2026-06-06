@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1 fw-bold text-dark">Riwayat Perawatan Aset Laboratorium</h1>
          <p class="text-muted">Daftar seluruh aktivitas pemeliharaan, perbaikan, dan kondisi aset inventaris laboratorium.</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Log Perawatan & Perbaikan</h5>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 fs-8 fw-semibold">
              Total: {{ isset($logs) ? count($logs) : 0 }} catatan
            </span>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-middle mb-0 table-hover">
              <thead class="table-light text-secondary">
                <tr>
                  <th class="px-4 py-3 small fw-bold text-uppercase" style="font-size: 11px;">Tanggal</th>
                  <th class="small fw-bold text-uppercase" style="font-size: 11px;">Aset / Nomor Label</th>
                  <th class="small fw-bold text-uppercase" style="font-size: 11px;">Petugas</th>
                  <th class="small fw-bold text-uppercase" style="font-size: 11px;">Deskripsi Pemeliharaan</th>
                  <th class="small fw-bold text-uppercase" style="font-size: 11px; text-align: center;">Perubahan Kondisi</th>
                  <th class="small fw-bold text-uppercase" style="font-size: 11px;">Penggunaan BHP</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($logs) && count($logs) > 0)
                  @foreach ($logs as $log)
                    <tr>
                      <td class="px-4 py-3">
                        <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($log['tanggal_maintenance'])->translatedFormat('d M Y') }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($log['tanggal_maintenance'])->format('H:i') }} WIB</small>
                      </td>
                      <td>
                        <div class="fw-bold text-dark mb-0 d-inline-flex align-items-center">
                          <i class="ti ti-barcode me-1.5 text-primary fs-5"></i>{{ $log['nomor_label'] }}
                        </div>
                      </td>
                      <td>
                        <span class="d-inline-flex align-items-center text-dark small">
                          <i class="ti ti-user me-1.5 text-secondary"></i>{{ $log['petugas'] }}
                        </span>
                      </td>
                      <td style="max-width: 300px; white-space: normal; line-height: 1.4;">
                        <span class="small text-dark">{{ $log['deskripsi'] }}</span>
                      </td>
                      <td style="text-align: center;">
                        @php
                          $getBadge = function($kondisi) {
                              if ($kondisi === 'baik') return 'bg-success bg-opacity-10 text-success border border-success border-opacity-15';
                              if ($kondisi === 'perlu_perbaikan') return 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-15';
                              return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-15';
                          };
                          $getLabel = function($kondisi) {
                              if ($kondisi === 'baik') return 'Baik';
                              if ($kondisi === 'perlu_perbaikan') return 'Perlu Perbaikan';
                              return 'Rusak';
                          };
                        @endphp
                        <div class="d-flex align-items-center justify-content-center gap-1.5 small">
                          <span class="badge {{ $getBadge($log['kondisi_sebelum']) }} fs-9">{{ $getLabel($log['kondisi_sebelum']) }}</span>
                          <i class="ti ti-arrow-narrow-right text-muted fs-6"></i>
                          <span class="badge {{ $getBadge($log['kondisi_sesudah']) }} fs-9fw-bold">{{ $getLabel($log['kondisi_sesudah']) }}</span>
                        </div>
                      </td>
                      <td>
                        @if ($log['nama_bhp'])
                          <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-2 py-1 fs-8">
                            <i class="ti ti-package me-1"></i>{{ $log['qty_bhp_used'] }} {{ $log['nama_bhp'] }}
                          </span>
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-5 text-muted" colspan="6">
                      <div class="py-4">
                        <i class="ti ti-tool fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                        <span>Belum ada riwayat perawatan aset dicatat.</span>
                      </div>
                    </td>
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
