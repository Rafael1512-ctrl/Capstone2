@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Ketua Prodi - Riwayat Draf Pengadaan</h1>
          <p class="text-muted">Lihat semua draf pengadaan tahunan dari Kepala Laboratorium yang telah Anda setujui (finalisasi) atau tolak.</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="mb-0">Riwayat Keputusan Draf</h5>
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <div class="position-relative">
                <input type="text" id="searchDraftId" class="form-control form-control-sm ps-4" placeholder="Cari ID Draf..." style="width: 200px;">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary"></i>
              </div>
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
                  <th>Tahun</th>
                  <th>Pengaju (Kepala Lab)</th>
                  <th>Total Items</th>
                  <th>Status Draf</th>
                  <th>Catatan Keputusan</th>
                  <th class="text-end px-4 py-3" style="width: 120px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($drafts) && count($drafts) > 0)
                  @foreach ($drafts as $d)
                    <tr>
                      <td class="px-4 py-3">{{ $loop->remaining + 1 }}</td>
                      <td class="fw-semibold">{{ $d['tahun'] }}</td>
                      <td>{{ $d['pengaju'] }}</td>
                      <td>{{ $d['total_items'] }} items</td>
                      <td>
                        @php
                          $badgeClass = $d['status'] === 'finalized' ? 'bg-success' : ($d['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                          {{ strtoupper($d['status']) }}
                        </span>
                      </td>
                      <td>
                        @if ($d['alasan_penolakan'])
                          <span class="text-muted small">{{ $d['alasan_penolakan'] }}</span>
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                      <td class="text-end px-4 py-3">
                        <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center gap-1 px-3" style="min-width: 108px;" href="/ketua-prodi/review/{{ $d['id'] }}">
                          <i class="ti ti-search"></i>
                          <span>Detail</span>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="7">Tidak ada riwayat draf pengadaan.</td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchDraftId');
      const filterYear = document.getElementById('filterYear');
      const tableRows = document.querySelectorAll('.table-responsive table tbody tr');

      function filterHistoryTable() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedYear = filterYear.value;

        tableRows.forEach(row => {
          if (row.cells.length === 1) {
            return;
          }

          const draftCell = row.cells[0].textContent.toLowerCase();
          const yearCell = row.cells[1].textContent.trim();
          const draftIdMatch = draftCell.includes(query);
          const yearMatch = selectedYear === '' || yearCell === selectedYear;

          row.style.display = draftIdMatch && yearMatch ? '' : 'none';
        });
      }

      if (searchInput) {
        searchInput.addEventListener('input', filterHistoryTable);
      }
      if (filterYear) {
        filterYear.addEventListener('change', filterHistoryTable);
      }
    });
  </script>
@endsection
