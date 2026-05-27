<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Signin - InApp Inventory Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/assets/images/favicon_io/site.webmanifest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
  </head>
  <body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
      <div class="card" style="max-width:420px; width:100%;">
        <div class="card-body p-5">
          <div class="text-center mb-3">
            <a class="mb-4 d-inline-block" href="/">
              <img src="/assets/images/logo-icon.svg" alt="" width="36">
              <span class="ms-2">
                <img src="/assets/images/logo.svg" alt="">
              </span>
            </a>
            <h1 class="card-title mb-5 h5">Sign in to your account</h1>
          </div>
          
          @if (session('error') || isset($error))
            <div class="alert alert-danger small py-2 text-center mb-3">
              {{ session('error') ?? $error }}
            </div>
          @endif
          
          <form class="needs-validation mt-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="email">Email address</label>
              <input id="email" class="form-control" type="email" name="email" placeholder="name@example.com" required autofocus>
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
