@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6 d-flex justify-content-between align-items-center">
          <div>
            <h1 class="fs-3 mb-1">Staf Laboratorium - Kelola Stok BHP</h1>
            <p class="text-muted">Pantau dan kelola ketersediaan barang habis pakai (BHP) laboratorium secara real-time.</p>
          </div>
          <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addBhpForm">
            <i class="ti ti-plus me-1"></i>Tambah BHP Baru
          </button>
        </div>
      </div>
    </div>

    <!-- LOW STOCK BHP WARNINGS -->
    @php
      $lowStockBhps = array_filter($bhpList ?? [], function($item) {
          return $item['stok'] <= $item['stok_minimum'];
      });
    @endphp

    @if(count($lowStockBhps) > 0)
      <div class="alert alert-warning border-start border-4 border-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="ti ti-alert-triangle fs-3 me-3 text-warning"></i>
        <div>
          <h5 class="alert-heading mb-1 fw-bold text-dark">Peringatan Stok Minimum!</h5>
          <p class="mb-2 text-dark">Terdapat {{ count($lowStockBhps) }} item BHP yang mendekati atau telah berada di bawah batas minimum ketersediaan:</p>
          <ul class="mb-0 text-dark ps-4">
            @foreach($lowStockBhps as $lowBhp)
              <li>
                <strong>{{ $lowBhp['nama_bhp'] }}</strong> di {{ $lowBhp['nama_ruangan'] ?? 'Gudang' }} — 
                Stok saat ini: <span class="badge bg-danger">{{ $lowBhp['stok'] }} {{ $lowBhp['satuan'] }}</span> 
                (Stok Minimum: {{ $lowBhp['stok_minimum'] }} {{ $lowBhp['satuan'] }})
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <!-- COLLAPSIBLE FORM -->
    <div id="addBhpForm" class="collapse mb-4">
      <div class="card p-4 border border-opacity-25 rounded-2">
        <h5 class="mb-3">Pendaftaran BHP Baru</h5>
        <form action="/staf-lab/bhp/create" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-2 col-12">
              <label class="form-label">Nama BHP</label>
              <input class="form-control" type="text" name="nama_bhp" placeholder="cth. Kertas A4" required>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Ruangan</label>
              <select class="form-select" name="ruangan_id" required>
                @foreach ($ruangan as $r)
                  <option value="{{ $r['id'] }}">{{ $r['kode_ruangan'] }} - {{ $r['nama_ruangan'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Stok Awal</label>
              <input class="form-control" type="number" name="stok" placeholder="0" min="0" required>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Stok Minimum</label>
              <input class="form-control" type="number" name="stok_minimum" placeholder="0" min="0" required>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Satuan</label>
              <input class="form-control" type="text" name="satuan" placeholder="cth. rim, box" required>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Kondisi</label>
              <select class="form-select" name="kondisi">
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
              </select>
            </div>
          </div>
          <div class="mt-3 d-flex justify-content-end gap-2">
            <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addBhpForm">Batal</button>
            <button class="btn btn-primary btn-sm" type="submit">Simpan BHP</button>
          </div>
        </form>
      </div>
    </div>

    <!-- BHP LIST TABLE -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-column flex-md-row gap-2">
            <h5 class="mb-0">Inventori Barang Habis Pakai (BHP)</h5>
            <div class="position-relative" style="min-width: 250px;">
              <input type="text" id="bhpSearchInput" class="form-control form-control-sm ps-5" placeholder="Cari nama BHP atau ruangan...">
              <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            </div>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">ID</th>
                  <th>Nama BHP</th>
                  <th>Ruangan</th>
                  <th>Satuan</th>
                  <th>Kondisi</th>
                  <th class="text-center">Jumlah Stok</th>
                  <th class="text-center">Stok Minimum</th>
                  <th class="text-end px-4">Aksi Perbarui Stok</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($bhpList) && count($bhpList) > 0)
                  @foreach ($bhpList as $bhp)
                    <tr>
                      <td class="px-4 py-3">{{ $bhp['id'] }}</td>
                      <td class="fw-semibold">{{ $bhp['nama_bhp'] }}</td>
                      <td>{{ $bhp['nama_ruangan'] ?? 'Gudang Utama' }}</td>
                      <td>{{ $bhp['satuan'] }}</td>
                      <td>
                        <span class="badge {{ $bhp['kondisi'] === 'baik' ? 'bg-success' : 'bg-danger' }}">
                          {{ strtoupper($bhp['kondisi']) }}
                        </span>
                      </td>
                      <td class="text-center fw-bold">
                        <span class="{{ $bhp['stok'] <= $bhp['stok_minimum'] ? 'text-danger' : 'text-success' }}">{{ $bhp['stok'] }}</span>
                      </td>
                      <td class="text-center text-muted fw-semibold">
                        {{ $bhp['stok_minimum'] }}
                      </td>
                      <td class="text-end px-4">
                        <form class="d-inline-flex gap-2 align-items-center justify-content-end" action="/staf-lab/bhp/update-stock/{{ $bhp['id'] }}" method="POST">
                          @csrf
                          <div class="d-flex flex-column gap-1">
                            <div class="d-inline-flex gap-1 align-items-center">
                              <small class="text-muted" style="min-width: 35px;">Stok:</small>
                              <input class="form-control form-control-sm px-1 py-0" type="number" name="stok" value="{{ $bhp['stok'] }}" min="0" style="max-width:70px; font-size: 0.8rem;" required>
                            </div>
                            <div class="d-inline-flex gap-1 align-items-center">
                              <small class="text-muted" style="min-width: 35px;">Min:</small>
                              <input class="form-control form-control-sm px-1 py-0" type="number" name="stok_minimum" value="{{ $bhp['stok_minimum'] }}" min="0" style="max-width:70px; font-size: 0.8rem;" required>
                            </div>
                          </div>
                          <select class="form-select form-select-sm" name="kondisi" style="max-width:90px; font-size: 0.8rem;">
                            <option value="baik" {{ $bhp['kondisi'] === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ $bhp['kondisi'] === 'rusak' ? 'selected' : '' }}>Rusak</option>
                          </select>
                          <button class="btn btn-sm btn-success py-1" type="submit" style="padding-left: 8px; padding-right: 8px;">
                            <i class="ti ti-device-floppy"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="8">Belum ada data BHP terdaftar.</td>
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
      document.getElementById('bhpSearchInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
          // Exclude empty state row
          if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

          const nama = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
          const ruangan = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
          
          if (nama.includes(query) || ruangan.includes(query)) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        // Handle empty search results dynamic notice
        let emptySearchRow = document.getElementById('emptySearchRow');
        if (visibleCount === 0 && query !== '') {
          if (!emptySearchRow) {
            emptySearchRow = document.createElement('tr');
            emptySearchRow.id = 'emptySearchRow';
            emptySearchRow.innerHTML = `<td colspan="8" class="text-center py-4 text-muted">Tidak ada BHP yang cocok dengan pencarian "${e.target.value}"</td>`;
            document.querySelector('table tbody').appendChild(emptySearchRow);
          } else {
            emptySearchRow.style.display = '';
            emptySearchRow.querySelector('td').textContent = `Tidak ada BHP yang cocok dengan pencarian "${e.target.value}"`;
          }
        } else {
          if (emptySearchRow) {
            emptySearchRow.style.display = 'none';
          }
        }
      });
    });
  </script>
@endsection
