@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Kepala Lab - Draf Pengadaan Tahunan</h1>
          <p class="text-muted">Buat dan kelola pengadaan barang (inventaris & BHP) baru untuk diajukan ke Ketua Program Studi.</p>
        </div>
      </div>
    </div>

    @if (!$hasActiveDraft)
      <!-- NO ACTIVE DRAFT: SHOW CREATE BUTTON -->
      <div class="row">
        <div class="col-12 col-md-6 mx-auto">
          <div class="card text-center p-5">
            <div class="icon-shape icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-4">
              <i class="ti ti-file-plus fs-3"></i>
            </div>
            <h4>Mulai Pengadaan Baru</h4>
            <p class="text-muted">Anda belum memiliki draf pengadaan aktif. Masukkan tahun anggaran untuk memulai.</p>
            <form action="/kepala-lab/pengadaan/create-draft" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label">Tahun Anggaran</label>
                <input class="form-control mx-auto text-center" type="number" name="tahun" value="{{ date('Y') }}" style="max-width:300px;" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Pilih Ketua Prodi</label>
                <select class="form-select mx-auto" name="ketua_prodi_id" style="max-width:300px;" required>
                  <option value="">-- Pilih Kaprodi --</option>
                  @if ($hasKaprodiList)
                    @foreach ($kaprodiList as $kaprodi)
                      <option value="{{ $kaprodi['id'] }}">{{ $kaprodi['nama'] }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <button class="btn btn-primary" type="submit">Buat Draf Baru</button>
            </form>
          </div>
        </div>
      </div>
    @else
      <!-- ACTIVE DRAFT EXISTS -->
      <div class="row g-4">
        <!-- Draft Info & Add Item -->
        <div class="col-lg-4 col-12">
          <div class="card mb-4">
            <div class="card-header bg-transparent px-4 py-3 border-bottom">
              <h5 class="mb-0">Info Draf Aktif</h5>
            </div>
            <div class="card-body p-4">
              <form action="/kepala-lab/pengadaan/update-draft/{{ $activeDraft['id'] }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label text-secondary">Tahun Anggaran</label>
                  <input class="form-control form-control-sm" type="number" name="tahun" value="{{ $activeDraft['tahun'] }}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label text-secondary">Pilih Ketua Prodi</label>
                  <select class="form-select form-select-sm" name="ketua_prodi_id" required>
                    <option value="">-- Pilih Kaprodi --</option>
                    @if ($hasKaprodiList)
                      @foreach ($kaprodiList as $kaprodi)
                        <option value="{{ $kaprodi['id'] }}" {{ $activeDraft['ketua_prodi_id'] == $kaprodi['id'] ? 'selected' : '' }}>
                          {{ $kaprodi['nama'] }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="d-flex justify-content-between mb-3">
                  <span class="text-secondary">Status Draf:</span>
                  <span class="badge bg-warning text-capitalize">{{ $activeDraft['status'] }}</span>
                </div>
                <div class="d-flex gap-2 mb-3">
                  <button class="btn btn-sm btn-primary flex-grow-1" type="submit">Simpan Draf</button>
                </div>
              </form>
              
              <form class="d-flex gap-2" action="/kepala-lab/pengadaan/delete-draft/{{ $activeDraft['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh draf ini beserta isinya?');">
                @csrf
                <button class="btn btn-sm btn-outline-danger flex-grow-1" type="submit">Hapus Draf</button>
              </form>
              <hr>
              <form class="d-grid" action="/kepala-lab/pengadaan/submit/{{ $activeDraft['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim draf ini? Setelah dikirim draf akan dikunci dan tidak dapat diubah lagi.');">
                @csrf
                <button class="btn btn-success" type="submit">
                  <i class="ti ti-send me-1"></i>Kirim ke Kaprodi
                </button>
              </form>
            </div>
          </div>

          <div class="card">
            <div class="card-header bg-transparent px-4 py-3 border-bottom">
              <h5 class="mb-0">Tambah Item ke Draf</h5>
            </div>
            <div class="card-body p-4">
              <form action="/kepala-lab/pengadaan/add-item" method="POST">
                @csrf
                <input type="hidden" name="draft_id" value="{{ $activeDraft['id'] }}">
                <div class="mb-3">
                  <label class="form-label">Nama Barang</label>
                  <input class="form-control" type="text" name="nama_barang" placeholder="cth. Monitor Dell 24 inch" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Tipe Barang</label>
                  <select class="form-select" name="tipe_barang" required>
                    <option value="inventaris">Inventaris (Barang Tetap)</option>
                    <option value="bhp">BHP (Barang Habis Pakai)</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Harga Satuan</label>
                  <input class="form-control" type="number" name="harga_satuan" placeholder="cth. 2500000" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Jumlah</label>
                  <input class="form-control" type="number" name="jumlah" placeholder="cth. 5" min="1" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Rasionalisasi Item</label>
                  <textarea class="form-control" name="rasionalisasi" rows="2" placeholder="Alasan butuh barang ini..." required></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Link Pembelian (Opsional)</label>
                  <input class="form-control" type="url" name="link_pembelian" placeholder="https://tokopedia.com/...">
                </div>
                <button class="btn btn-primary w-100" type="submit">Tambah ke Draf</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Table of Items -->
        <div class="col-lg-8 col-12">
          <div class="card">
            <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Detail Draf Pengadaan</h5>
              <span class="small text-secondary">{{ $itemCount }} item terdaftar</span>
            </div>
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Barang & Rasionalisasi</th>
                    <th>Tipe</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th class="text-end px-4">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @if ($hasItems)
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
                        <td class="text-end px-4">
                          <form class="d-inline" action="/kepala-lab/pengadaan/delete-item/{{ $item['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini dari draf?');">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                              <i class="ti ti-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td class="text-center py-5" colspan="6">Draf ini belum memiliki item pengadaan. Tambah di panel kiri.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection
