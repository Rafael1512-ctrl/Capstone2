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
                    <div class="card-header bg-transparent px-4 py-3 border-bottom">
                        <h5 class="mb-0">Inventaris Terdaftar & QR-Labeled</h5>
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
                                    <th class="text-center">QR Code / Barcode</th>
                                    <th class="text-center">QR Univ</th>
                                    @if (Session::get('user')['role'] === 'staf_admin')
                                        <th class="text-end px-4">Aksi</th>
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
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center gap-3">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($inv['nomor_label']) }}"
                                                        alt="QR Code"
                                                        style="width: 40px; height: 40px; border: 1px solid #ddd; padding: 2px; border-radius: 4px;">
                                                    <span
                                                        class="small text-muted font-monospace">{{ $inv['nomor_label'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($inv['qr_univ_path'])
                                                    <span class="badge bg-success">Terunggah</span>
                                                @else
                                                    <span class="badge bg-secondary">Belum</span>
                                                @endif
                                            </td>
                                            @if (Session::get('user')['role'] === 'staf_admin')
                                                <td class="text-end px-4">
                                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                                        @if($inv['qr_univ_path'])
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btn-icon"
                                                            onclick="openUploadQrUnivModal({{ $inv['id'] }}, '{{ addslashes($inv['nomor_label'] ?? '') }}', '{{ addslashes($inv['kode_inventaris_univ'] ?? '') }}', '{{ addslashes($inv['tanggal_daftar_univ'] ?? '') }}', '{{ addslashes($inv['qr_univ_path'] ?? '') }}')"
                                                            title="Edit Data Univ">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        @else
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-icon"
                                                            onclick="openUploadQrUnivModal({{ $inv['id'] }}, '{{ addslashes($inv['nomor_label'] ?? '') }}', '{{ addslashes($inv['kode_inventaris_univ'] ?? '') }}', '{{ addslashes($inv['tanggal_daftar_univ'] ?? '') }}', '{{ addslashes($inv['qr_univ_path'] ?? '') }}')"
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
                                            colspan="{{ Session::get('user')['role'] === 'staf_admin' ? 11 : 10 }}">Belum
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
                        <div class="mb-3">
                            <label class="form-label small">Kode Inventaris Univ.</label>
                            <input id="kode_inventaris_univ" name="kode_inventaris_univ" type="text" class="form-control form-control-sm" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Tanggal Daftar Univ.</label>
                            <input id="tanggal_daftar_univ" name="tanggal_daftar_univ" type="date" class="form-control form-control-sm">
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

    <script>
        function openUploadQrUnivModal(itemId, label, kodeInventarisUniv, tanggalDaftarUniv, qrUnivPath) {
            const form = document.getElementById('uploadQrUnivForm');
            form.action = `/staf-admin/inventaris/upload-qr-univ/${itemId}`;
            document.getElementById('uploadQrUnivLabel').value = label;
            document.getElementById('kode_inventaris_univ').value = kodeInventarisUniv || '';
            document.getElementById('tanggal_daftar_univ').value = tanggalDaftarUniv || '';

            const existingInfo = document.getElementById('existingQrUnivInfo');
            if (qrUnivPath) {
                existingInfo.innerHTML = `QR Univ saat ini sudah terunggah. <a href="${qrUnivPath}" target="_blank">Lihat file</a>`;
            } else {
                existingInfo.innerHTML = 'Belum ada QR Universitas terunggah untuk inventaris ini.';
            }

            const modalEl = document.getElementById('uploadQrUnivModal');
            try {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            } catch (e) {
                console.error("Modal error:", e);
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    </script>
@endsection
