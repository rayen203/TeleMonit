<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            position: relative;
        }
        .email-wrapper {
            width: 100%;
            min-height: 100vh;
            background-color: #EDF2F7;
        }
        .logo-container {
            width: 1022px;
            height: auto;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFFFFF;
            border-radius: 10px;
        }
        .logo {
            display: block;
            width: 409.5px;
            height: 91px;
            margin: 20px auto 0;
        }
        .content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            color: #696669;
        }
        .content h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #000;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
            margin: 10px 0;
            color: #626161;
        }
        .credentials {
            margin: 20px 0;
            text-align: left;
            display: inline-block;
        }
        .credentials p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 30px;
            background-color: #000A44;
            color: #fff !important;
            text-decoration: none;
            border-radius: 16px;
            font-weight: bold;
            margin: 20px 0;
        }
        .external-footer {
            margin-top: 20px;
            font-size: 12px;
            color: #4A5568;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <img src="cid:logo2.png" alt="TeleMonit Logo" class="logo">
        <div class="logo-container">
            <div class="content">
                @yield('content')
            </div>
        </div>
        <div class="external-footer">
            <p>© 2025 TELEMONIT. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
