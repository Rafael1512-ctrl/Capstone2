@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Staf Laboratorium - Log Maintenance & Kondisi Inventaris</h1>
          <p class="text-muted">Catat pemeliharaan aset inventaris laboratorium secara rutin. Stok BHP yang digunakan akan terpotong secara otomatis oleh sistem.</p>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- LOG NEW MAINTENANCE FORM -->
      <div class="col-lg-4 col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Catat Maintenance Baru</h5>
          </div>
          <div class="card-body p-4">
            <form action="/staf-lab/maintenance/create" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label">Pilih Barang Aset / Inventaris</label>
                <select class="form-select" name="inventaris_id" required>
                  @foreach ($inventaris as $inv)
                    <option value="{{ $inv['id'] }}">{{ $inv['nomor_label'] }} - {{ $inv['nama_barang'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi / Detail Pemeliharaan</label>
                <textarea class="form-control" name="deskripsi" rows="3" placeholder="cth. Pembersihan debu kipas angin dan re-pasta processor." required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Status Akhir Kondisi Barang</label>
                <select class="form-select" name="status_akhir" required>
                  <option value="baik">Baik (Siap Digunakan)</option>
                  <option value="perlu_perbaikan">Perlu Perbaikan</option>
                  <option value="rusak">Rusak (Tidak Dapat Digunakan)</option>
                </select>
              </div>
              <hr>
              <div class="mb-3">
                <label class="form-label fw-semibold">Gunakan Bahan Habis Pakai (BHP)?</label>
                <select class="form-select" name="bhp_id_used">
                  <option value="">-- Tidak Menggunakan BHP --</option>
                  @foreach ($bhpList as $bhp)
                    <option value="{{ $bhp['id'] }}">{{ $bhp['nama_bhp'] }} (Stok: {{ $bhp['stok'] }})</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Jumlah BHP yang Digunakan</label>
                <input class="form-control" type="number" name="qty_bhp_used" placeholder="0" min="0">
                <small class="text-muted form-text">Kosongkan atau isi 0 jika tidak menggunakan BHP.</small>
              </div>
              <button class="btn btn-primary w-100 mt-2" type="submit">Simpan Log Maintenance</button>
            </form>
          </div>
        </div>
      </div>

      <!-- LOGS LIST TABLE -->
      <div class="col-lg-8 col-12">
        <div class="card">
          <div class="card-header bg-transparent px-4 py-3 border-bottom">
            <h5 class="mb-0">Riwayat Pemeliharaan Laboratorium</h5>
          </div>
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 table-hover">
              <thead class="table-light">
                <tr>
                  <th class="px-4 py-3">Tanggal</th>
                  <th>Aset / Label</th>
                  <th>Petugas</th>
                  <th>Deskripsi Pemeliharaan</th>
                  <th>Penggunaan BHP</th>
                </tr>
              </thead>
              <tbody>
                @if (isset($logs) && count($logs) > 0)
                  @foreach ($logs as $log)
                    <tr>
                      <td class="px-4 py-3">{{ \Carbon\Carbon::parse($log['tanggal_maintenance'])->translatedFormat('d M Y') }}</td>
                      <td>
                        <div class="fw-semibold">{{ $log['nomor_label'] }}</div>
                      </td>
                      <td>{{ $log['petugas'] }}</td>
                      <td>{{ $log['deskripsi'] }}</td>
                      <td>
                        @if ($log['nama_bhp'])
                          <span class="badge bg-warning-subtle text-warning-emphasis">{{ $log['qty_bhp_used'] }} {{ $log['nama_bhp'] }}</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center py-5 text-muted" colspan="5">Belum ada riwayat pemeliharaan dicatat.</td>
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
