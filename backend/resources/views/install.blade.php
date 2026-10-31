<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Install App — Alamada Learning Center</title>
    <meta name="description" content="Download and install the Alamada LGU Learning Center Parent Monitoring App for Android.">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100svh;
            background: linear-gradient(160deg, #f0faf0 0%, #e8f5e9 100%);
            font-family: -apple-system, 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px max(24px, env(safe-area-inset-bottom));
        }

        .wrap { width: 100%; max-width: 420px; }

        /* ── Logo ── */
        .logo-row {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-row img {
            width: 200px;
            max-width: 80%;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,.12));
        }
        .logo-fallback {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
        }
        .logo-fallback span:first-child {
            font-size: 2.2rem; font-weight: 900; color: #2e7d32; letter-spacing: -.5px;
        }
        .logo-fallback span:last-child {
            font-size: .75rem; color: #81c784; letter-spacing: 2px; text-transform: uppercase;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 24px;
            padding: 28px 24px 24px;
            box-shadow: 0 8px 40px rgba(46,125,50,.13);
            margin-bottom: 18px;
        }

        /* App icon */
        .app-icon {
            width: 76px; height: 76px; border-radius: 20px;
            background: linear-gradient(135deg, #43a047, #2e7d32);
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; margin: 0 auto 14px;
            box-shadow: 0 6px 20px rgba(46,125,50,.30);
        }
        .app-name { font-size: 1.2rem; font-weight: 800; color: #212121; text-align: center; }
        .app-sub  { font-size: .83rem; color: #9e9e9e; text-align: center; margin-bottom: 20px; margin-top: 3px; }

        /* Meta pills */
        .meta { display: flex; justify-content: center; gap: 10px; margin-bottom: 22px; flex-wrap: wrap; }
        .pill {
            background: #e8f5e9; border-radius: 999px;
            padding: 5px 14px; font-size: .8rem; font-weight: 600; color: #2e7d32;
            display: flex; align-items: center; gap: 5px;
        }

        /* Download button */
        .btn-dl {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #43a047, #2e7d32);
            color: #fff; border: none; border-radius: 14px;
            font-size: 1.05rem; font-weight: 800; cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(46,125,50,.35);
            transition: opacity .15s, transform .1s;
        }
        .btn-dl:hover  { opacity: .92; color: #fff; }
        .btn-dl:active { transform: scale(.98); color: #fff; }
        .btn-dl svg { width: 22px; height: 22px; fill: #fff; flex-shrink: 0; }
        .badge {
            background: rgba(255,255,255,.22); border-radius: 6px;
            padding: 2px 8px; font-size: .75rem; font-weight: 600;
        }

        .hint {
            text-align: center; font-size: .78rem; color: #a5a5a5;
            margin-top: 8px; line-height: 1.5;
        }

        /* Divider */
        hr { border: none; border-top: 1px solid #f0f0f0; margin: 20px 0; }

        /* Steps */
        .steps-title { font-size: .95rem; font-weight: 700; color: #2e7d32; margin-bottom: 14px; }
        .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .step-num {
            min-width: 28px; height: 28px; border-radius: 50%;
            background: #e8f5e9; color: #2e7d32;
            font-size: .78rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .step-text { font-size: .86rem; color: #424242; line-height: 1.55; }
        .step-text b { color: #212121; }

        /* QR card */
        .qr-card {
            background: #fff; border-radius: 24px;
            padding: 22px 20px; text-align: center;
            box-shadow: 0 8px 40px rgba(46,125,50,.10);
            margin-bottom: 18px;
        }
        .qr-title { font-size: .9rem; font-weight: 700; color: #212121; margin-bottom: 14px; }
        #qr { border-radius: 10px; margin: 0 auto; }
        .qr-url { font-size: .78rem; color: #757575; margin-top: 10px; word-break: break-all; }
        .qr-url b { color: #2e7d32; }

        /* Footer */
        .footer { text-align: center; font-size: .78rem; color: #bdbdbd; padding-bottom: 8px; }
        .footer a { color: #81c784; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="wrap">

    {{-- Logo --}}
    <div class="logo-row">
        <img src="{{ asset('images/logo.png') }}"
             alt="Alamada Learning Center"
             onerror="this.style.display='none';document.getElementById('logo-fb').style.display='flex'">
        <div id="logo-fb" class="logo-fallback" style="display:none">
            <span>ALAMADA</span>
            <span>Learning Center</span>
        </div>
    </div>

    {{-- Main card --}}
    <div class="card">

        <div class="app-icon">📱</div>
        <div class="app-name">Alamada LGU Parent App</div>
        <div class="app-sub">Mobile Monitoring System &nbsp;·&nbsp; Android</div>

        {{-- Meta pills --}}
        <div class="meta">
            <span class="pill">
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:#2e7d32"><path d="M17 1H7C5.9 1 5 1.9 5 3v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V5h10v14z"/></svg>
                Android 7.0+
            </span>
            <span class="pill">
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:#2e7d32"><path d="M20 6h-2.18c.07-.44.18-.88.18-1.35C18 2.55 16.35 1 14.35 1c-1.4 0-2.37.72-3.29 1.53L10 3.35 8.94 2.53C8.02 1.72 6.93 1 5.62 1 3.62 1 2 2.55 2 4.65c0 .47.1.91.18 1.35H0v2h1v11h22V8h1V6h-3zm-6.35-3c1 0 1.35.72 1.35 1.65 0 1.33-1.6 2.7-3 3.78C10.6 7.35 9 5.98 9 4.65 9 3.72 9.35 3 10.35 3h3.3z"/></svg>
                Free
            </span>
            <span class="pill">
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:#2e7d32"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                v1.0
            </span>
        </div>

        {{-- Download button --}}
        <a href="{{ route('app.apk') }}" class="btn-dl" id="dl-btn">
            <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zm-14 9v2h14v-2H5z"/></svg>
            Download APK
            <span class="badge">~7.4 MB</span>
        </a>

        <p class="hint">
            Opens directly on your Android phone<br>
            No Google Play account needed
        </p>

        <hr>

        {{-- Install steps --}}
        <div class="steps-title">📋 How to install</div>

        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text">Tap <b>Download APK</b> above and wait for the file to finish downloading.</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">Open your phone's <b>Files</b> or <b>Downloads</b> app and tap <b>AlamadaLearningCenter.apk</b>.</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">If asked, tap <b>"Install anyway"</b> or go to <b>Settings → Security → Install unknown apps</b> and allow your browser.</div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-text">Open the app and log in with the credentials given to you by the <b>Learning Center</b>.</div>
        </div>

        <hr>

        <p style="font-size:.8rem; color:#9e9e9e; text-align:center; line-height:1.7">
            No account yet? Contact the administrator<br>
            <b style="color:#2e7d32">admin@alamada-lgu.gov.ph</b>
        </p>

    </div>{{-- /card --}}

    {{-- QR card --}}
    <div class="qr-card">
        <div class="qr-title">📲 Scan to open on your phone</div>
        <canvas id="qr" width="160" height="160"></canvas>
        <p class="qr-url">Or share this link:<br>
            <b>{{ url('/install') }}</b>
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <a href="{{ route('login') }}">← Staff / Admin Portal</a>
        &nbsp;·&nbsp;
        Alamada LGU Learning Center © {{ date('Y') }}
    </div>

</div>{{-- /wrap --}}

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qr'), {
        text:         '{{ url("/install") }}',
        width:        160,
        height:       160,
        colorDark:    '#2e7d32',
        colorLight:   '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
</script>

</body>
</html>
