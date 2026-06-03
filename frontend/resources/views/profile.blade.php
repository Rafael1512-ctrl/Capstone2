@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- HEADER -->
        <div class="row mb-5">
            <div class="col-12">
                <div>
                    <h1 class="fs-3 mb-1 fw-bold text-dark">Pengaturan Profil</h1>
                    <p class="text-muted mb-0">Kelola informasi pribadi Anda dan perbarui kata sandi akun.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- PROFILE CARD (LEFT COLUMN) -->
            <div class="col-lg-4 col-12">
                <div class="card border-0 shadow-sm text-center py-5 px-4 h-100">
                    <div class="position-relative d-inline-block mx-auto mb-4">
                        <div class="avatar-container" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);">
                            <span class="text-white fw-bold fs-2">{{ strtoupper(substr($user['nama'] ?? 'G', 0, 2)) }}</span>
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle" style="width: 15px; height: 15px;" title="Online"></span>
                    </div>

                    <h4 class="fw-bold mb-1 text-dark">{{ $user['nama'] }}</h4>
                    <p class="text-muted small mb-3">{{ $user['email'] }}</p>

                    <div class="mb-4">
                        <span class="badge bg-primary text-uppercase px-3 py-2 fs-8 fw-bold">
                            <i class="ti ti-shield-check me-1"></i>
                            {{ str_replace('_', ' ', $user['role']) }}
                        </span>
                    </div>

                    <hr class="my-4 border-slate-100">

                    <!-- Info list -->
                    <div class="text-start small px-3 text-secondary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Status Sesi:</span>
                            <span class="fw-bold text-success">Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Metode Autentikasi:</span>
                            <span class="fw-bold text-dark">JWT Token</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFILE EDIT FORM (RIGHT COLUMN) -->
            <div class="col-lg-8 col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent px-4 py-3.5 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">Detail Informasi Pribadi</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" data-confirm="Apakah Anda yakin ingin menyimpan perubahan profil Anda?">
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input class="form-control" type="text" name="nama" value="{{ old('nama', $user['nama']) }}" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Alamat Email</label>
                                    <input class="form-control" type="email" name="email" value="{{ old('email', $user['email']) }}" required>
                                </div>
                            </div>

                            <hr class="my-4 border-slate-100">
                            
                            <h6 class="fw-bold text-dark mb-3">Keamanan & Ubah Password (Kosongkan jika tidak ingin diubah)</h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Password Baru</label>
                                    <input class="form-control" type="password" name="password" placeholder="Minimal 6 karakter">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input class="form-control" type="password" name="password_confirmation" placeholder="Ulangi password baru">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                <a href="/" class="btn btn-outline-secondary px-4">Batal</a>
                                <button class="btn btn-primary px-4 fw-bold" type="submit">
                                    <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
