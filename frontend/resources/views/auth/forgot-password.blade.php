<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Forgot Password - InLab Inventory Dashboard</title>
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
            <h1 class="card-title mb-2 h5">Reset Password</h1>
            <p class="text-muted small">Masukkan email Anda untuk menerima link reset password.</p>
          </div>
          
          <form id="forgotPasswordForm" class="needs-validation mt-4">
            <div class="mb-4">
              <label class="form-label" for="email">Email address</label>
              <input id="email" class="form-control" type="email" name="email" placeholder="name@example.com" required autofocus>
              <div class="invalid-feedback">Silakan masukkan email yang valid.</div>
            </div>
            <button id="btnSubmit" class="btn class-btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" type="submit">
              <i class="ti ti-mail-fast fs-5"></i> Kirim Link Reset
            </button>
          </form>

          <div class="text-center mt-4">
            <a href="/login" class="small link-primary d-flex align-items-center justify-content-center gap-1">
              <i class="ti ti-arrow-left"></i> Kembali ke Sign In
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- JS Logic -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('forgotPasswordForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const apiUrl = document.querySelector('meta[name="api-url"]')?.getAttribute('content') || 'http://localhost:3000/api';

        form.addEventListener('submit', function(e) {
          e.preventDefault();

          const email = document.getElementById('email').value.trim();
          if (!email) return;

          const originalBtnHtml = btnSubmit.innerHTML;
          btnSubmit.disabled = true;
          btnSubmit.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Mengirim...';

          fetch(apiUrl + '/auth/forgot-password', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
          })
          .then(res => {
            return res.json().then(data => {
              if (!res.ok) {
                throw new Error(data.error || 'Terjadi kesalahan saat mengirim link.');
              }
              return data;
            });
          })
          .then(data => {
            Swal.fire({
              icon: 'success',
              title: 'Email Terkirim',
              text: data.message || 'Link reset password telah dikirim ke email Anda.',
              confirmButtonText: 'OK',
              confirmButtonColor: '#4f46e5'
            });
            form.reset();
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
