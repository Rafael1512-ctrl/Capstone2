@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- HEADER -->
        <div class="row mb-5">
            <div class="col-12">
                <div>
                    <h1 class="fs-3 mb-1 fw-bold text-dark">Profil Saya</h1>
                    <p class="text-muted mb-0">Kelola informasi profil pribadi Anda dan foto profil Anda.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- AVATAR UPLOAD SECTION (LEFT COLUMN) -->
            <div class="col-lg-4 col-12">
                <div class="card border-0 shadow-sm text-center py-5 px-4 h-100 bg-white" style="border-radius: 16px;">
                    <!-- Avatar Preview Area -->
                    <div class="position-relative d-inline-block mx-auto mb-4" style="width: 120px; height: 120px;">
                        <div class="avatar-wrapper d-flex align-items-center justify-content-center w-100 h-100">
                            <img id="profileAvatarImg" class="avatar avatar-xxl rounded-circle d-none border border-light shadow-sm" src="" alt="Avatar" style="width: 120px; height: 120px; object-fit: cover;">
                            <div id="profileAvatarInitials" class="avatar avatar-xxl rounded-circle d-flex align-items-center justify-content-center text-white fw-bold bg-success fs-1 shadow-sm" style="display: none !important; width: 120px; height: 120px; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
                                -
                            </div>
                        </div>
                        <!-- Online indicator -->
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white border-3 rounded-circle shadow-sm" style="width: 20px; height: 20px;" title="Online"></span>
                    </div>

                    <h4 id="profileNameDisplay" class="fw-bold mb-1 text-dark">-</h4>
                    <p id="profileEmailDisplay" class="text-secondary small mb-2">-</p>
                    <p id="profilePositionPlaceholder" class="text-muted small mb-3 text-capitalize">-</p>

                    <div class="mb-4">
                        <span id="profileRoleBadge" class="badge bg-light text-success border border-success-subtle text-uppercase px-3 py-2 fs-8 fw-semibold" style="border-radius: 30px;">
                            <i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Loading...
                        </span>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f9;">

                    <!-- Photo Upload Button -->
                    <div class="px-3">
                        <input type="file" id="avatarFileInput" accept="image/png, image/jpeg, image/webp" class="d-none">
                        <button type="button" class="btn btn-outline-success w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" id="btnUploadPhoto" style="border-radius: 10px; transition: all 0.2s;">
                            <i class="ti ti-camera fs-5"></i> Ganti Foto
                        </button>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">Format gambar JPG, PNG, atau WebP, maks. 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- PROFILE DETAIL FORM (RIGHT COLUMN) -->
            <div class="col-lg-8 col-12">
                <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 16px;">
                    <div class="card-header bg-transparent px-4 py-4 border-bottom" style="border-color: #f1f5f9;">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="ti ti-user-edit text-success fs-4"></i> Detail Informasi Pribadi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="profileEditForm">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-bold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-user"></i></span>
                                        <input class="form-control bg-light border-start-0" type="text" id="profileNameInput" required placeholder="Contoh: Ahmad Yusuf">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-bold text-secondary small">Alamat Email <span class="text-muted">(Read-Only)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-mail"></i></span>
                                        <input class="form-control bg-light border-start-0 text-muted" type="email" id="profileEmailInput" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-bold text-secondary small">No. Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-phone"></i></span>
                                        <input class="form-control bg-light border-start-0" type="text" id="profilePhoneInput" placeholder="Contoh: 081234567890" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-bold text-secondary small">Jabatan / Posisi <span class="text-muted">(Read-Only)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-briefcase"></i></span>
                                        <input class="form-control bg-light border-start-0 text-muted" type="text" id="profilePositionInput" readonly disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-4" style="border-color: #f1f5f9;">
                                <a href="/" class="btn btn-light px-4 py-2.5 fw-semibold" style="border-radius: 10px;">Batal</a>
                                <button class="btn btn-success px-4 py-2.5 fw-bold d-flex align-items-center gap-2" type="submit" id="btnSaveProfile" style="border-radius: 10px; background-color: #10b981; border-color: #10b981;">
                                    <i class="ti ti-device-floppy fs-5"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROFILE JS LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="jwt-token"]')?.getAttribute('content');
            const apiUrl = document.querySelector('meta[name="api-url"]')?.getAttribute('content') || 'http://localhost:3000/api';
            if (!token) return;

            const btnUploadPhoto = document.getElementById('btnUploadPhoto');
            const avatarFileInput = document.getElementById('avatarFileInput');
            const profileForm = document.getElementById('profileEditForm');
            const btnSaveProfile = document.getElementById('btnSaveProfile');

            function formatRoleName(role) {
                const roleLabels = {
                    'admin': 'Admin',
                    'kepala_lab': 'Kepala Lab',
                    'ketua_prodi': 'Ketua Prodi',
                    'staf_admin': 'Staf Admin',
                    'staf_lab': 'Staf Lab'
                };
                return roleLabels[role] || role.replace(/_/g, ' ');
            }

            function updateProfileAvatarUI(profile) {
                const avatarImg = document.getElementById('profileAvatarImg');
                const avatarInitials = document.getElementById('profileAvatarInitials');
                
                const initials = profile.name 
                    ? profile.name.trim().split(/\s+/).map(p => p[0]).slice(0, 2).join('').toUpperCase() 
                    : 'U';

                if (profile.avatar_url) {
                    if (avatarImg) {
                        avatarImg.src = profile.avatar_url;
                        avatarImg.classList.remove('d-none');
                    }
                    if (avatarInitials) {
                        avatarInitials.style.setProperty('display', 'none', 'important');
                    }
                } else {
                    if (avatarImg) {
                        avatarImg.classList.add('d-none');
                    }
                    if (avatarInitials) {
                        avatarInitials.textContent = initials;
                        avatarInitials.style.setProperty('display', 'flex', 'important');
                    }
                }
            }

            // Function to populate form fields
            function loadProfileForm() {
                if (typeof window.fetchUserProfile === 'function') {
                    window.fetchUserProfile().then(profile => {
                        if (profile) {
                            document.getElementById('profileNameInput').value = profile.name || '';
                            document.getElementById('profileEmailInput').value = profile.email || '';
                            document.getElementById('profilePhoneInput').value = profile.phone || '';
                            document.getElementById('profilePositionInput').value = profile.position || '';
                            
                            // Display information
                            document.getElementById('profileNameDisplay').textContent = profile.name || '';
                            document.getElementById('profileEmailDisplay').textContent = profile.email || '';
                            
                            const defaultPosition = formatRoleName(profile.role);
                            document.getElementById('profilePositionPlaceholder').textContent = profile.position || defaultPosition;
                            
                            // Role Badge
                            const badge = document.getElementById('profileRoleBadge');
                            if (badge) {
                                badge.innerHTML = `<i class="ti ti-shield-check me-1"></i> ${defaultPosition}`;
                            }

                            // Profile Avatar
                            updateProfileAvatarUI(profile);
                        }
                    });
                }
            }

            // Load form details immediately
            loadProfileForm();

            // Trigger file input click when "Ganti Foto" button is clicked
            if (btnUploadPhoto && avatarFileInput) {
                btnUploadPhoto.addEventListener('click', function() {
                    avatarFileInput.click();
                });

                avatarFileInput.addEventListener('change', function() {
                    const file = avatarFileInput.files[0];
                    if (!file) return;

                    // Immediately show local preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatarImg = document.getElementById('profileAvatarImg');
                        const avatarInitials = document.getElementById('profileAvatarInitials');
                        if (avatarImg) {
                            avatarImg.src = e.target.result;
                            avatarImg.classList.remove('d-none');
                        }
                        if (avatarInitials) {
                            avatarInitials.style.setProperty('display', 'none', 'important');
                        }
                    };
                    reader.readAsDataURL(file);

                    // Disable upload button and show loading state
                    const originalBtnHtml = btnUploadPhoto.innerHTML;
                    btnUploadPhoto.disabled = true;
                    btnUploadPhoto.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Mengunggah...';

                    // Prepare Multipart form-data
                    const formData = new FormData();
                    formData.append('avatar', file);

                    fetch(apiUrl + '/user/avatar', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        body: formData
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw new Error(data.error || 'Gagal mengunggah foto.');
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Foto profil berhasil diperbarui.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // Force refresh the cached profile in layouts and update navigation avatars
                        if (typeof window.fetchUserProfile === 'function') {
                            window.fetchUserProfile(true).then(() => {
                                loadProfileForm();
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Upload error:', err);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: err.message || 'Gagal mengunggah foto profil.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        // Reload form to revert preview to original
                        loadProfileForm();
                    })
                    .finally(() => {
                        btnUploadPhoto.disabled = false;
                        btnUploadPhoto.innerHTML = originalBtnHtml;
                        avatarFileInput.value = ''; // Reset file input
                    });
                });
            }

            // Handle Profile Edit Form Submission
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const name = document.getElementById('profileNameInput').value;
                    const phone = document.getElementById('profilePhoneInput').value;

                    // Disable button and show loading state
                    const originalBtnHtml = btnSaveProfile.innerHTML;
                    btnSaveProfile.disabled = true;
                    btnSaveProfile.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Menyimpan...';

                    fetch(apiUrl + '/user/profile', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + token
                        },
                        body: JSON.stringify({ name, phone })
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw new Error(data.error || 'Gagal memperbarui profil.');
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Profil berhasil diperbarui.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        // Force refresh cached profile and update views
                        if (typeof window.fetchUserProfile === 'function') {
                            window.fetchUserProfile(true).then(() => {
                                loadProfileForm();
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Update profile error:', err);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: err.message || 'Gagal memperbarui profil.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    })
                    .finally(() => {
                        btnSaveProfile.disabled = false;
                        btnSaveProfile.innerHTML = originalBtnHtml;
                    });
                });
            }
        });
    </script>
@endsection
