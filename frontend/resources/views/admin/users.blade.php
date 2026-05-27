@extends('layouts.app')

@section('content')
  <div class="container-fluid px-4 py-4">
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h1 class="h3 fw-bold text-dark mb-1">Administrator - Kelola Pengguna</h1>
              <p class="text-muted mb-0 small">Kelola akun pengguna sistem, tetapkan peran (role), dan ubah kredensial akses.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addUserForm">
              <i class="ti ti-user-plus fs-5"></i>
              <span class="fw-semibold">Tambah Pengguna</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- COLLAPSIBLE FORM -->
    <div id="addUserForm" class="collapse mb-4">
      <div class="card border-0 shadow-sm p-4 rounded-3">
        <div class="border-bottom pb-3 mb-4">
          <h5 class="fw-bold text-dark mb-0">
            <i class="ti ti-user-plus text-primary me-2 fs-4"></i>Tambah Pengguna Baru
          </h5>
        </div>
        <form action="/admin/users/create" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-3 col-12">
              <label class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
              <input class="form-control form-control-lg" type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="col-md-3 col-12">
              <label class="form-label fw-semibold text-secondary small">Email</label>
              <input class="form-control form-control-lg" type="email" name="email" placeholder="contoh@mail.com" required>
            </div>
            <div class="col-md-3 col-12">
              <label class="form-label fw-semibold text-secondary small">Password</label>
              <input class="form-control form-control-lg" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="col-md-3 col-12">
              <label class="form-label fw-semibold text-secondary small">Peran (Role)</label>
              <select class="form-select form-select-lg" name="role_id" required>
                @foreach ($roles as $r)
                  <option value="{{ $r['id'] }}">{{ strtoupper(str_replace('_', ' ', $r['nama'])) }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-end gap-2">
            <button class="btn btn-light px-4" type="button" data-bs-toggle="collapse" data-bs-target="#addUserForm">Batal</button>
            <button class="btn btn-primary px-4" type="submit">Simpan Pengguna</button>
          </div>
        </form>
      </div>
    </div>

    <!-- USERS TABLE -->
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold text-dark mb-0">Daftar Pengguna Aktif</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light text-secondary small text-uppercase fw-semibold">
                <tr>
                  <th class="px-4 py-3" style="width: 80px;">ID</th>
                  <th class="py-3">Nama Lengkap</th>
                  <th class="py-3">Email</th>
                  <th class="py-3">Peran (Role)</th>
                  <th class="text-end px-4 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody class="small">
                @if ($hasUsers)
                  @foreach ($users as $u)
                    <tr>
                      <td class="px-4 py-3 text-secondary">{{ $u['id'] }}</td>
                      <td class="py-3 fw-semibold text-dark">{{ $u['nama'] }}</td>
                      <td class="py-3 text-secondary">{{ $u['email'] }}</td>
                      <td class="py-3">
                        @php
                          $badgeClass = $u['role'] === 'admin' ? 'bg-primary bg-opacity-10 text-primary' : ($u['role'] === 'kepala_lab' ? 'bg-success bg-opacity-10 text-success' : ($u['role'] === 'ketua_prodi' ? 'bg-warning bg-opacity-10 text-warning' : ($u['role'] === 'staf_admin' ? 'bg-info bg-opacity-10 text-info' : 'bg-secondary bg-opacity-10 text-secondary')));
                        @endphp
                        <span class="badge px-3 py-2 rounded-pill {{ $badgeClass }}">
                          {{ strtoupper(str_replace('_', ' ', $u['role'])) }}
                        </span>
                      </td>
                      <td class="text-end px-4 py-3">
                        <div class="d-inline-flex gap-2">
                          <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 px-3" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $u['id'] }}">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                          </button>
                          @if ($u['id'] !== Session::get('user')['id'])
                            <form class="d-inline" action="/admin/users/delete/{{ $u['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                              @csrf
                              <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 px-3" type="submit">
                                <i class="ti ti-trash"></i>
                                <span>Hapus</span>
                              </button>
                            </form>
                          @else
                            <span class="small text-muted px-2">(Akun Anda)</span>
                          @endif
                        </div>
                      </td>
                    </tr>

                    <!-- EDIT MODAL FOR THIS USER -->
                    <div class="modal fade" id="editUserModal-{{ $u['id'] }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                          <div class="modal-header border-bottom bg-light">
                            <h5 class="modal-title fw-bold text-dark">
                              <i class="ti ti-edit text-primary me-2"></i>Edit Data Pengguna
                            </h5>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form action="/admin/users/edit/{{ $u['id'] }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
                                <input class="form-control form-control-lg" type="text" name="nama" value="{{ $u['nama'] }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Email</label>
                                <input class="form-control form-control-lg" type="email" name="email" value="{{ $u['email'] }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                                <input class="form-control form-control-lg" type="password" name="password" placeholder="Password baru (opsional)">
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Peran (Role)</label>
                                <select class="form-select form-select-lg" name="role_id" required>
                                  @foreach ($roles as $r)
                                    <option value="{{ $r['id'] }}" {{ $r['id'] === $u['role_id'] ? 'selected' : '' }}>
                                      {{ strtoupper(str_replace('_', ' ', $r['nama'])) }}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer border-top bg-light">
                              <button class="btn btn-light px-4" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $u['id'] }}">Batal</button>
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
                      <i class="ti ti-user-off fs-1 d-block mb-2"></i>Tidak ada data pengguna.
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
