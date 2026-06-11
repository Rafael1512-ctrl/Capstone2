@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h1 class="h3 fw-bold text-dark mb-1">Administrator - Kelola Ruangan</h1>
                            <p class="text-muted mb-0 small">Kelola pendaftaran, kode ruangan, dan lokasi fisik laboratorium
                                dalam sistem.</p>
                        </div>
                        <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm"
                            data-bs-toggle="collapse" data-bs-target="#addRoomForm">
                            <i class="ti ti-plus fs-5"></i>
                            <span class="fw-semibold">Tambah Ruangan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLLAPSIBLE FORM -->
        <div id="addRoomForm" class="collapse mb-4">
            <div class="card border-0 shadow-sm p-4 rounded-3">
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="ti ti-layout-grid-add text-primary me-2 fs-4"></i>Tambah Ruangan Baru
                    </h5>
                </div>
                <form action="/admin/ruangan/create" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label fw-semibold text-secondary small">Kode Ruangan</label>
                            <input class="form-control form-control-lg" type="text" name="kode_ruangan"
                                placeholder="cth. R-304" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label fw-semibold text-secondary small">Nama Ruangan</label>
                            <input class="form-control form-control-lg" type="text" name="nama_ruangan"
                                placeholder="cth. Lab Jaringan Komputer" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label fw-semibold text-secondary small">Lokasi / Gedung</label>
                            <input class="form-control form-control-lg" type="text" name="lokasi"
                                placeholder="cth. Gedung IT Lantai 3" required>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button class="btn btn-light px-4" type="button" data-bs-toggle="collapse"
                            data-bs-target="#addRoomForm">Batal</button>
                        <button class="btn btn-primary px-4" type="submit">Simpan Ruangan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ROOMS TABLE -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div
                        class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <h5 class="fw-bold text-dark mb-0">Daftar Ruangan Aktif</h5>
                        <!-- Live Filter Elements -->
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="position-relative">
                                <input type="text" id="searchRoom" class="form-control form-control-sm ps-4"
                                    placeholder="Cari kode atau nama..." style="width: 220px;">
                                <i
                                    class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary"></i>
                            </div>
                            <select id="filterLocation" class="form-select form-select-sm" style="width: 180px;">
                                <option value="">Semua Lokasi / Gedung</option>
                                <!-- Locations will be populated dynamically via JavaScript -->
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="roomsTable">
                            <thead class="table-light text-secondary small text-uppercase fw-semibold">
                                <tr>
                                    <th class="px-4 py-3" style="width: 80px;">ID</th>
                                    <th class="py-3">Kode Ruangan</th>
                                    <th class="py-3">Nama Ruangan</th>
                                    <th class="py-3">Lokasi / Gedung</th>
                                    <th class="text-end px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @if (isset($ruangan) && count($ruangan) > 0)
                                    @foreach ($ruangan as $r)
                                        <tr>
                                            <td class="px-4 py-3 text-secondary">{{ $r['id'] }}</td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 fw-bold">{{ $r['kode_ruangan'] }}</span>
                                            </td>
                                            <td class="py-3 fw-semibold text-dark">{{ $r['nama_ruangan'] }}</td>
                                            <td class="py-3 text-secondary">{{ $r['lokasi'] }}</td>
                                            <td class="text-end px-4 py-3">
                                                <div class="d-inline-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editRoomModal-{{ $r['id'] }}">
                                                        <i class="ti ti-edit"></i>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button
                                                        class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteRoomModal-{{ $r['id'] }}">
                                                        <i class="ti ti-trash"></i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center py-5 text-muted" colspan="5">
                                            <i class="ti ti-package-off fs-1 d-block mb-2"></i>Belum ada ruangan terdaftar.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT MODALS (OUTSIDE TABLE FOR VALID HTML & PREVENT FLICKERING) -->
        @if (isset($ruangan) && count($ruangan) > 0)
            @foreach ($ruangan as $r)
                <div class="modal fade" id="editRoomModal-{{ $r['id'] }}" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-bottom bg-light">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="ti ti-edit text-primary me-2"></i>Edit Data Ruangan
                                </h5>
                                <button class="btn-close" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="/admin/ruangan/edit/{{ $r['id'] }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label
                                            class="form-label fw-semibold text-secondary small">Kode
                                            Ruangan</label>
                                        <input class="form-control form-control-lg" type="text"
                                            name="kode_ruangan" value="{{ $r['kode_ruangan'] }}"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label
                                            class="form-label fw-semibold text-secondary small">Nama
                                            Ruangan</label>
                                        <input class="form-control form-control-lg" type="text"
                                            name="nama_ruangan" value="{{ $r['nama_ruangan'] }}"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label
                                            class="form-label fw-semibold text-secondary small">Lokasi
                                            / Gedung</label>
                                        <input class="form-control form-control-lg" type="text"
                                            name="lokasi" value="{{ $r['lokasi'] }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer border-top bg-light">
                                    <button class="btn btn-light px-4" type="button"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary px-4" type="submit">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- DELETE ROOM MODAL -->
                <div class="modal fade" id="deleteRoomModal-{{ $r['id'] }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-bottom bg-light">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="ti ti-alert-triangle text-danger me-2"></i>Konfirmasi Hapus
                                </h5>
                                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="mb-0 text-dark">Apakah Anda yakin ingin menghapus ruangan <strong>{{ $r['nama_ruangan'] }}</strong> ({{ $r['kode_ruangan'] }})?<br><span class="text-danger small mt-2 d-block">*Tindakan ini akan menghapus data secara permanen dari database.</span></p>
                            </div>
                            <div class="modal-footer border-top bg-light">
                                <button class="btn btn-light px-4" type="button" data-bs-dismiss="modal">Batal</button>
                                <form action="/admin/ruangan/delete/{{ $r['id'] }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger px-4" type="submit">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchRoom');
            const filterLocation = document.getElementById('filterLocation');
            const tableRows = document.querySelectorAll('#roomsTable tbody tr');

            // Dynamically extract unique locations to populate dropdown
            const locations = new Set();
            tableRows.forEach(row => {
                if (row.cells.length === 1) return; // Skip empty row
                const loc = row.cells[3].textContent.trim();
                if (loc) locations.add(loc);
            });

            // Populate dropdown list
            locations.forEach(loc => {
                const option = document.createElement('option');
                option.value = loc;
                option.textContent = loc;
                filterLocation.appendChild(option);
            });

            function filterTable() {
                const query = searchInput.value.toLowerCase().trim();
                const selectedLoc = filterLocation.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    if (row.cells.length === 1) return;

                    const code = row.cells[1].textContent.toLowerCase();
                    const name = row.cells[2].textContent.toLowerCase();
                    const loc = row.cells[3].textContent.toLowerCase();

                    const matchesQuery = code.includes(query) || name.includes(query);
                    const matchesLoc = selectedLoc === "" || loc.includes(selectedLoc);

                    if (matchesQuery && matchesLoc) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterLocation.addEventListener('change', filterTable);
        });
    </script>
@endsection
