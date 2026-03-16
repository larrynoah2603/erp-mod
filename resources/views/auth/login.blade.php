<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | ERP</title>
    <style>
        :root { color-scheme: light; }
        body { margin:0; font-family: Inter, Arial, sans-serif; background:#f3f4f6; color:#111827; }
        .wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .card { width:100%; max-width:420px; background:#fff; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,.08); padding:24px; }
        h1 { margin:0 0 6px; font-size:24px; }
        .muted { color:#6b7280; font-size:14px; margin-bottom:18px; }
        label { font-size:14px; font-weight:600; }
        input[type="email"], input[type="password"] { width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; margin-top:6px; }
        input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
        .row { margin-bottom:14px; }
        .btn { width:100%; padding:10px 14px; border:0; border-radius:8px; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; }
        .btn:hover { background:#1d4ed8; }
        .error { color:#b91c1c; margin-bottom:12px; font-size:14px; }
        .ok { color:#065f46; margin-bottom:12px; font-size:14px; }
        .remember { display:flex; align-items:center; gap:8px; font-size:14px; }
        .hint { margin-top:16px; font-size:12px; color:#6b7280; background:#f9fafb; border:1px dashed #d1d5db; border-radius:8px; padding:10px; }
    </style>
</head>
<body>
<div class="wrap">
    <main class="card">
        <h1>Connexion</h1>
        <p class="muted">Connectez-vous à votre espace ERP</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="ok">{{ session('status') }}</div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login') }}" autocomplete="on">
            @csrf

            <div class="row">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>

            <div class="row">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="row remember">
                <input id="remember" type="checkbox" name="remember" autocomplete="off">
                <label for="remember" style="font-weight:500;">Se souvenir de moi</label>
            </div>

            <button id="login-submit" name="login-submit" type="submit" class="btn">Se connecter</button>
        </form>

        <div class="hint">
            Comptes de démonstration : admin@demo.com / password, manager@demo.com / password.
        </div>
    </main>
</div>
</body>
</html>
