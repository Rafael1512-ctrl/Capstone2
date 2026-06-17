<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Reset Password - InLab Inventory Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/assets/images/favicon_io/site.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="api-url" content="{{ env('NODE_API_URL', 'http://localhost:3000/api') }}">
  </head>
  <body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
      <div class="card" style="max-width:420px; width:100%;">
        <div class="card-body p-5">
          <div class="text-center mb-3">
            <a class="mb-4 d-inline-flex align-items-center justify-content-center text-decoration-none" href="/">
              <img src="/assets/images/logo-icon.svg" alt="" width="38" height="38">
              <span class="ms-2 text-start">
                <span class="fw-bold text-primary" style="font-size: 26px; line-height: 1; font-family: 'Poppins', sans-serif; letter-spacing: -0.5px;">In</span><span class="fw-bold text-dark" style="font-size: 26px; line-height: 1; font-family: 'Poppins', sans-serif; letter-spacing: -0.5px;">Lab</span>
                <span class="text-secondary d-block" style="font-size: 10px; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; margin-top: -2px; font-family: 'Poppins', sans-serif;">Inventory Lab</span>
              </span>
            </a>
            <h1 class="card-title mb-2 h5">Ubah Password Baru</h1>
            <p class="text-muted small">Silakan masukkan password baru Anda (minimal 8 karakter).</p>
          </div>
          
          <form id="resetPasswordForm" class="needs-validation mt-4">
            <div class="mb-3">
              <label class="form-label" for="password">Password Baru</label>
              <input id="password" class="form-control" type="password" name="password" placeholder="Password Baru" required minlength="8">
              <div class="invalid-feedback">Password minimal 8 karakter.</div>
            </div>
            <div class="mb-4">
              <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
              <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="Ulangi Password Baru" required minlength="8">
              <div class="invalid-feedback">Silakan ulangi password baru.</div>
            </div>
            <button id="btnSubmit" class="btn class-btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" type="submit">
              <i class="ti ti-lock-open fs-5"></i> Simpan Password Baru
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- JS Logic -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetPasswordForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const apiUrl = document.querySelector('meta[name="api-url"]')?.getAttribute('content') || 'http://localhost:3000/api';

        // Extract token and email from query params
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const email = urlParams.get('email');

        if (!token || !email) {
          Swal.fire({
            icon: 'error',
            title: 'Tautan Tidak Valid',
            text: 'Tautan reset password ini tidak valid atau tidak lengkap. Silakan minta tautan baru.',
            confirmButtonText: 'Kembali ke Sign In',
            confirmButtonColor: '#4f46e5',
            allowOutsideClick: false
          }).then(() => {
            window.location.href = '/login';
          });
          btnSubmit.disabled = true;
          return;
        }

        form.addEventListener('submit', function(e) {
          e.preventDefault();

          const password = document.getElementById('password').value;
          const password_confirmation = document.getElementById('password_confirmation').value;

          if (password !== password_confirmation) {
            Swal.fire({
              icon: 'error',
              title: 'Kesalahan',
              text: 'Konfirmasi password tidak cocok.',
              confirmButtonText: 'OK',
              confirmButtonColor: '#4f46e5'
            });
            return;
          }

          if (password.length < 8) {
            Swal.fire({
              icon: 'error',
              title: 'Kesalahan',
              text: 'Password minimal harus 8 karakter.',
              confirmButtonText: 'OK',
              confirmButtonColor: '#4f46e5'
            });
            return;
          }

          const originalBtnHtml = btnSubmit.innerHTML;
          btnSubmit.disabled = true;
          btnSubmit.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Menyimpan...';

          fetch(apiUrl + '/auth/reset-password', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, token, password, password_confirmation })
          })
          .then(res => {
            return res.json().then(data => {
              if (!res.ok) {
                throw new Error(data.error || 'Gagal mengubah password.');
              }
              return data;
            });
          })
          .then(data => {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: data.message || 'Password Anda berhasil diubah. Mengalihkan ke login...',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
            
            setTimeout(() => {
              window.location.href = '/login';
            }, 2000);
          })
          .catch(err => {
            console.error(err);
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: err.message || 'Terjadi kesalahan pada server.',
              confirmButtonText: 'Coba Lagi',
              confirmButtonColor: '#4f46e5'
            });
          })
          .finally(() => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalBtnHtml;
          });
        });
      });
    </script>
  </body>
</html>
