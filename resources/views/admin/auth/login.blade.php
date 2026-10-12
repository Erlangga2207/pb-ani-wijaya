<!DOCTYPE html>
<html lang="id">
<head>
    @include('admin.layouts.aset')
    <title>Masuk — PB. Ani Wijaya</title>
</head>
<body>
<main class="login-latar">
    <div class="kotak login-kotak">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="PB. Ani Wijaya" width="72" height="72" style="object-fit:contain">
            <h1 class="mt-2 mb-0">Masuk ke panel</h1>
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required autofocus autocomplete="username">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="current-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-3">
                <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>

        <p class="small text-ink-2 text-center mt-3 mb-0">Lupa kata sandi? Minta pemilik mengaturnya ulang.</p>
    </div>
</main>
</body>
</html>
