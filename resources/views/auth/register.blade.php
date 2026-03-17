<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte | ERP</title>
    <style>
        body { margin:0; font-family: Inter, Arial, sans-serif; background:#f3f4f6; color:#111827; }
        .wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .card { width:100%; max-width:460px; background:#fff; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,.08); padding:24px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media (max-width: 640px) { .grid { grid-template-columns:1fr; } }
        .row { margin-bottom:14px; }
        label { display:block; font-size:14px; font-weight:600; margin-bottom:6px; }
        input { width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; }
        .btn { width:100%; padding:10px 14px; border:0; border-radius:8px; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; }
        .error { color:#b91c1c; margin-bottom:12px; font-size:14px; }
        .link { text-align:center; margin-top:12px; font-size:14px; }
    </style>
</head>
<body>
<div class="wrap">
    <main class="card">
        <h1>Créer un compte</h1>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid">
                <div class="row">
                    <label for="first_name">Prénom</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required>
                </div>
                <div class="row">
                    <label for="last_name">Nom</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required>
                </div>
            </div>
            <div class="row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="row">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="row">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button class="btn" type="submit">Créer mon compte</button>
        </form>

        <p class="link">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
    </main>
</div>
</body>
</html>
