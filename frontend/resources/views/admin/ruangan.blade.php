@extends('layouts.app')

@section('content')
  <div class="container-fluid px-4 py-4">
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h1 class="h3 fw-bold text-dark mb-1">Administrator - Kelola Ruangan</h1>
              <p class="text-muted mb-0 small">Kelola pendaftaran, kode ruangan, dan lokasi fisik laboratorium dalam sistem.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addRoomForm">
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
              <input class="form-control form-control-lg" type="text" name="kode_ruangan" placeholder="cth. R-304" required>
            </div>
            <div class="col-md-4 col-12">
              <label class="form-label fw-semibold text-secondary small">Nama Ruangan</label>
              <input class="form-control form-control-lg" type="text" name="nama_ruangan" placeholder="cth. Lab Jaringan Komputer" required>
            </div>
            <div class="col-md-4 col-12">
              <label class="form-label fw-semibold text-secondary small">Lokasi / Gedung</label>
              <input class="form-control form-control-lg" type="text" name="lokasi" placeholder="cth. Gedung IT Lantai 3" required>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-end gap-2">
            <button class="btn btn-light px-4" type="button" data-bs-toggle="collapse" data-bs-target="#addRoomForm">Batal</button>
            <button class="btn btn-primary px-4" type="submit">Simpan Ruangan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ROOMS TABLE -->
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold text-dark mb-0">Daftar Ruangan Aktif</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 fw-bold">{{ $r['kode_ruangan'] }}</span>
                      </td>
                      <td class="py-3 fw-semibold text-dark">{{ $r['nama_ruangan'] }}</td>
                      <td class="py-3 text-secondary">{{ $r['lokasi'] }}</td>
                      <td class="text-end px-4 py-3">
                        <div class="d-inline-flex gap-2">
                          <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 px-3" data-bs-toggle="modal" data-bs-target="#editRoomModal-{{ $r['id'] }}">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                          </button>
                          <form class="d-inline" action="/admin/ruangan/delete/{{ $r['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?');">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 px-3" type="submit">
                              <i class="ti ti-trash"></i>
                              <span>Hapus</span>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>

                    <!-- EDIT MODAL FOR THIS ROOM -->
                    <div class="modal fade" id="editRoomModal-{{ $r['id'] }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                          <div class="modal-header border-bottom bg-light">
                            <h5 class="modal-title fw-bold text-dark">
                              <i class="ti ti-edit text-primary me-2"></i>Edit Data Ruangan
                            </h5>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form action="/admin/ruangan/edit/{{ $r['id'] }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Kode Ruangan</label>
                                <input class="form-control form-control-lg" type="text" name="kode_ruangan" value="{{ $r['kode_ruangan'] }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Nama Ruangan</label>
                                <input class="form-control form-control-lg" type="text" name="nama_ruangan" value="{{ $r['nama_ruangan'] }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Lokasi / Gedung</label>
                                <input class="form-control form-control-lg" type="text" name="lokasi" value="{{ $r['lokasi'] }}" required>
                              </div>
                            </div>
                            <div class="modal-footer border-top bg-light">
                              <button class="btn btn-light px-4" type="button" data-bs-toggle="modal" data-bs-target="#editRoomModal-{{ $r['id'] }}">Batal</button>
                              <button class="btn btn-primary px-4" type="submit">Simpan Perubahan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
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
  </div>
@endsection
