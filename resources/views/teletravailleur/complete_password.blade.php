<!DOCTYPE html>
<html>
<head>
    <title>Changer le Mot de Passe - TeleMonit</title>
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            width: 400px;
            position: relative;
        }
        .avatar {
            width: 100px;
            height: 100px;
            background: #4a90e2;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar img {
            width: 60%;
            height: auto;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-sizing: border-box;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        button {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            color: white;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        button:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        .dots {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .dot {
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            margin: 0 5px;
        }
        .dot.active {
            background: white;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: left;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="avatar">
            <!-- Placeholder pour l'avatar, à remplacer par une image réelle si disponible -->
        </div>
        <form method="POST" action="{{ route('teletravailleur.change.password', $token) }}">
            @csrf
            <input type="password" name="old_password" placeholder="Old password:" required>
            @error('old_password')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="password" name="new_password" placeholder="New password:" required>
            @error('new_password')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="password" name="new_password_confirmation" placeholder="Confirm password:" required>
            <button type="submit">Next →</button>
        </form>
        <div class="dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
</body>
</html>
