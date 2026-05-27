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

    <!-- COLLAPSIBLE FORM -->
    <div id="addBhpForm" class="collapse mb-4">
      <div class="card p-4 border border-opacity-25 rounded-2">
        <h5 class="mb-3">Pendaftaran BHP Baru</h5>
        <form action="/staf-lab/bhp/create" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-3 col-12">
              <label class="form-label">Nama BHP</label>
              <input class="form-control" type="text" name="nama_bhp" placeholder="cth. Kertas A4, Spidol" required>
            </div>
            <div class="col-md-3 col-12">
              <label class="form-label">Ruangan Penyimpanan</label>
              <select class="form-select" name="ruangan_id" required>
                @foreach ($ruangan as $r)
                  <option value="{{ $r['id'] }}">{{ $r['kode_ruangan'] }} - {{ $r['nama_ruangan'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Jumlah Stok Awal</label>
              <input class="form-control" type="number" name="stok" placeholder="0" min="0" required>
            </div>
            <div class="col-md-2 col-12">
              <label class="form-label">Satuan</label>
              <input class="form-control" type="text" name="satuan" placeholder="cth. rim, box, pcs" required>
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
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Inventori Barang Habis Pakai (BHP)</h5>
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
                        <span class="{{ $bhp['stok'] <= 5 ? 'text-danger' : 'text-success' }}">{{ $bhp['stok'] }}</span>
                      </td>
                      <td class="text-end px-4">
                        <form class="d-inline-flex gap-2 align-items-center justify-content-end" action="/staf-lab/bhp/update-stock/{{ $bhp['id'] }}" method="POST">
                          @csrf
                          <input class="form-control form-control-sm" type="number" name="stok" value="{{ $bhp['stok'] }}" min="0" style="max-width:80px;" required>
                          <select class="form-select form-select-sm" name="kondisi" style="max-width:100px;">
                            <option value="baik" {{ $bhp['kondisi'] === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ $bhp['kondisi'] === 'rusak' ? 'selected' : '' }}>Rusak</option>
                          </select>
                          <button class="btn btn-sm btn-success" type="submit">
                            <i class="ti ti-device-floppy"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-4" colspan="7">Belum ada data BHP terdaftar.</td>
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
