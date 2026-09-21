<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Alamada Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #f0faf0 0%, #e8f5e9 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
        }

        /* ── Wrapper ── */
        .login-wrapper {
            width: 100%; max-width: 420px;
            display: flex; flex-direction: column; align-items: center;
        }

        /* ── Logo ── */
        .logo-area {
            width: 100%; text-align: center;
            margin-bottom: 10px;
        }
        .logo-area img {
            width: 280px;
            max-width: 100%;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 6px 18px rgba(0,0,0,0.13));
        }

        /* ── Login card ── */
        .login-card {
            width: 100%;
            background: #ffffff;
            border-radius: 22px;
            padding: 30px 30px 24px;
            box-shadow: 0 8px 40px rgba(46,125,50,0.13);
        }

        /* ── Fields ── */
        .field-wrap { position: relative; margin-bottom: 16px; }
        .field-wrap .fi {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%);
            color: #9e9e9e; font-size: .9rem; pointer-events: none;
        }
        .field-wrap input {
            width: 100%; padding: 13px 44px 13px 42px;
            border: 1.5px solid #e0e0e0; border-radius: 12px;
            font-size: .93rem; color: #212121;
            background: #fafafa; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field-wrap input:focus {
            border-color: #43a047;
            box-shadow: 0 0 0 3px rgba(67,160,71,.12);
            background: #fff;
        }
        .field-wrap input::placeholder { color: #bdbdbd; }
        .toggle-pwd {
            position: absolute; right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9e9e9e; cursor: pointer; font-size: .9rem; padding: 0;
        }
        .toggle-pwd:hover { color: #43a047; }

        /* ── Error ── */
        .err-box {
            background: #ffebee; border: 1px solid #ffcdd2;
            border-radius: 10px; padding: 10px 14px;
            color: #c62828; font-size: .84rem; margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }

        /* ── Login button ── */
        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            color: #fff; border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 16px rgba(46,125,50,.32);
            transition: opacity .2s, transform .1s;
        }
        .btn-login:hover   { opacity: .92; }
        .btn-login:active  { transform: scale(.99); }
        .btn-login:disabled { opacity: .65; cursor: not-allowed; }

        /* ── Forgot ── */
        .forgot-link {
            display: block; text-align: center; margin-top: 14px;
            color: #2e7d32; font-size: .88rem; font-weight: 600;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* ── Role badges ── */
        .role-note {
            display: flex; gap: 8px; justify-content: center;
            margin-top: 16px; padding-top: 14px;
            border-top: 1px solid #f0f0f0;
        }
        .role-badge {
            font-size: .72rem; padding: 3px 10px;
            border-radius: 20px; font-weight: 700;
        }
        .role-admin  { background:#ffebee; color:#c62828; }
        .role-staff  { background:#e3f2fd; color:#1565c0; }

        /* ── Green leaf corners ── */
        .leaf-left {
            position: fixed; bottom:0; left:-10px;
            width:150px; height:100px;
            background: radial-gradient(ellipse at 30% 80%, #2e7d32, #388e3c);
            border-radius: 0 90% 0 0; z-index:0; opacity:.85;
        }
        .leaf-right {
            position: fixed; bottom:0; right:-10px;
            width:150px; height:100px;
            background: radial-gradient(ellipse at 70% 80%, #2e7d32, #388e3c);
            border-radius: 90% 0 0 0; z-index:0; opacity:.85;
        }
    </style>
</head>
<body>

<div class="leaf-left"></div>
<div class="leaf-right"></div>

<div class="login-wrapper" style="position:relative;z-index:10">

    <!-- ══ LOGO IMAGE ══ -->
    <div class="logo-area">
        <img src="{{ asset('images/logo.png') }}"
             alt="Alamada Learning Center Monitoring System"
             onerror="this.src='{{ asset('images/logo.svg') }}'"
             style="width:280px;max-width:100%;display:block;margin:0 auto;
                    filter:drop-shadow(0 4px 16px rgba(0,0,0,0.12))">
    </div>

    <!-- ══ LOGIN CARD ══ -->
    <div class="login-card">

        @if ($errors->any())
        <div class="err-box">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if (session('status'))
        <div class="err-box" style="background:#e8f5e9;border-color:#c8e6c9;color:#2e7d32">
            <i class="fas fa-check-circle"></i>{{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <!-- Email -->
            <div class="field-wrap">
                <i class="fas fa-envelope fi"></i>
                <input type="email" name="email" id="emailInput"
                       value="{{ old('email') }}"
                       placeholder="Email Address"
                       autocomplete="email" autofocus required>
            </div>

            <!-- Password -->
            <div class="field-wrap">
                <i class="fas fa-lock fi"></i>
                <input type="password" name="password" id="pwdInput"
                       placeholder="Password"
                       autocomplete="current-password" required>
                <button type="button" class="toggle-pwd" onclick="togglePwd()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>

            <!-- Log In button -->
            <button type="submit" class="btn-login" id="loginBtn">
                <span id="btnText"><i class="fas fa-sign-in-alt me-2"></i>Log In</span>
                <span id="btnLoad" style="display:none">
                    <span class="spinner-border spinner-border-sm me-2"
                          style="width:.9rem;height:.9rem;border-width:.15em"></span>Signing in…
                </span>
            </button>
        </form>

        <!-- Forgotten password -->
        <a href="#" class="forgot-link" onclick="showForgot(event)">
            Forgotten password?
        </a>

        <!-- Role badges -->
        <div class="role-note">
            <span class="role-badge role-admin">Admin</span>
            <span class="role-badge role-staff">Staff</span>
        </div>
    </div>
</div>

<!-- Forgotten password modal -->
<div id="fgOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
            z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:20px;padding:28px 24px;
                max-width:300px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,.2)">
        <div style="text-align:center;margin-bottom:14px">
            <i class="fas fa-lock" style="font-size:2rem;color:#43a047"></i>
        </div>
        <h6 style="font-weight:700;text-align:center;margin-bottom:8px">
            Password Recovery
        </h6>
        <p style="font-size:.84rem;color:#757575;text-align:center;margin-bottom:20px">
            Please contact the <strong>Learning Center Administrator</strong>
            to reset your password.<br><br>
            Email: <strong style="color:#2e7d32">admin@alamada-lgu.gov.ph</strong>
        </p>
        <button onclick="hideForgot()"
                style="width:100%;padding:12px;
                       background:linear-gradient(135deg,#388e3c,#2e7d32);
                       color:#fff;border:none;border-radius:12px;
                       font-weight:700;font-size:.95rem;cursor:pointer">
            Got it
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const p = document.getElementById('pwdInput');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') {
        p.type = 'text';
        i.classList.replace('fa-eye','fa-eye-slash');
    } else {
        p.type = 'password';
        i.classList.replace('fa-eye-slash','fa-eye');
    }
}
function showForgot(e) {
    e.preventDefault();
    document.getElementById('fgOverlay').style.display = 'flex';
}
function hideForgot() {
    document.getElementById('fgOverlay').style.display = 'none';
}
document.getElementById('fgOverlay').addEventListener('click', function(e) {
    if (e.target === this) hideForgot();
});
document.getElementById('loginForm').addEventListener('submit', function() {
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('btnLoad').style.display = 'inline';
    document.getElementById('loginBtn').disabled = true;
});
</script>
</body>
</html>
