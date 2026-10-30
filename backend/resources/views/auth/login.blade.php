<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Alamada CMS">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <title>Login — Alamada Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            min-height: -webkit-fill-available;
            background: linear-gradient(160deg, #f0faf0 0%, #e8f5e9 100%);
            display: flex; align-items: flex-start; justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: max(40px, env(safe-area-inset-top)) 20px max(40px, env(safe-area-inset-bottom));
            -webkit-tap-highlight-color: rgba(0,0,0,0.05);
            overflow-y: auto;
        }

        /* ── Wrapper ── */
        .login-wrapper {
            width: 100%; max-width: 420px;
            display: flex; flex-direction: column; align-items: center;
            margin: auto;
        }

        /* ── Logo ── */
        .logo-area {
            width: 100%; text-align: center;
            margin-bottom: 0;
            padding-top: 16px;
        }
        .logo-area img {
            width: 340px;
            max-width: 96%;
            height: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 4px 14px rgba(0,0,0,0.10));
            object-fit: contain;
            object-position: center;
        }

        /* ── Login card ── */
        .login-card {
            width: 100%;
            background: #ffffff;
            border-radius: 22px;
            padding: 14px 30px 24px;
            box-shadow: 0 8px 40px rgba(46,125,50,0.13);
            overflow: visible;
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
            min-width: 44px; min-height: 44px;
            display: flex; align-items: center; justify-content: center;
        }
        .toggle-pwd:hover { color: #43a047; }

        /* iOS: prevent auto-zoom on input focus (font-size must be >= 16px) */
        @media (max-width: 768px) {
            .field-wrap input { font-size: 16px; }
            .btn-login { font-size: 16px; min-height: 50px; }
        }

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

        /* ── Wake-up splash ── */
        #wakeOverlay {
            position: fixed; inset: 0; z-index: 9000;
            background: linear-gradient(160deg, #f0faf0 0%, #e8f5e9 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 22px;
        }
        #wakeOverlay img.splash-logo {
            width: 260px; max-width: 80%;
            filter: drop-shadow(0 4px 14px rgba(0,0,0,0.12));
            animation: pulse 1.8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1;   transform: scale(1); }
            50%      { opacity: .75; transform: scale(.97); }
        }
        .wake-label {
            font-size: .88rem; color: #4caf50; font-weight: 600;
            letter-spacing: .04em;
        }
        .wake-dots span {
            display: inline-block; width: 8px; height: 8px;
            background: #4caf50; border-radius: 50%; margin: 0 3px;
            animation: bounce 1.2s ease-in-out infinite;
        }
        .wake-dots span:nth-child(2) { animation-delay: .2s; }
        .wake-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes bounce {
            0%,80%,100% { transform: translateY(0); }
            40%          { transform: translateY(-8px); }
        }

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

<!-- ══ WAKE-UP SPLASH — shown while the server cold-starts ══ -->
<div id="wakeOverlay">
    <img class="splash-logo"
         src="{{ asset('images/logo.png') }}"
         onerror="this.src='{{ asset('images/logo.svg') }}'"
         alt="Alamada Learning Center">
    <div class="wake-label" id="wakeLabel">Starting up, please wait…</div>
    <div class="wake-dots">
        <span></span><span></span><span></span>
    </div>
</div>

<div class="leaf-left"></div>
<div class="leaf-right"></div>

<div class="login-wrapper" style="position:relative;z-index:10">

    <!-- ══ LOGIN CARD (logo lives inside) ══ -->
    <div class="login-card">

        <!-- ══ LOGO IMAGE ══ -->
        <div class="logo-area" style="margin-bottom:18px;">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Alamada Learning Center Monitoring System"
                 onerror="this.src='{{ asset('images/logo.svg') }}'"
                 style="width:340px;max-width:96%;height:auto;display:block;margin:0 auto;
                        filter:drop-shadow(0 4px 14px rgba(0,0,0,0.10));object-fit:contain">
        </div>

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
// ── Wake-up ping — hides the splash once the server responds ─────────────
(function wakeUp() {
    const overlay  = document.getElementById('wakeOverlay');
    const label    = document.getElementById('wakeLabel');
    const apiBase  = '{{ rtrim(config("app.url"), "/") }}';
    const maxWait  = 90;   // seconds before giving up and showing the form anyway
    const interval = 3000; // ms between retries
    let   elapsed  = 0;

    const messages = [
        'Starting up, please wait…',
        'Waking the server…',
        'Almost there…',
        'Connecting to database…',
        'Loading application…',
    ];
    let msgIdx = 0;

    const msgTimer = setInterval(() => {
        msgIdx = (msgIdx + 1) % messages.length;
        label.textContent = messages[msgIdx];
    }, 3500);

    function reveal() {
        clearInterval(msgTimer);
        overlay.style.transition = 'opacity .5s ease';
        overlay.style.opacity    = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(() => overlay.remove(), 520);
        document.getElementById('emailInput').focus();
    }

    function ping() {
        fetch(apiBase + '/api/health', { cache: 'no-store' })
            .then(r => { if (r.ok) { reveal(); } else { retry(); } })
            .catch(() => retry());
    }

    function retry() {
        elapsed += interval / 1000;
        if (elapsed >= maxWait) { reveal(); return; }
        setTimeout(ping, interval);
    }

    // Start immediately, then retry on failure
    ping();
})();

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
