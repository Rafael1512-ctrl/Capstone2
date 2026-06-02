@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Staf Laboratorium - Riwayat Mutasi BHP</h1>
          <p class="text-muted">Laporan terperinci penggunaan barang habis pakai (BHP) sebagai material pemeliharaan aset laboratorium.</p>
        </div>
      </div>
    </div>

    <!-- MUTASI LIST TABLE -->
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm border border-opacity-10">
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">Jurnal Penggunaan & Mutasi Stok BHP</h5>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
              Total Log: {{ count($mutasiList ?? []) }} Catatan
            </span>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3" style="width: 80px;">ID Log</th>
                  <th>Tanggal Penggunaan</th>
                  <th>Nama BHP</th>
                  <th class="text-center">Jumlah Pemakaian</th>
                  <th>Aset/Inventaris Terawat</th>
                  <th>Deskripsi Pemeliharaan</th>
                  <th>Staf Lab (Petugas)</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($mutasiList) && count($mutasiList) > 0)
                  @foreach ($mutasiList as $log)
                    <tr>
                      <td class="px-4 py-3 text-secondary font-monospace">#{{ $log['id'] }}</td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <i class="ti ti-calendar text-secondary"></i>
                          <span>{{ date('d M Y, H:i', strtotime($log['tanggal'])) }}</span>
                        </div>
                      </td>
                      <td>
                        <span class="fw-semibold text-dark">{{ $log['nama_bhp'] }}</span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1">
                          -{{ $log['jumlah'] }} {{ $log['satuan'] }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex flex-column">
                          <span class="fw-semibold text-dark">{{ $log['nama_aset'] }}</span>
                          <small class="text-secondary font-monospace">{{ $log['nomor_label'] }}</small>
                        </div>
                      </td>
                      <td class="text-wrap" style="max-width: 250px;">
                        <span class="text-secondary">{{ $log['deskripsi'] ?: 'Tidak ada deskripsi pemeliharaan.' }}</span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="avatar avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.75rem;">
                            {{ strtoupper(substr($log['petugas'], 0, 2)) }}
                          </div>
                          <span class="text-dark">{{ $log['petugas'] }}</span>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-5 text-muted" colspan="7">
                      <div class="d-flex flex-column align-items-center justify-content-center p-4">
                        <i class="ti ti-history-off fs-1 text-secondary mb-2"></i>
                        <h6 class="mb-0 fw-semibold text-secondary">Belum ada riwayat mutasi BHP.</h6>
                        <small class="text-muted">Penggunaan barang habis pakai otomatis tercatat ketika staf lab menginput log pemeliharaan aset.</small>
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
