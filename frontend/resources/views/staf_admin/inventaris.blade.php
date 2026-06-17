@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    @if (Session::get('user')['role'] === 'staf_admin')
                        <h1 class="fs-3 mb-1">Staf Administrasi - Penerimaan & Labeling Inventaris</h1>
                        <p class="text-muted">Lakukan pencatatan penerimaan barang, penomoran label/kode inventaris, pemetaan
                            ruangan, dan generate QR Code untuk barang inventaris yang telah disetujui.</p>
                    @else
                        <h1 class="fs-3 mb-1">Daftar Inventaris</h1>
                        <p class="text-muted">Daftar seluruh barang inventaris laboratorium yang telah teregistrasi dan
                            terdaftar dalam sistem.</p>
                    @endif
                </div>
            </div>
        </div>

        @if (Session::get('user')['role'] === 'staf_admin')
            <!-- PENDING ITEMS SECTION -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header bg-warning bg-opacity-10 border-bottom px-4 py-3 border-warning border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0 text-warning-emphasis">
                                Menunggu Penerimaan & Penomoran Label (Barang Approved)
                                @if (isset($selectedDraftId) && $selectedDraftId)
                                    <span class="badge bg-warning text-dark ms-2">Draf Terpilih</span>
                                @endif
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <form action="/inventaris" method="GET" class="d-flex align-items-center gap-2 m-0">
                                    <label class="small text-muted mb-0 text-nowrap">Filter Draf:</label>
                                    <select name="draft_id" class="form-select form-select-sm" onchange="this.form.submit()"
                                        style="width: auto;">
                                        <option value="">-- Semua Draf --</option>
                                        @foreach ($drafts ?? [] as $d)
                                            <option value="{{ $d['id'] }}"
                                                {{ ($selectedDraftId ?? '') == $d['id'] ? 'selected' : '' }}>
                                                Draf ID: {{ $d['id'] }} - {{ $d['tahun'] }} ({{ $d['pengaju'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Barang (Pengadaan)</th>
                                        <th>Kategori</th>
                                        <th>Jenis</th>
                                        <th>Tahun</th>
                                        <th>Qty</th>
                                        <th>Form Labeling & Penerimaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($pendingItems) && count($pendingItems) > 0)
                                        @foreach ($pendingItems as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="fw-semibold">{{ $item['nama_barang'] }}</div>
                                                    <small class="text-muted">Diajukan oleh: {{ $item['pengaju'] }}</small>
                                                </td>
                                                <td><span
                                                        class="badge bg-light text-dark border px-2 py-1">{{ $item['kategori'] ?? 'Lainnya' }}</span>
                                                </td>
                                                <td>{{ $item['jenis'] ?? '-' }}</td>
                                                <td>{{ $item['tahun'] }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $item['jumlah'] }} unit</div>
                                                    <div class="small text-muted">Diterima:
                                                        {{ $item['received_count'] ?? 0 }} / {{ $item['jumlah'] }}</div>
                                                </td>
                                                <td>
                                                    <form class="row g-2 align-items-end py-2"
                                                        action="/staf-admin/inventaris/receive/{{ $item['id'] }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="col-md-3 col-12">
                                                            <label class="form-label small mb-1">Kode Inventaris /
                                                                Label</label>
                                                            <input class="form-control form-control-sm" type="text"
                                                                name="nomor_label" placeholder="cth. INV-LAB-01" required>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <label class="form-label small mb-1">Ruangan Penempatan</label>
                                                            <select class="form-select form-select-sm" name="ruangan_id"
                                                                required>
                                                                @foreach ($ruangan as $r)
                                                                    <option value="{{ $r['id'] }}">
                                                                        {{ $r['kode_ruangan'] }} -
                                                                        {{ $r['nama_ruangan'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <label class="form-label small mb-1">Tgl Penerimaan</label>
                                                            <input class="form-control form-control-sm" type="date"
                                                                name="tanggal_terima" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="col-md-3 col-12 d-grid">
                                                            <button class="btn btn-sm btn-primary" type="submit">
                                                                <i class="ti ti-check"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center py-4 text-muted" colspan="6">Tidak ada barang
                                                inventaris pending untuk dilabeli.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RECEIVED ITEMS SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">Inventaris Terdaftar & QR-Labeled</h5>
                        <form action="/inventaris" method="GET" class="d-flex align-items-center gap-2 m-0 flex-wrap">
                            @if (request()->query('draft_id'))
                                <input type="hidden" name="draft_id" value="{{ request()->query('draft_id') }}">
                            @endif
                            
                            <label class="small text-muted mb-0 text-nowrap">Filter Ruangan:</label>
                            <select name="ruangan_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; min-width: 150px;">
                                <option value="">-- Semua Ruangan --</option>
                                @foreach ($ruangan ?? [] as $r)
                                    <option value="{{ $r['id'] }}" {{ ($selectedRuanganId ?? '') == $r['id'] ? 'selected' : '' }}>
                                        {{ $r['kode_ruangan'] }} - {{ $r['nama_ruangan'] }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="small text-muted mb-0 text-nowrap ms-md-2">Filter Tahun:</label>
                            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                <option value="">-- Semua Tahun --</option>
                                @foreach ($availableYears ?? [] as $yr)
                                    <option value="{{ $yr }}" {{ ($selectedTahun ?? '') == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @if (request()->query('ruangan_id') || request()->query('tahun'))
                                <a href="/inventaris{{ request()->query('draft_id') ? '?draft_id=' . request()->query('draft_id') : '' }}" class="btn btn-sm btn-light border small ms-2">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th>Kode/Label</th>
                                    <th style="min-width: 250px;">Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal Terima</th>
                                    <th>Kondisi</th>
                                    @if (Session::get('user')['role'] === 'staf_admin')
                                        <th class="text-center px-4">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($receivedItems) && count($receivedItems) > 0)
                                    @foreach ($receivedItems as $inv)
                                        <tr>
                                            <td class="px-4 py-3">{{ $loop->remaining + 1 }}</td>
                                            <td><span class="badge bg-primary fs-7">{{ $inv['nomor_label'] }}</span></td>
                                            <td class="fw-semibold">{{ $inv['nama_barang'] }}</td>
                                            <td><span
                                                    class="badge bg-light text-dark border px-2 py-1">{{ $inv['kategori'] ?? 'Lainnya' }}</span>
                                            </td>
                                            <td>{{ $inv['jenis'] ?? '-' }}</td>
                                            <td>{{ $inv['nama_ruangan'] ?? 'Belum Ditempatkan' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($inv['tanggal_terima'])->translatedFormat('d M Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass =
                                                        $inv['kondisi'] === 'baik'
                                                            ? 'bg-success'
                                                            : ($inv['kondisi'] === 'rusak_ringan'
                                                                ? 'bg-warning text-dark'
                                                                : ($inv['kondisi'] === 'rusak_berat'
                                                                    ? 'bg-danger'
                                                                    : 'bg-secondary'));
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ str_replace('_', ' ', strtoupper($inv['kondisi'])) }}
                                                </span>
                                            </td>
                                            @if (Session::get('user')['role'] === 'staf_admin')
                                                <td class="text-end px-4">
                                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-info btn-icon"
                                                            data-item='@json($inv)'
                                                            onclick="openDetailModal(this)"
                                                            title="Detail Inventaris">
                                                            <i class="ti ti-eye"></i>
                                                        </button>

                                                        @if($inv['qr_univ_path'])
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btn-icon"
                                                            onclick="openUploadQrUnivModal({{ $inv['id'] }}, '{{ addslashes($inv['nomor_label'] ?? '') }}', '{{ addslashes($inv['qr_univ_path'] ?? '') }}')"
                                                            title="Edit Data Univ">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        @else
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-icon"
                                                            onclick="openUploadQrUnivModal({{ $inv['id'] }}, '{{ addslashes($inv['nomor_label'] ?? '') }}', '{{ addslashes($inv['qr_univ_path'] ?? '') }}')"
                                                            title="Unggah QR Univ">
                                                            <i class="ti ti-upload"></i>
                                                        </button>
                                                        @endif

                                                        <form action="/staf-admin/inventaris/delete/{{ $inv['id'] }}"
                                                            method="POST" class="m-0"
                                                            data-confirm="Apakah Anda yakin ingin menghapus barang inventaris ini (soft delete)?">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-light text-danger border btn-icon"
                                                                title="Soft Delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center py-4"
                                            colspan="{{ Session::get('user')['role'] === 'staf_admin' ? 9 : 8 }}">Belum
                                            ada barang inventaris yang dilabeli.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- University QR Upload Modal -->
    <div class="modal fade" id="uploadQrUnivModal" tabindex="-1" aria-labelledby="uploadQrUnivModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-light border-bottom px-4 py-3 d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark" id="uploadQrUnivModalLabel">Kelola QR & Data Universitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadQrUnivForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label small">Inventaris</label>
                            <input type="text" id="uploadQrUnivLabel" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="qr_univ_file" class="form-label small">File QR Universitas</label>
                            <input id="qr_univ_file" name="qr_univ_file" type="file" accept="image/png,image/jpeg,image/jpg" class="form-control form-control-sm">
                            <div class="form-text">Unggah file gambar QR code universitas (PNG / JPG). Biarkan kosong jika tidak ingin mengubah/mengunggah gambar baru.</div>
                        </div>

                        <div id="existingQrUnivInfo" class="small text-muted"></div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan QR Univ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Inventaris Modal -->
    <div class="modal fade" id="detailInventarisModal" tabindex="-1" aria-labelledby="detailInventarisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-light border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark" id="detailInventarisModalLabel">
                        <i class="ti ti-info-circle text-primary me-2"></i>Detail Barang Inventaris
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeDetailModal()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Info Column -->
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Informasi Barang</h6>
                            <table class="table table-borderless table-sm small">
                                <tr>
                                    <th width="35%" class="text-muted fw-normal">Nama Barang</th>
                                    <td width="5%" class="text-muted">:</td>
                                    <td class="fw-semibold text-dark" id="det_nama_barang"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kode / Label</th>
                                    <td class="text-muted">:</td>
                                    <td><span class="badge bg-primary fs-7" id="det_nomor_label"></span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kategori</th>
                                    <td class="text-muted">:</td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1" id="det_kategori"></span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Jenis</th>
                                    <td class="text-muted">:</td>
                                    <td id="det_jenis"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Ruangan</th>
                                    <td class="text-muted">:</td>
                                    <td id="det_ruangan" class="fw-medium"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Tanggal Terima</th>
                                    <td class="text-muted">:</td>
                                    <td id="det_tanggal_terima"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Kondisi</th>
                                    <td class="text-muted">:</td>
                                    <td><span class="badge" id="det_kondisi"></span></td>
                                </tr>

                            </table>
                        </div>
                        <!-- QR Code Column -->
                        <div class="col-md-5 border-start-md">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">QR Code & Labeling</h6>
                            <div class="d-flex flex-column gap-4 align-items-center">
                                <!-- QR Inventaris -->
                                <div class="text-center w-100 p-2 bg-light rounded border">
                                    <span class="d-block small fw-bold text-muted mb-2">QR INVENTARIS LAB</span>
                                    <div id="det_qr_inventaris_container" class="mb-2"></div>
                                    <span class="small font-monospace text-dark d-block fw-semibold" id="det_qr_inventaris_label"></span>
                                </div>
                                <!-- QR Universitas -->
                                <div class="text-center w-100 p-2 bg-light rounded border">
                                    <span class="d-block small fw-bold text-muted mb-2">QR UNIVERSITAS</span>
                                    <div id="det_qr_univ_container" class="mb-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="closeDetailModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUploadQrUnivModal(itemId, label, qrUnivPath) {
            const form = document.getElementById('uploadQrUnivForm');
            form.action = `/staf-admin/inventaris/upload-qr-univ/${itemId}`;
            document.getElementById('uploadQrUnivLabel').value = label;

            const existingInfo = document.getElementById('existingQrUnivInfo');
            if (qrUnivPath) {
                existingInfo.innerHTML = `QR Univ saat ini sudah terunggah. <a href="${qrUnivPath}" target="_blank">Lihat file</a>`;
            } else {
                existingInfo.innerHTML = 'Belum ada QR Universitas terunggah untuk inventaris ini.';
            }

            const modalEl = document.getElementById('uploadQrUnivModal');
            const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);

            if (bs && bs.Modal) {
                try {
                    const modal = bs.Modal.getOrCreateInstance(modalEl) || new bs.Modal(modalEl);
                    modal.show();
                } catch (e) {
                    console.error("Modal error, falling back to manual display:", e);
                    showManualModal(modalEl);
                }
            } else {
                showManualModal(modalEl);
            }
        }

        function showManualModal(modalEl) {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            
            // Add backdrop if it does not exist
            let backdrop = document.getElementById('uploadQrUnivModalBackdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'uploadQrUnivModalBackdrop';
                document.body.appendChild(backdrop);
            }
            document.body.classList.add('modal-open');
            
            // Register click handlers for dismiss buttons
            const closeBtns = modalEl.querySelectorAll('[data-bs-dismiss="modal"]');
            closeBtns.forEach(btn => {
                btn.onclick = function() {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    modalEl.setAttribute('aria-hidden', 'true');
                    modalEl.removeAttribute('aria-modal');
                    const bd = document.getElementById('uploadQrUnivModalBackdrop');
                    if (bd) {
                        bd.remove();
                    }
                    document.body.classList.remove('modal-open');
                };
            });
        }

        function openDetailModal(btn) {
            const item = JSON.parse(btn.getAttribute('data-item'));
            console.log("Detail item:", item);
            document.getElementById('det_nama_barang').textContent = item.nama_barang || '-';
            document.getElementById('det_nomor_label').textContent = item.nomor_label || '-';
            document.getElementById('det_kategori').textContent = (item.kategori || 'Lainnya').toUpperCase();
            document.getElementById('det_jenis').textContent = item.jenis || '-';
            document.getElementById('det_ruangan').textContent = item.nama_ruangan || 'Belum Ditempatkan';
            
            // Format date
            let tglTerima = item.tanggal_terima ? new Date(item.tanggal_terima).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            }) : '-';
            document.getElementById('det_tanggal_terima').textContent = tglTerima;
            
            // Condition badge class
            const kondisiEl = document.getElementById('det_kondisi');
            kondisiEl.textContent = (item.kondisi || 'baik').replace('_', ' ').toUpperCase();
            kondisiEl.className = 'badge';
            if (item.kondisi === 'baik') {
                kondisiEl.classList.add('bg-success');
            } else if (item.kondisi === 'rusak_ringan') {
                kondisiEl.classList.add('bg-warning', 'text-dark');
            } else if (item.kondisi === 'rusak_berat') {
                kondisiEl.classList.add('bg-danger');
            } else {
                kondisiEl.classList.add('bg-secondary');
            }

            // Univ info
            let isKodePath = item.kode_inventaris_univ && (item.kode_inventaris_univ.startsWith('/') || item.kode_inventaris_univ.includes('/'));

            // QR code local
            const qrLocalContainer = document.getElementById('det_qr_inventaris_container');
            const detailUrl = `${window.location.origin}/inventaris/detail/${item.id}`;
            qrLocalContainer.innerHTML = `
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(detailUrl)}" 
                     alt="QR Local" 
                     class="img-thumbnail" 
                     style="width: 120px; height: 120px;">
            `;
            document.getElementById('det_qr_inventaris_label').textContent = item.nomor_label || '';

            // QR Univ
            const qrUnivContainer = document.getElementById('det_qr_univ_container');
            let univImgPath = item.qr_univ_path || (isKodePath ? item.kode_inventaris_univ : null);
            if (univImgPath) {
                qrUnivContainer.innerHTML = `
                    <img src="${univImgPath}" 
                         alt="QR Universitas" 
                         class="img-thumbnail" 
                         style="width: 120px; height: 120px; object-fit: contain;">
                    <a href="${univImgPath}" target="_blank" class="d-block btn btn-link btn-sm mt-1 p-0 text-decoration-none">
                        <i class="ti ti-external-link"></i> Lihat Penuh
                    </a>
                `;
            } else {
                qrUnivContainer.innerHTML = `
                    <div class="text-muted py-4 small bg-white border rounded">
                        <i class="ti ti-qrcode fs-3 d-block mb-1 opacity-50"></i>
                        Belum diunggah
                    </div>
                `;
            }

            const modalEl = document.getElementById('detailInventarisModal');
            const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
            if (bs && bs.Modal) {
                try {
                    const modal = bs.Modal.getOrCreateInstance(modalEl) || new bs.Modal(modalEl);
                    modal.show();
                } catch (e) {
                    console.error("Modal error, falling back to manual display:", e);
                    showManualDetailModal(modalEl);
                }
            } else {
                showManualDetailModal(modalEl);
            }
        }

        function showManualDetailModal(modalEl) {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            
            // Add backdrop if it does not exist
            let backdrop = document.getElementById('detailModalBackdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'detailModalBackdrop';
                document.body.appendChild(backdrop);
            }
            document.body.classList.add('modal-open');
        }

        function closeDetailModal() {
            const modalEl = document.getElementById('detailInventarisModal');
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            const bd = document.getElementById('detailModalBackdrop');
            if (bd) {
                bd.remove();
            }
            document.body.classList.remove('modal-open');
        }
    </script>
@endsection
