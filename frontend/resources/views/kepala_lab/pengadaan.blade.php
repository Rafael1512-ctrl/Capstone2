@extends('layouts.app')

@section('content')
  @php
    $totalDraftPrice = 0;
    $totalQty = 0;
    if ($hasItems) {
        foreach ($items as $item) {
            $totalDraftPrice += $item['harga_satuan'] * $item['jumlah'];
            $totalQty += $item['jumlah'];
        }
    }
  @endphp

  <div class="container-fluid">
    <!-- HEADER -->
    <div class="row mb-5">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h1 class="fs-3 mb-1 fw-bold text-dark">Draf Pengadaan Tahunan</h1>
            <p class="text-muted mb-0">Buat, kelola, dan ajukan pengadaan barang inventaris & BHP baru ke Ketua Program Studi.</p>
          </div>
          @if ($hasActiveDraft)
            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 fs-7 fw-semibold border border-warning border-opacity-25 text-uppercase">
              <i class="ti ti-file-text me-1"></i>Status: {{ $activeDraft['status'] }}
            </span>
          @endif
        </div>
      </div>
    </div>

    @if (!$hasActiveDraft)
      <!-- NO ACTIVE DRAFT: SHOW CREATE BUTTON -->
      <div class="row mt-6">
        <div class="col-12 col-md-6 col-lg-5 mx-auto">
          <div class="card shadow-sm border-0 py-5 px-4 text-center">
            <div class="icon-shape icon-xxl bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-4" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
              <i class="ti ti-file-plus fs-1 text-primary"></i>
            </div>
            <h4 class="fw-bold mb-2">Mulai Pengadaan Baru</h4>
            <p class="text-muted mb-4 px-3">Anda belum memiliki draf pengadaan aktif. Tentukan tahun anggaran dan pilih Ketua Program Studi untuk memulai.</p>
            
            <form action="/kepala-lab/pengadaan/create-draft" method="POST" class="text-start">
              @csrf
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Tahun Anggaran</label>
                <input class="form-control text-center fw-bold" type="number" name="tahun" value="{{ date('Y') }}" required>
              </div>
              <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Pilih Ketua Prodi</label>
                <select class="form-select" name="ketua_prodi_id" required>
                  <option value="">-- Pilih Kaprodi --</option>
                  @if ($hasKaprodiList)
                    @foreach ($kaprodiList as $kaprodi)
                      <option value="{{ $kaprodi['id'] }}">{{ $kaprodi['nama'] }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">
                <i class="ti ti-plus me-1"></i> Buat Draf Baru
              </button>
            </form>
          </div>
        </div>
      </div>
    @else
      <!-- ACTIVE DRAFT EXISTS -->
      
      <!-- KPI WIDGETS -->
      <div class="row g-4 mb-4">
        <!-- Widget 1: Total Anggaran -->
        <div class="col-md-4 col-12">
          <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
              <div>
                <span class="small text-uppercase fw-semibold" style="color: #94a3b8; font-size: 11px; letter-spacing: 0.5px;">Total Estimasi Anggaran</span>
                <h3 class="mb-0 mt-1 fw-bold text-white" style="font-size: 24px;">Rp {{ number_format($totalDraftPrice, 0, ',', '.') }}</h3>
              </div>
              <div class="bg-primary bg-opacity-20 text-primary p-3 rounded-3" style="background-color: rgba(230, 98, 57, 0.15) !important;">
                <i class="ti ti-wallet fs-2 text-primary" style="color: #E66239 !important;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Widget 2: Jenis Barang -->
        <div class="col-md-4 col-12">
          <div class="card border-0 shadow-sm bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Jumlah Jenis Barang</span>
                <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 24px;">{{ $itemCount }} <span class="fs-6 fw-normal text-muted">Barang</span></h3>
              </div>
              <div class="bg-info bg-opacity-10 text-info p-3 rounded-3" style="background-color: rgba(0, 184, 219, 0.1) !important;">
                <i class="ti ti-box fs-2 text-info" style="color: #00B8DB !important;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Widget 3: Total Kuantitas -->
        <div class="col-md-4 col-12">
          <div class="card border-0 shadow-sm bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Total Unit / Kuantitas</span>
                <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 24px;">{{ $totalQty }} <span class="fs-6 fw-normal text-muted">Unit</span></h3>
              </div>
              <div class="bg-success bg-opacity-10 text-success p-3 rounded-3" style="background-color: rgba(0, 201, 81, 0.1) !important;">
                <i class="ti ti-refresh fs-2 text-success" style="color: #00C951 !important;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Settings Column (Left Column - col-lg-3 for compact display) -->
        <div class="col-lg-3 col-12">
          
          <!-- DRAFT SETTINGS CARD (Now placed at the very top of column) -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent px-4 py-3 border-bottom">
              <h5 class="mb-0 fw-bold text-dark fs-6">Pengaturan Draf</h5>
            </div>
            <div class="card-body p-4">
              <form action="/kepala-lab/pengadaan/update-draft/{{ $activeDraft['id'] }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label small fw-semibold text-secondary">Tahun Anggaran</label>
                  <input class="form-control form-control-sm text-center fw-bold" type="number" name="tahun" value="{{ $activeDraft['tahun'] }}" required>
                </div>
                <div class="mb-4">
                  <label class="form-label small fw-semibold text-secondary">Ketua Prodi (Kaprodi)</label>
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
                
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-primary flex-grow-1 py-2 fw-semibold" type="submit">
                    <i class="ti ti-device-floppy me-1"></i> Simpan
                  </button>
                  <button class="btn btn-sm btn-outline-danger px-2.5" type="submit" form="delete-draft-form" title="Hapus Seluruh Draf">
                    <i class="ti ti-trash"></i>
                  </button>
                </div>
              </form>

              <!-- Hidden delete draft form -->
              <form id="delete-draft-form" action="/kepala-lab/pengadaan/delete-draft/{{ $activeDraft['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh draf ini beserta isinya?');" style="display: none;">
                @csrf
              </form>
            </div>
          </div>
        </div>

        <!-- Detail Table & Submission Column (Right Column - col-lg-9 to give more horizontal width) -->
        <div class="col-lg-9 col-12">
          <!-- TABLE CARD -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-dark">Detail Item Pengadaan</h5>
                <span class="badge bg-light text-secondary border px-2.5 py-1.5 fs-8 fw-semibold">{{ $itemCount }} item</span>
              </div>
              <button class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="ti ti-plus fs-6"></i> Tambah Item
              </button>
            </div>
            
            <div class="table-responsive">
              <table class="table align-items-center mb-0 table-hover">
                <thead class="table-light text-secondary">
                  <tr>
                    <th class="px-4 py-3 small fw-bold text-uppercase" style="font-size: 11px;">Barang & Rasionalisasi</th>
                    <th class="small fw-bold text-uppercase" style="font-size: 11px;">Tipe</th>
                    <th class="small fw-bold text-uppercase" style="font-size: 11px;">Harga Satuan</th>
                    <th class="small fw-bold text-uppercase text-center" style="font-size: 11px;">Qty</th>
                    <th class="small fw-bold text-uppercase" style="font-size: 11px;">Total</th>
                    <th class="text-end px-4 small fw-bold text-uppercase" style="font-size: 11px; width: 80px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @if ($hasItems)
                    @foreach ($items as $item)
                      <tr class="align-middle">
                        <td class="px-4 py-3">
                          <div class="fw-semibold text-dark">{{ $item['nama_barang'] }}</div>
                          <div class="small text-muted mb-1" style="max-width: 400px; white-space: normal; line-height: 1.4;">
                            {{ $item['rasionalisasi'] }}
                          </div>
                          @if ($item['link_pembelian'])
                            <a class="small text-primary text-decoration-none d-inline-flex align-items-center" href="{{ $item['link_pembelian'] }}" target="_blank">
                              <i class="ti ti-link me-1"></i>Link Pembelian
                            </a>
                          @endif
                        </td>
                        <td>
                          @if ($item['tipe_barang'] === 'inventaris')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1 fs-8 text-capitalize">
                              Inventaris
                            </span>
                          @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 fs-8 text-capitalize">
                              BHP
                            </span>
                          @endif
                        </td>
                        <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="text-center fw-medium">{{ $item['jumlah'] }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') }}</td>
                        <td class="text-end px-4">
                          <form action="/kepala-lab/pengadaan/delete-item/{{ $item['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                            @csrf
                            <button class="btn btn-icon btn-sm btn-outline-danger border-0 rounded-circle" type="submit" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                              <i class="ti ti-trash fs-5"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td class="text-center py-5 text-muted" colspan="6">
                        <div class="py-4">
                          <i class="ti ti-clipboard-x fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                          <span>Draf ini belum memiliki item pengadaan. Tambah item menggunakan tombol di kanan atas.</span>
                        </div>
                      </td>
                    </tr>
                  @endif
                </tbody>
                @if ($hasItems)
                  <tfoot class="table-light border-top">
                    <tr class="align-middle">
                      <td colspan="4" class="text-end fw-bold text-dark py-3">Total Anggaran Draf:</td>
                      <td colspan="2" class="fw-extrabold text-primary py-3 fs-5">Rp {{ number_format($totalDraftPrice, 0, ',', '.') }}</td>
                    </tr>
                  </tfoot>
                @endif
              </table>
            </div>
          </div>

          <!-- SUBMISSION CARD (Directly below the items review table) -->
          <div class="card shadow-sm border-0 mt-4 bg-white">
            <div class="card-body p-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
              <div class="text-start">
                <h5 class="mb-1 fw-bold text-dark">Finalisasi & Kirim Pengajuan</h5>
                <p class="text-muted small mb-0">Pastikan semua item barang dan estimasi anggaran sudah benar. Draf akan dikunci setelah dikirim.</p>
              </div>
              <form action="/kepala-lab/pengadaan/submit/{{ $activeDraft['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim draf ini? Setelah dikirim draf akan dikunci dan tidak dapat diubah lagi.');">
                @csrf
                <button class="btn btn-success px-4 py-2.5 fw-bold" type="submit">
                  <i class="ti ti-send me-1"></i> Kirim ke Kaprodi
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

  <!-- ADD ITEM MODAL -->
  @if ($hasActiveDraft)
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
          <div class="modal-header bg-light border-bottom px-4 py-3">
            <h5 class="modal-title fw-bold text-dark fs-5" id="addItemModalLabel">
              <i class="ti ti-plus text-primary me-1"></i> Tambah Item Baru
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form action="/kepala-lab/pengadaan/add-item" method="POST">
              @csrf
              <input type="hidden" name="draft_id" value="{{ $activeDraft['id'] }}">
              
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Nama Barang</label>
                <input class="form-control" type="text" name="nama_barang" placeholder="cth. Monitor Dell 24 inch" required>
              </div>
              
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Tipe Barang</label>
                <select class="form-select" name="tipe_barang" required>
                  <option value="inventaris">Inventaris (Barang Tetap)</option>
                  <option value="bhp">BHP (Barang Habis Pakai)</option>
                </select>
              </div>

              <!-- Price and Qty in one row -->
              <div class="row g-2 mb-3">
                <div class="col-7">
                  <label class="form-label small fw-semibold text-secondary">Harga Satuan</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light text-muted">Rp</span>
                    <input class="form-control" type="number" name="harga_satuan" placeholder="250000" min="0" required>
                  </div>
                </div>
                <div class="col-5">
                  <label class="form-label small fw-semibold text-secondary">Jumlah (Qty)</label>
                  <input class="form-control text-center" type="number" name="jumlah" placeholder="5" min="1" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Rasionalisasi Item</label>
                <textarea class="form-control" name="rasionalisasi" rows="2" placeholder="Mengapa barang ini dibutuhkan..." required></textarea>
              </div>
              
              <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Link Pembelian (Opsional)</label>
                <input class="form-control" type="url" name="link_pembelian" placeholder="https://tokopedia.com/...">
              </div>
              
              <div class="d-flex gap-2 justify-content-end border-top pt-3">
                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary px-4 fw-semibold" type="submit">
                  <i class="ti ti-plus me-1"></i> Tambahkan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection
