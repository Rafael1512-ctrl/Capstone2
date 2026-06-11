<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Signin - InLab Inventory Dashboard</title>
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
            <h1 class="card-title mb-5 h5">Sign in to your account</h1>
          </div>
          
          @if (session('success'))
            <div class="alert alert-success small py-2 text-center mb-3 d-flex align-items-center justify-content-center gap-2">
              <i class="ti ti-circle-check"></i>
              <span>{{ session('success') }}</span>
            </div>
          @endif

          @if (session('error') || isset($error))
            @php $errorType = session('error_type', 'auth'); @endphp
            @if ($errorType === 'unverified')
              <div class="alert alert-warning border border-warning small py-3 mb-3" role="alert">
                <div class="d-flex align-items-start gap-2">
                  <i class="ti ti-mail-exclamation fs-5 mt-1 text-warning-emphasis"></i>
                  <div class="text-start">
                    <strong class="d-block mb-1">Email Belum Diverifikasi</strong>
                    <span>{{ session('error') ?? $error }}</span>
                    <p class="mb-0 mt-2 text-muted" style="font-size: 0.8rem;">
                      Buka inbox email Anda dan klik tautan verifikasi. Jika tidak ada, minta admin mengirim ulang dari menu Kelola Pengguna.
                    </p>
                  </div>
                </div>
              </div>
            @elseif ($errorType === 'connection')
              <div class="alert alert-danger border border-danger small py-3 mb-3" role="alert">
                <div class="d-flex align-items-start gap-2">
                  <i class="ti ti-plug-connected-x fs-5 mt-1"></i>
                  <div class="text-start">
                    <strong class="d-block mb-1">Backend Tidak Aktif</strong>
                    <span>{{ session('error') ?? $error }}</span>
                    <p class="mb-0 mt-2 text-muted" style="font-size: 0.8rem;">
                      Jalankan <code>npm run dev</code> atau <code>npx nodemon index.js</code> di folder root proyek.
                    </p>
                  </div>
                </div>
              </div>
            @else
              <div class="alert alert-danger small py-2 text-center mb-3 d-flex align-items-center justify-content-center gap-2">
                <i class="ti ti-alert-circle"></i>
                <span>{{ session('error') ?? $error }}</span>
              </div>
            @endif
          @endif
          
          <form class="needs-validation mt-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="email">Email address</label>
              <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
              <label class="form-label d-flex justify-content-between" for="password">
                <span>Password</span>
                <a class="small link-primary" href="#">Forgot Password?</a>
              </label>
              <input id="password" class="form-control" type="password" name="password" placeholder="Password" required minlength="6">
              <div class="invalid-feedback">Please provide a password (min 6 characters).</div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input id="remember" class="form-check-input" type="checkbox">
                <label class="form-check-label small" for="remember">Remember me</label>
              </div>
            </div>
            <button class="btn class-btn btn-primary w-100" type="submit">Sign in</button>
          </form>
          <div class="text-center mt-3 small text-muted">Don't have an account?&nbsp;
            <a class="link-primary" href="#">Sign up</a>
            <hr class="my-3">
            <div class="text-start">
              <span class="small text-secondary fw-semibold">Akun Dummy (Password: password):</span>
              <ul class="small text-secondary ps-3 mb-0 mt-1">
                <li><a class="text-decoration-none" href="#" onclick="document.getElementById('email').value='admin@mail.com';document.getElementById('password').value='password';">admin@mail.com (Admin)</a></li>
                <li><a class="text-decoration-none" href="#" onclick="document.getElementById('email').value='kepala@mail.com';document.getElementById('password').value='password';">kepala@mail.com (Kepala Lab)</a></li>
                <li><a class="text-decoration-none" href="#" onclick="document.getElementById('email').value='prodi@mail.com';document.getElementById('password').value='password';">prodi@mail.com (Ketua Prodi)</a></li>
                <li><a class="text-decoration-none" href="#" onclick="document.getElementById('email').value='stafadmin@mail.com';document.getElementById('password').value='password';">stafadmin@mail.com (Staf Admin)</a></li>
                <li><a class="text-decoration-none" href="#" onclick="document.getElementById('email').value='staflab@mail.com';document.getElementById('password').value='password';">staflab@mail.com (Staf Lab)</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
