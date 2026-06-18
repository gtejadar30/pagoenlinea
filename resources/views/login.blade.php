<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — Pago en Línea</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
        }

        .panel-left {
            flex: 1;
            display: none;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #0ea5e9 100%);
            padding: 3rem;
            color: #fff;
            flex-direction: column;
            justify-content: center;
        }

        .panel-left h1 {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .panel-left p {
            font-size: 1rem;
            opacity: .85;
            max-width: 400px;
            line-height: 1.6;
        }

        .panel-right {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #fff;
        }

        @media (min-width: 900px) {
            .panel-left { display: flex; }
            .panel-right {
                max-width: none;
                width: 480px;
                margin: 0;
            }
        }

        .login-box {
            width: 100%;
            max-width: 380px;
        }

        .login-box h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .35rem;
        }

        .login-box > p {
            color: #64748b;
            font-size: .9rem;
            margin-bottom: 2rem;
        }

        .field {
            margin-bottom: 1.25rem;
        }

        .field label {
            display: block;
            font-size: .85rem;
            font-weight: 500;
            color: #334155;
            margin-bottom: .4rem;
        }

        .field input {
            width: 100%;
            padding: .75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: .95rem;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
        }

        .field input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        .error-msg {
            background: #fef2f2;
            color: #dc2626;
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: .85rem;
            margin-bottom: 1rem;
            border: 1px solid #fecaca;
        }

        .btn-login {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #1e40af, #1d4ed8);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .15s;
        }

        .btn-login:hover { opacity: .92; }

        .note {
            margin-top: 1.5rem;
            padding: .85rem 1rem;
            background: #f8fafc;
            border-radius: 8px;
            font-size: .8rem;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="panel-left">
        <div>
            <h1>Pago en Línea</h1>
            <p>Consulta tu estado de cuenta, revisa tus deudas municipales y realiza tus trámites de forma segura.</p>
        </div>
    </div>

    <div class="panel-right">
        <div class="login-box">
            <h2>Bienvenido</h2>
            <p>Ingresa tus credenciales para continuar</p>

            @if ($errors->has('code'))
                <div class="error-msg">{{ $errors->first('code') }}</div>
            @endif
            @if ($errors->has('password'))
                <div class="error-msg">{{ $errors->first('password') }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="login_form">
                @csrf

                <div class="field">
                    <label for="code">Código de contribuyente</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Ej: 125846" required autofocus inputmode="numeric" autocomplete="username" maxlength="10">
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login" id="btn_login">Ingresar</button>
            </form>

            <div class="note">
                Use su código de contribuyente (se completa a 10 dígitos automáticamente) y la misma contraseña del pagosonline.
            </div>
        </div>
    </div>

    <script>
        function padCode(value) {
            let code = (value || '').replace(/\D/g, '');
            while (code.length < 10) {
                code = '0' + code;
            }
            return code.slice(0, 10);
        }

        document.getElementById('code').addEventListener('blur', function () {
            if (this.value.trim() !== '') {
                this.value = padCode(this.value);
            }
        });

        document.getElementById('login_form').addEventListener('submit', function () {
            const codeInput = document.getElementById('code');
            codeInput.value = padCode(codeInput.value);
        });
    </script>
</body>
</html>
