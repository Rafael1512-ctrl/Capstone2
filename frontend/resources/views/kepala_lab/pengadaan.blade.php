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
                        <p class="text-muted mb-0">Buat, kelola, dan ajukan pengadaan barang inventaris & BHP baru ke Ketua
                            Program Studi.</p>
                    </div>
                    @if ($hasActiveDraft)
                        <span
                            class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 fs-7 fw-semibold border border-warning border-opacity-25 text-uppercase">
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
                        <div class="icon-shape icon-xxl bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-4"
                            style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-file-plus fs-1 text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Mulai Pengadaan Baru</h4>
                        <p class="text-muted mb-4 px-3">Anda belum memiliki draf pengadaan aktif. Tentukan tahun anggaran
                            dan pilih Ketua Program Studi untuk memulai.</p>

                        <form action="/kepala-lab/pengadaan/create-draft" method="POST" class="text-start">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Tahun Anggaran</label>
                                <select class="form-select text-center fw-bold" name="tahun" required>
                                    @for ($y = date('Y') + 5; $y >= date('Y') - 1; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Pengajuan akan dikirim
                                    ke:</label>
                                <div class="p-3 bg-light rounded border border-2 border-primary border-opacity-25">
                                    <p class="mb-0 fw-semibold text-dark">
                                        <i class="ti ti-user-circle me-2 text-primary"></i>
                                        {{ $hasKaprodiList && isset($kaprodiList[0]) ? $kaprodiList[0]['nama'] : 'Ketua Prodi' }}
                                    </p>
                                </div>
                                <input type="hidden" name="ketua_prodi_id"
                                    value="{{ $hasKaprodiList && isset($kaprodiList[0]) ? $kaprodiList[0]['id'] : 'auto' }}">
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
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-uppercase fw-semibold"
                                    style="color: #94a3b8; font-size: 11px; letter-spacing: 0.5px;">Total Estimasi
                                    Anggaran</span>
                                <h3 class="mb-0 mt-1 fw-bold text-white" style="font-size: 24px;">Rp
                                    {{ number_format($totalDraftPrice, 0, ',', '.') }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-20 text-primary p-3 rounded-3"
                                style="background-color: rgba(230, 98, 57, 0.15) !important;">
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
                                <span class="text-muted small text-uppercase fw-semibold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">Jumlah Jenis Barang</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 24px;">{{ $itemCount }} <span
                                        class="fs-6 fw-normal text-muted">Barang</span></h3>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-3"
                                style="background-color: rgba(0, 184, 219, 0.1) !important;">
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
                                <span class="text-muted small text-uppercase fw-semibold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">Total Unit / Kuantitas</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 24px;">{{ $totalQty }} <span
                                        class="fs-6 fw-normal text-muted">Unit</span></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3"
                                style="background-color: rgba(0, 201, 81, 0.1) !important;">
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
                                    <select class="form-select form-select-sm text-center fw-bold" name="tahun" required>
                                        @for ($y = date('Y') + 5; $y >= date('Y') - 1; $y--)
                                            <option value="{{ $y }}" {{ $y == $activeDraft['tahun'] ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary">Ketua Prodi (Kaprodi)</label>
                                    <div class="p-2.5 bg-light rounded border border-2 border-primary border-opacity-25">
                                        <p class="mb-0 fw-semibold text-dark small">
                                            <i class="ti ti-user-circle me-1 text-primary"></i>
                                            {{ $hasKaprodiList && isset($kaprodiList[0]) ? $kaprodiList[0]['nama'] : 'Ketua Prodi' }}
                                        </p>
                                    </div>
                                    <input type="hidden" name="ketua_prodi_id"
                                        value="{{ $hasKaprodiList && isset($kaprodiList[0]) ? $kaprodiList[0]['id'] : $activeDraft['ketua_prodi_id'] }}">
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary flex-grow-1 py-2 fw-semibold" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Simpan
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger px-2.5" type="submit"
                                        form="delete-draft-form" title="Hapus Seluruh Draf">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </form>

                            <!-- Hidden delete draft form -->
                            <form id="delete-draft-form"
                                action="/kepala-lab/pengadaan/delete-draft/{{ $activeDraft['id'] }}" method="POST"
                                style="display: none;"
                                data-confirm="Apakah Anda yakin ingin menghapus seluruh draf ini? Semua item di dalamnya akan ikut terhapus dan tidak bisa dikembalikan.">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Detail Table & Submission Column (Right Column - col-lg-9 to give more horizontal width) -->
                <div class="col-lg-9 col-12">
                    <!-- TABLE CARD -->
                    <div class="card shadow-sm border-0">
                        <div
                            class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bold text-dark">Detail Item Pengadaan</h5>
                                <span
                                    class="badge bg-light text-secondary border px-2.5 py-1.5 fs-8 fw-semibold">{{ $itemCount }}
                                    item</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1 shadow-sm px-3"
                                    data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="ti ti-plus fs-6"></i> Tambah Item Baru
                                </button>
                                <button
                                    class="btn btn-outline-primary btn-sm fw-semibold d-flex align-items-center gap-1 shadow-sm px-3"
                                    data-bs-toggle="modal" data-bs-target="#replaceAssetModal">
                                    <i class="ti ti-replace fs-6"></i> Ganti Aset Lama
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 table-hover">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th class="px-4 py-3 small fw-bold text-uppercase" style="font-size: 11px;">Barang & Rasionalisasi</th>
                                        <th class="small fw-bold text-uppercase" style="font-size: 11px;">Kategori</th>
                                        <th class="small fw-bold text-uppercase" style="font-size: 11px;">Jenis</th>
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
                                                    <div class="small text-muted mb-1"
                                                        style="max-width: 400px; white-space: normal; line-height: 1.4;">
                                                        {{ $item['rasionalisasi'] }}
                                                    </div>
                                                    @if (isset($item['inventaris_digantikan_id']) && $item['inventaris_digantikan_id'])
                                                        <div class="mt-1 mb-2">
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-2.5 py-1.5 fs-8 text-wrap text-start d-inline-flex align-items-center">
                                                                <i class="ti ti-replace me-1.5 fs-6"></i>Menggantikan: {{ $item['label_digantikan'] }} - {{ $item['nama_digantikan'] }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($item['link_pembelian'])
                                                        <a class="small text-primary text-decoration-none d-inline-flex align-items-center"
                                                            href="{{ $item['link_pembelian'] }}" target="_blank">
                                                            <i class="ti ti-link me-1"></i>Link Pembelian
                                                        </a>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-light text-dark border px-2 py-1 fs-8">{{ $item['kategori'] ?? '-' }}</span></td>
                                                <td>{{ $item['jenis'] ?? '-' }}</td>
                                                <td>
                                                    @if ($item['tipe_barang'] === 'inventaris')
                                                        <span
                                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1 fs-8 text-capitalize">
                                                            Inventaris
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 fs-8 text-capitalize">
                                                            BHP
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                                <td class="text-center fw-medium">{{ $item['jumlah'] }}</td>
                                                <td class="fw-bold text-dark">Rp
                                                    {{ number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end px-4">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-icon btn-sm btn-outline-warning border-0 rounded-circle"
                                                            type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editItemModal{{ $item['id'] }}"
                                                            style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                            title="Edit Item">
                                                            <i class="ti ti-edit fs-5"></i>
                                                        </button>
                                                        <form action="/kepala-lab/pengadaan/delete-item/{{ $item['id'] }}"
                                                            method="POST"
                                                            class="m-0"
                                                            data-confirm="Apakah Anda yakin ingin menghapus item ini?">
                                                            @csrf
                                                            <button
                                                                class="btn btn-icon btn-sm btn-outline-danger border-0 rounded-circle"
                                                                type="submit"
                                                                style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                                title="Hapus Item">
                                                                <i class="ti ti-trash fs-5"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center py-5 text-muted" colspan="8">
                                                <div class="py-4">
                                                    <i
                                                        class="ti ti-clipboard-x fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                                                    <span>Draf ini belum memiliki item pengadaan. Tambah item menggunakan
                                                        tombol di kanan atas.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if ($hasItems)
                                    <tfoot class="table-light border-top">
                                        <tr class="align-middle">
                                            <td colspan="6" class="text-end fw-bold text-dark py-3">Total Anggaran
                                                Draf:</td>
                                            <td colspan="2" class="fw-extrabold text-primary py-3 fs-5">Rp
                                                {{ number_format($totalDraftPrice, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if ($hasActiveDraft && $hasItems)
                        @foreach ($items as $item)
                            <!-- EDIT ITEM MODAL -->
                            <div class="modal fade" id="editItemModal{{ $item['id'] }}" tabindex="-1" aria-labelledby="editItemModalLabel{{ $item['id'] }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 12px;">
                                        <div class="modal-header bg-light border-bottom px-4 py-3">
                                            <h5 class="modal-title fw-bold text-dark fs-5" id="editItemModalLabel{{ $item['id'] }}">
                                                <i class="ti ti-edit text-warning me-1"></i> Edit Item
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <form action="/kepala-lab/pengadaan/update-item/{{ $item['id'] }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="tipe_barang" id="editItemTypeHidden{{ $item['id'] }}" value="{{ $item['tipe_barang'] }}">

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">Nama Barang</label>
                                                    <input class="form-control" type="text" name="nama_barang" value="{{ $item['nama_barang'] }}" placeholder="cth. Monitor Dell 24 inch" required>
                                                </div>

                                                @php
                                                    $predefinedCategories = ['Keyboard', 'Mouse', 'Monitor', 'PC / Komputer', 'Laptop', 'Printer', 'Alat Jaringan', 'Kabel', 'Kursi', 'Meja', 'Komponen PC'];
                                                    $isManualCategory = !in_array($item['kategori'] ?? '', $predefinedCategories);
                                                @endphp

                                                <div class="mb-3 row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold text-secondary">Kategori</label>
                                                        <select class="form-select" name="kategori" id="editCategorySelect{{ $item['id'] }}" required>
                                                            <option value="" disabled>-- Pilih Kategori --</option>
                                                            @foreach ($predefinedCategories as $catOption)
                                                                <option value="{{ $catOption }}" {{ ($item['kategori'] ?? '') === $catOption ? 'selected' : '' }}>{{ $catOption }}</option>
                                                            @endforeach
                                                            <option value="Lainnya" {{ $isManualCategory ? 'selected' : '' }}>Lainnya (Tulis Manual)</option>
                                                        </select>
                                                        <input type="text" class="form-control mt-2 {{ $isManualCategory ? '' : 'd-none' }}" name="kategori_manual" id="editCategoryManualInput{{ $item['id'] }}" value="{{ $isManualCategory ? ($item['kategori'] ?? '') : '' }}" placeholder="Tulis Kategori Baru" {{ $isManualCategory ? 'required' : '' }}>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold text-secondary">Jenis / Model</label>
                                                        <input class="form-control" type="text" name="jenis" value="{{ $item['jenis'] ?? '' }}" placeholder="cth. Mechanical, Wireless, dll." required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">Tipe Barang</label>
                                                    <select class="form-select" name="tipe_barang_select" id="editTipeBarangSelect{{ $item['id'] }}" required>
                                                        <option value="inventaris" {{ ($item['tipe_barang'] ?? '') === 'inventaris' ? 'selected' : '' }}>Inventaris (Barang Tetap)</option>
                                                        <option value="bhp" {{ ($item['tipe_barang'] ?? '') === 'bhp' ? 'selected' : '' }}>BHP (Barang Habis Pakai)</option>
                                                    </select>
                                                </div>

                                                @if (isset($item['inventaris_digantikan_id']) && $item['inventaris_digantikan_id'])
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-secondary">Aset Lama Yang Digantikan</label>
                                                        <select class="form-select" name="inventaris_digantikan_id">
                                                            <option value="">-- Tidak menggantikan aset --</option>
                                                            @foreach ($inventarisList ?? [] as $inv)
                                                                <option value="{{ $inv['id'] }}" {{ ($item['inventaris_digantikan_id'] ?? null) == $inv['id'] ? 'selected' : '' }}>
                                                                    [{{ $inv['nomor_label'] }}] {{ $inv['nama_barang'] }} - {{ $inv['nama_ruangan'] ?? 'Gudang Utama' }} ({{ strtoupper(str_replace('_', ' ', $inv['kondisi'] ?? '')) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif

                                                <!-- Price and Qty in one row -->
                                                <div class="row g-2 mb-3">
                                                    <div class="col-7">
                                                        <label class="form-label small fw-semibold text-secondary">Harga Satuan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted">Rp</span>
                                                            <input class="form-control" type="number" name="harga_satuan" value="{{ $item['harga_satuan'] }}" placeholder="250000" min="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-5">
                                                        <label class="form-label small fw-semibold text-secondary">Jumlah (Qty)</label>
                                                        <input class="form-control text-center {{ isset($item['inventaris_digantikan_id']) && $item['inventaris_digantikan_id'] ? 'bg-light' : '' }}" type="number" name="jumlah" value="{{ $item['jumlah'] }}" placeholder="5" min="1" {{ isset($item['inventaris_digantikan_id']) && $item['inventaris_digantikan_id'] ? 'readonly' : '' }} required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">Rasionalisasi Item</label>
                                                    <textarea class="form-control" name="rasionalisasi" rows="2" placeholder="Mengapa barang ini dibutuhkan..." required>{{ $item['rasionalisasi'] }}</textarea>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label small fw-semibold text-secondary">Link Pembelian (Opsional)</label>
                                                    <input class="form-control" type="url" name="link_pembelian" value="{{ $item['link_pembelian'] }}" placeholder="https://tokopedia.com/...">
                                                </div>

                                                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                                                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-warning px-4 fw-semibold text-white" type="submit">
                                                        <i class="ti ti-device-floppy me-1"></i> Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const editTipeSelect{{ $item['id'] }} = document.getElementById('editTipeBarangSelect{{ $item['id'] }}');
                                    const editTypeHiddenInput{{ $item['id'] }} = document.getElementById('editItemTypeHidden{{ $item['id'] }}');
                                    const editCategorySelect{{ $item['id'] }} = document.getElementById('editCategorySelect{{ $item['id'] }}');
                                    const editCategoryManualInput{{ $item['id'] }} = document.getElementById('editCategoryManualInput{{ $item['id'] }}');

                                    if (editTipeSelect{{ $item['id'] }} && editTypeHiddenInput{{ $item['id'] }}) {
                                        editTipeSelect{{ $item['id'] }}.addEventListener('change', function() {
                                            editTypeHiddenInput{{ $item['id'] }}.value = this.value;
                                        });
                                    }

                                    if (editCategorySelect{{ $item['id'] }} && editCategoryManualInput{{ $item['id'] }}) {
                                        editCategorySelect{{ $item['id'] }}.addEventListener('change', function() {
                                            if (this.value === 'Lainnya') {
                                                editCategoryManualInput{{ $item['id'] }}.classList.remove('d-none');
                                                editCategoryManualInput{{ $item['id'] }}.required = true;
                                                editCategoryManualInput{{ $item['id'] }}.focus();
                                            } else {
                                                editCategoryManualInput{{ $item['id'] }}.classList.add('d-none');
                                                editCategoryManualInput{{ $item['id'] }}.required = false;
                                                editCategoryManualInput{{ $item['id'] }}.value = '';
                                            }
                                        });
                                    }
                                });
                            </script>
                        @endforeach
                    @endif

                    <!-- SUBMISSION CARD (Directly below the items review table) -->
                    <div class="card shadow-sm border-0 mt-4 bg-white">
                        <div
                            class="card-body p-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="text-start">
                                <h5 class="mb-1 fw-bold text-dark">Finalisasi & Kirim Pengajuan</h5>
                                <p class="text-muted small mb-0">Pastikan semua item barang dan estimasi anggaran sudah
                                    benar. Draf akan dikunci setelah dikirim.</p>
                            </div>
                            <button class="btn btn-success px-4 py-2.5 fw-bold" data-bs-toggle="modal" data-bs-target="#submitDraftModal">
                                <i class="ti ti-send me-1"></i> Kirim ke Kaprodi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ADD ITEM MODAL -->
    @if ($hasActiveDraft)
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel"
            aria-hidden="true">
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
                            <input type="hidden" name="tipe_barang" id="addItemTypeHidden" value="inventaris">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nama Barang</label>
                                <input class="form-control" type="text" name="nama_barang"
                                    placeholder="cth. Monitor Dell 24 inch" required>
                            </div>

                            <div class="mb-3 row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-secondary">Kategori</label>
                                    <select class="form-select" name="kategori" id="categorySelect" required>
                                        <option value="" disabled selected>-- Pilih Kategori --</option>
                                        <option value="Keyboard">Keyboard</option>
                                        <option value="Mouse">Mouse</option>
                                        <option value="Monitor">Monitor</option>
                                        <option value="PC / Komputer">PC / Komputer</option>
                                        <option value="Laptop">Laptop</option>
                                        <option value="Printer">Printer</option>
                                        <option value="Alat Jaringan">Alat Jaringan</option>
                                        <option value="Kabel">Kabel / Konektor</option>
                                        <option value="Kursi">Kursi</option>
                                        <option value="Meja">Meja</option>
                                        <option value="Komponen PC">Komponen PC</option>
                                        <option value="Lainnya">Lainnya (Tulis Manual)</option>
                                    </select>
                                    <input type="text" class="form-control mt-2 d-none" name="kategori_manual" id="categoryManualInput" placeholder="Tulis Kategori Baru">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-secondary">Jenis / Model</label>
                                    <input class="form-control" type="text" name="jenis" placeholder="cth. Mechanical, Wireless, dll." required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Tipe Barang</label>
                                <select class="form-select" name="tipe_barang_select" id="tipeBarangSelect" required>
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
                                        <input class="form-control" type="number" name="harga_satuan"
                                            placeholder="250000" min="0" required>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-secondary">Jumlah (Qty)</label>
                                    <input class="form-control text-center" type="number" name="jumlah"
                                        placeholder="5" min="1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Rasionalisasi Item</label>
                                <textarea class="form-control" name="rasionalisasi" rows="2" placeholder="Mengapa barang ini dibutuhkan..."
                                    required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Link Pembelian
                                    (Opsional)</label>
                                <input class="form-control" type="url" name="link_pembelian"
                                    placeholder="https://tokopedia.com/...">
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

        <!-- REPLACE ASSET MODAL -->
        <div class="modal fade" id="replaceAssetModal" tabindex="-1" aria-labelledby="replaceAssetModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header bg-light border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark fs-5" id="replaceAssetModalLabel">
                            <i class="ti ti-replace text-primary me-1"></i> Ganti Aset Lama
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="/kepala-lab/pengadaan/add-item" method="POST">
                            @csrf
                            <input type="hidden" name="draft_id" value="{{ $activeDraft['id'] }}">
                            <input type="hidden" name="tipe_barang" value="inventaris">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Pilih Kategori Aset</label>
                                <select class="form-select" id="replaceCategoryFilter" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <option value="Keyboard">Keyboard</option>
                                    <option value="Mouse">Mouse</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="PC / Komputer">PC / Komputer</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Printer">Printer</option>
                                    <option value="Alat Jaringan">Alat Jaringan</option>
                                    <option value="Kabel">Kabel / Konektor</option>
                                    <option value="Kursi">Kursi</option>
                                    <option value="Meja">Meja</option>
                                    <option value="Komponen PC">Komponen PC</option>
                                    <option value="Lainnya">Lainnya (Tulis Manual)</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none" id="replaceCategoryFilterManual" placeholder="Tulis Kategori Baru">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Pilih Aset Lama Yang Digantikan</label>
                                <select class="form-select" name="inventaris_digantikan_id" id="replaceAssetSelect" disabled required>
                                    <option value="" disabled selected>-- Pilih Kategori Terlebih Dahulu --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nama Barang Baru</label>
                                <input class="form-control" type="text" name="nama_barang" id="replaceAssetNameInput"
                                    placeholder="Nama barang baru penganti" required>
                            </div>

                            <div class="mb-3 row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-secondary">Kategori Sebenarnya (Terkirim)</label>
                                    <input type="text" class="form-control bg-light" name="kategori" id="replaceCategoryFinalInput" readonly required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-secondary">Jenis / Model Baru</label>
                                    <input class="form-control" type="text" name="jenis" id="replaceAssetJenisInput" placeholder="cth. Mechanical, Wireless, dll." required>
                                </div>
                            </div>

                            <!-- Price and Qty in one row -->
                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="form-label small fw-semibold text-secondary">Harga Satuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">Rp</span>
                                        <input class="form-control" type="number" name="harga_satuan"
                                            placeholder="250000" min="0" required>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-secondary">Jumlah (Qty)</label>
                                    <input class="form-control text-center bg-light" type="number" name="jumlah" value="1" min="1" readonly required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Rasionalisasi Item</label>
                                <textarea class="form-control" name="rasionalisasi" rows="2" placeholder="Mengapa barang ini perlu diganti..."
                                    required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Link Pembelian (Opsional)</label>
                                <input class="form-control" type="url" name="link_pembelian"
                                    placeholder="https://tokopedia.com/...">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- ADD ITEM MODAL LOGIC ---
            const tipeSelect = document.getElementById('tipeBarangSelect');
            const typeHiddenInput = document.getElementById('addItemTypeHidden');
            const categorySelect = document.getElementById('categorySelect');
            const categoryManualInput = document.getElementById('categoryManualInput');

            if (tipeSelect && typeHiddenInput) {
                tipeSelect.addEventListener('change', function() {
                    typeHiddenInput.value = this.value;
                });
            }

            if (categorySelect && categoryManualInput) {
                categorySelect.addEventListener('change', function() {
                    if (this.value === 'Lainnya') {
                        categoryManualInput.classList.remove('d-none');
                        categoryManualInput.required = true;
                        categoryManualInput.focus();
                    } else {
                        categoryManualInput.classList.add('d-none');
                        categoryManualInput.required = false;
                        categoryManualInput.value = '';
                    }
                });
            }

            // --- REPLACE ASSET MODAL LOGIC (Category-First Filter) ---
            const allAssets = {!! json_encode($inventarisList ?? []) !!};
            const replaceCategoryFilter = document.getElementById('replaceCategoryFilter');
            const replaceCategoryFilterManual = document.getElementById('replaceCategoryFilterManual');
            const replaceAssetSelect = document.getElementById('replaceAssetSelect');
            const replaceCategoryFinalInput = document.getElementById('replaceCategoryFinalInput');
            const replaceAssetNameInput = document.getElementById('replaceAssetNameInput');
            const replaceAssetJenisInput = document.getElementById('replaceAssetJenisInput');

            function getSelectedCategory() {
                if (!replaceCategoryFilter) return '';
                if (replaceCategoryFilter.value === 'Lainnya') {
                    return replaceCategoryFilterManual ? replaceCategoryFilterManual.value : 'Lainnya';
                }
                return replaceCategoryFilter.value;
            }

            function updateCategoryFinal() {
                if (replaceCategoryFinalInput) {
                    replaceCategoryFinalInput.value = getSelectedCategory();
                }
            }

            if (replaceCategoryFilter) {
                replaceCategoryFilter.addEventListener('change', function() {
                    const val = this.value;
                    if (val === 'Lainnya') {
                        if (replaceCategoryFilterManual) {
                            replaceCategoryFilterManual.classList.remove('d-none');
                            replaceCategoryFilterManual.required = true;
                            replaceCategoryFilterManual.value = '';
                            replaceCategoryFilterManual.focus();
                        }
                    } else {
                        if (replaceCategoryFilterManual) {
                            replaceCategoryFilterManual.classList.add('d-none');
                            replaceCategoryFilterManual.required = false;
                            replaceCategoryFilterManual.value = '';
                        }
                    }
                    updateCategoryFinal();
                    filterAssets();
                });
            }

            if (replaceCategoryFilterManual) {
                replaceCategoryFilterManual.addEventListener('input', function() {
                    updateCategoryFinal();
                    filterAssets();
                });
            }

            function filterAssets() {
                if (!replaceAssetSelect) return;
                const cat = getSelectedCategory().trim().toLowerCase();

                // Clear current options
                replaceAssetSelect.innerHTML = '';

                if (cat === '') {
                    replaceAssetSelect.disabled = true;
                    replaceAssetSelect.innerHTML = '<option value="" disabled selected>-- Pilih Kategori Terlebih Dahulu --</option>';
                    return;
                }

                // Filter assets by category
                const filtered = allAssets.filter(item => {
                    const itemCat = (item.kategori || '').trim().toLowerCase();
                    return itemCat === cat;
                });

                if (filtered.length === 0) {
                    replaceAssetSelect.disabled = true;
                    replaceAssetSelect.innerHTML = '<option value="" disabled selected>-- Tidak ada aset lama dalam kategori ini --</option>';
                } else {
                    replaceAssetSelect.disabled = false;
                    let html = '<option value="" disabled selected>-- Pilih Aset Lama --</option>';
                    filtered.forEach(item => {
                        const room = item.nama_ruangan || 'Gudang Utama';
                        const kondisi = item.kondisi.replace('_', ' ').toUpperCase();
                        html += `<option value="${item.id}" data-nama="${item.nama_barang}" data-jenis="${item.jenis}">` +
                                `[${item.nomor_label}] ${item.nama_barang} - ${room} (${kondisi})` +
                                `</option>`;
                    });
                    replaceAssetSelect.innerHTML = html;
                }

                // Clear downstream fields
                if (replaceAssetNameInput) replaceAssetNameInput.value = '';
                if (replaceAssetJenisInput) replaceAssetJenisInput.value = '';
            }

            if (replaceAssetSelect) {
                replaceAssetSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    if (!opt || opt.value === "") return;

                    const nama = opt.getAttribute('data-nama');
                    const jenis = opt.getAttribute('data-jenis');

                    if (replaceAssetNameInput) replaceAssetNameInput.value = nama || '';
                    if (replaceAssetJenisInput) replaceAssetJenisInput.value = jenis || '';
                });
            }
        });
    </script>

    <!-- SUBMIT DRAFT MODAL -->
    @if ($hasActiveDraft)
        <div class="modal fade" id="submitDraftModal" tabindex="-1" aria-labelledby="submitDraftModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header bg-light border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark fs-5" id="submitDraftModalLabel">
                            <i class="ti ti-send text-success me-1"></i> Konfirmasi Pengiriman Draf
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="icon-shape icon-xl bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-send fs-2 text-success"></i>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark">Kirim Draf Pengadaan?</h5>
                            <p class="text-muted small px-3">Draf akan dikirim ke Ketua Program Studi untuk ditinjau. Setelah dikirim, draf akan **dikunci** dan tidak dapat diubah kembali.</p>
                        </div>
                        
                        <!-- Draft Summary Card -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-3">Ringkasan Draf #{{ $activeDraft['id'] }}</h6>
                                <div class="row g-2 small">
                                    <div class="col-6 text-muted">Tahun Anggaran:</div>
                                    <div class="col-6 fw-semibold text-dark text-end">{{ $activeDraft['tahun'] }}</div>
                                    
                                    <div class="col-6 text-muted">Jumlah Item:</div>
                                    <div class="col-6 fw-semibold text-dark text-end">{{ $itemCount }} Barang</div>
                                    
                                    <div class="col-6 text-muted">Total Estimasi:</div>
                                    <div class="col-6 fw-bold text-primary text-end">Rp {{ number_format($totalDraftPrice, 0, ',', '.') }}</div>
                                    
                                    <div class="col-6 text-muted border-top pt-2 mt-2">Penerima Review:</div>
                                    <div class="col-6 fw-semibold text-dark text-end border-top pt-2 mt-2">
                                        {{ $hasKaprodiList && isset($kaprodiList[0]) ? $kaprodiList[0]['nama'] : 'Ketua Program Studi' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="/kepala-lab/pengadaan/submit/{{ $activeDraft['id'] }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2 justify-content-end border-top pt-3">
                                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Batal</button>
                                <button class="btn btn-success px-4 fw-semibold" type="submit">
                                    <i class="ti ti-send me-1"></i> Kirim Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
