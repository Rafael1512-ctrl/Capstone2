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
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="mb-0">Riwayat Pengajuan (Draf Terkirim / Selesai)</h5>
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <select id="filterYear" class="form-select form-select-sm" style="width: 160px;">
                <option value="">Semua Tahun</option>
                @php
                  $years = isset($drafts) ? collect($drafts)->pluck('tahun')->unique()->sortDesc() : collect([]);
                @endphp
                @foreach ($years as $year)
                  <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">No.</th>
                  <th>Tahun Anggaran</th>
                  <th>Jumlah Item</th>
                  <th>Catatan Kaprodi</th>
                  <th>Status Draf</th>
                  <th class="text-center px-4">Detail Draft</th>
                </tr>
              </thead>
              <tbody>
                @if ($hasDrafts)
                  @foreach ($drafts as $d)
                    <tr>
                      <td class="px-4 py-3">{{ $loop->remaining + 1 }}</td>
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
                      <td class="text-center px-4">
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-info"
                          data-bs-toggle="modal"
                          data-bs-target="#modalDetailDraft{{ $d['id'] }}"
                        >
                          <i class="ti ti-eye me-1"></i>Lihat Detail
                        </button>
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

  {{-- MODAL DETAIL DRAFT (satu per draf) --}}
  @if ($hasDrafts)
    @foreach ($drafts as $d)
      @php
        $badgeClass = $d['status'] === 'submitted' ? 'bg-info' : ($d['status'] === 'reviewed' ? 'bg-primary' : ($d['status'] === 'finalized' ? 'bg-success' : ($d['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary')));
      @endphp
      <div
        class="modal fade"
        id="modalDetailDraft{{ $d['id'] }}"
        tabindex="-1"
        aria-labelledby="modalDetailDraftLabel{{ $d['id'] }}"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalDetailDraftLabel{{ $d['id'] }}">
                <i class="ti ti-file-description me-2"></i>
                Detail Draf #{{ $loop->remaining + 1 }} &mdash; Tahun {{ $d['tahun'] }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

              {{-- Info Draf --}}
              <div class="row g-3 mb-4">
                <div class="col-sm-4">
                  <p class="text-muted small mb-1">Status Draf</p>
                  <span class="badge {{ $badgeClass }} fs-6">{{ strtoupper($d['status']) }}</span>
                </div>
                <div class="col-sm-4">
                  <p class="text-muted small mb-1">Tahun Anggaran</p>
                  <p class="fw-semibold mb-0">{{ $d['tahun'] }}</p>
                </div>
                <div class="col-sm-4">
                  <p class="text-muted small mb-1">Jumlah Item</p>
                  <p class="fw-semibold mb-0">{{ $d['item_count'] }} item</p>
                </div>
                @if ($d['alasan_penolakan'])
                  <div class="col-12">
                    <p class="text-muted small mb-1">Catatan / Alasan Kaprodi</p>
                    <div class="alert alert-danger py-2 mb-0">
                      {{ $d['alasan_penolakan'] }}
                    </div>
                  </div>
                @endif
              </div>

              <hr>

              {{-- Tabel Items --}}
              <h6 class="mb-3"><i class="ti ti-list me-1"></i>Daftar Item</h6>
              @if (isset($d['items']) && count($d['items']) > 0)
                <div class="table-responsive">
                  <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                      <tr>
                        <th class="px-3">#</th>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Status Item</th>
                        <th>Catatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($d['items'] as $i => $item)
                        @php
                          $itemStatus = $item['status_item'] ?? 'pending';
                          $itemBadge  = $itemStatus === 'approved'
                            ? 'bg-success'
                            : ($itemStatus === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                          $itemLabel  = $itemStatus === 'approved'
                            ? 'Approved'
                            : ($itemStatus === 'rejected' ? 'Rejected' : 'Pending');
                        @endphp
                        <tr>
                          <td class="px-3">{{ $i + 1 }}</td>
                          <td>{{ $item['nama_barang'] }}</td>
                          <td><span class="badge bg-light text-dark border px-2 py-1 fs-8">{{ $item['kategori'] ?? '-' }}</span></td>
                          <td>{{ $item['jenis'] ?? '-' }}</td>
                          <td class="text-center">{{ $item['jumlah'] }}</td>
                          <td class="text-center">
                            <span class="badge {{ $itemBadge }}">{{ $itemLabel }}</span>
                          </td>
                          <td class="small">{{ $item['catatan'] ?? '-' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted text-center py-3">Tidak ada item dalam draf ini.</p>
              @endif

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @endif
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterYear = document.getElementById('filterYear');
      const tableRows = document.querySelectorAll('.table-responsive table tbody tr');

      function filterHistoryTable() {
        const selectedYear = filterYear.value;

        tableRows.forEach(row => {
          // Skip the "no data" row
          if (row.cells.length === 1) {
            return;
          }

          const yearCell = row.cells[1].textContent.trim();
          const yearMatch = selectedYear === '' || yearCell === selectedYear;

          row.style.display = yearMatch ? '' : 'none';
        });
      }

      if (filterYear) {
        filterYear.addEventListener('change', filterHistoryTable);
      }
    });
  </script>
@endsection
