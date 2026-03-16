<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
</head>
<body>
    <main style="max-width:420px;margin:40px auto;font-family:Arial,sans-serif;">
        <h1>Connexion</h1>

        @if ($errors->any())
            <div style="color:#b91c1c;margin-bottom:12px;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div style="color:#065f46;margin-bottom:12px;">
                {{ session('status') }}
            </div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login') }}" autocomplete="on">
            @csrf

            <div style="margin-bottom:10px;">
                <label for="email">Email</label><br>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    style="width:100%;padding:8px;"
                >
            </div>

            <div style="margin-bottom:10px;">
                <label for="password">Mot de passe</label><br>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    style="width:100%;padding:8px;"
                >
            </div>

            <div style="margin-bottom:14px;">
                <label for="remember">
                    <input id="remember" type="checkbox" name="remember" autocomplete="off"> Se souvenir de moi
                </label>
            </div>

            <button id="login-submit" name="login-submit" type="submit" style="padding:8px 14px;">Se connecter</button>
        </form>
    </main>
</body>
</html>
