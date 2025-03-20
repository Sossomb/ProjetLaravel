<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ISI BURGER</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #dbeafe, #bfdbfe);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .glass-card h2 {
            color: #1e3a8a;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.3);
            outline: none;
            transition: 0.3s;
        }
        .input-field:focus {
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.5);
        }
        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background: #3b82f6;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }
        .btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
<div class="glass-card">
    <h2>Connexion</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label class="block text-blue-900 font-semibold mb-2" for="email">Email</label>
        <input id="email" class="input-field mb-4" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label class="block text-blue-900 font-semibold mb-2" for="password">Mot de passe</label>
        <input id="password" class="input-field mb-4" type="password" name="password" required>

        <div class="flex justify-between items-center mb-4">
            <label class="text-blue-900 flex items-center">
                <input type="checkbox" name="remember" class="mr-2"> Se souvenir de moi
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-blue-900 text-sm hover:underline">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <button type="submit" class="btn">Se connecter</button>
    </form>
</div>
</body>
</html>
