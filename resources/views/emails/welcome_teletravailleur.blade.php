<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez TeleMonit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #1e3a8a;
            color: white;
            text-align: center;
            padding: 10px 0;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            background-color: #1e3a8a;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TeleMonit</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->prenom }} {{ $user->nom }},</p>
            <p>Bienvenue chez TeleMonit ! Nous sommes ravis de vous accueillir dans notre équipe de télétravail.</p>
            <p>Voici vos identifiants de connexion pour accéder à votre compte :</p>
            <ul>
                <li><strong>Email :</strong> {{ $user->email }}</li>
                <li><strong>Mot de passe temporaire :</strong> {{ $password }}</li>
            </ul>
            <p>Veuillez compléter votre profil en cliquant sur le bouton ci-dessous ou en visitant ce lien directement : <a href="{{ $completionLink }}">Compléter mon profil</a></p>
            <a href="{{ $completionLink }}" class="button">Compléter mon profil</a>
            <p>Après avoir complété votre profil, vous pourrez accéder à votre tableau de bord et commencer à utiliser nos outils de monitoring.</p>
            <p>Si vous avez des questions, n’hésitez pas à contacter notre équipe d’assistance.</p>
            <p>Cordialement,</p>
            <p>L’équipe TeleMonit</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} TeleMonit. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
