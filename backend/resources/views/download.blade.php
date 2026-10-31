<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Download App — Alamada Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #f0faf0 0%, #e8f5e9 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex; flex-direction: column;
            padding: max(20px, env(safe-area-inset-top)) 20px
                     max(20px, env(safe-area-inset-bottom));
        }
        .page-wrap { max-width: 480px; width: 100%; margin: 0 auto; }

        .logo-area { text-align: center; margin: 10px 0 16px; }
        .logo-area img { width: 220px; max-width: 90%;
            filter: drop-shadow(0 4px 14px rgba(0,0,0,.12)); }

        .card-main {
            background: #fff; border-radius: 22px; padding: 28px 26px 22px;
            box-shadow: 0 8px 40px rgba(46,125,50,.13); margin-bottom: 20px;
        }
        .app-icon {
            width: 72px; height: 72px; border-radius: 18px;
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            display: flex; align-items: center; justify-content: center;
            font-size: 38px; margin: 0 auto 14px;
            box-shadow: 0 4px 16px rgba(46,125,50,.30);
        }
        .app-title { font-size: 1.25rem; font-weight: 800; color: #212121; text-align: center; margin-bottom: 4px; }
        .app-sub   { font-size: .85rem; color: #9e9e9e; text-align: center; margin-bottom: 20px; }

        .meta-row { display: flex; justify-content: center; gap: 18px; margin-bottom: 20px; }
        .meta-item { text-align: center; }
        .meta-item .val { font-size: .95rem; font-weight: 700; color: #2e7d32; }
        .meta-item .lbl { font-size: .72rem; color: #9e9e9e; }

        /* Download button states */
        .btn-download {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 15px;
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            color: #fff; border: none; border-radius: 14px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 18px rgba(46,125,50,.32);
            text-decoration: none;
            transition: opacity .2s, transform .1s;
            margin-bottom: 10px;
        }
        .btn-download:hover  { opacity: .92; color: #fff; }
        .btn-download:active { transform: scale(.99); }
        .btn-download .size-badge {
            background: rgba(255,255,255,.22); border-radius: 6px;
            padding: 2px 8px; font-size: .75rem; font-weight: 600;
        }
        .btn-loading, .btn-unavailable {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 15px; border: none; border-radius: 14px;
            font-size: 1rem; font-weight: 700; margin-bottom: 10px;
        }
        .btn-loading     { background: #e8f5e9; color: #2e7d32; }
        .btn-unavailable { background: #e0e0e0; color: #9e9e9e; cursor: not-allowed; }

        /* Build status pill */
        .build-status {
            text-align: center; font-size: .78rem; color: #9e9e9e;
            margin-bottom: 14px;
        }
        .build-status .dot { display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; margin-right: 5px; }
        .dot-green  { background: #43a047; }
        .dot-yellow { background: #fb8c00; }
        .dot-gray   { background: #bdbdbd; }

        .divider { border: none; border-top: 1px solid #f0f0f0; margin: 16px 0; }

        /* Steps */
        .steps-title { font-size: .95rem; font-weight: 700; color: #2e7d32; margin-bottom: 12px; margin-top: 6px; }
        .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .step-num {
            min-width: 28px; height: 28px; border-radius: 50%;
            background: #e8f5e9; color: #2e7d32;
            font-size: .8rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .step-text { font-size: .87rem; color: #424242; line-height: 1.5; }
        .step-text b { color: #212121; }

        /* QR */
        .qr-card {
            background: #fff; border-radius: 22px; padding: 22px;
            box-shadow: 0 8px 40px rgba(46,125,50,.10);
            text-align: center; margin-bottom: 20px;
        }
        #qr-canvas { border-radius: 10px; margin: 0 auto; display: block; }

        .footer-note { text-align: center; font-size: .78rem; color: #9e9e9e; padding-bottom: 10px; }
        .footer-note a { color: #2e7d32; font-weight: 600; text-decoration: none; }

        @media (max-width: 400px) { .card-main { padding: 20px 16px 18px; } }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- Logo -->
    <div class="logo-area">
        <img src="{{ asset('images/logo.png') }}" alt="Alamada Learning Center"
             onerror="this.style.display='none'">
    </div>

    <!-- Main card -->
    <div class="card-main">

        <div class="app-icon">📱</div>
        <div class="app-title">Alamada LGU Parent App</div>
        <div class="app-sub">Mobile Monitoring System &nbsp;·&nbsp; Android</div>

        <!-- Meta row -->
        <div class="meta-row">
            <div class="meta-item">
                <div class="val" id="meta-version">—</div>
                <div class="lbl">Version</div>
            </div>
            <div class="meta-item">
                <div class="val">Android 7+</div>
                <div class="lbl">Requires</div>
            </div>
            <div class="meta-item">
                <div class="val" id="meta-size">—</div>
                <div class="lbl">Size</div>
            </div>
        </div>

        <!-- Build status -->
        <div class="build-status" id="build-status">
            <span class="dot dot-yellow"></span>Checking for latest build…
        </div>

        <!-- Download button — injected by JS -->
        <div id="download-btn-wrap">
            <div class="btn-loading">
                <span class="spinner-border spinner-border-sm" style="width:.9rem;height:.9rem;border-width:.15em"></span>
                Loading…
            </div>
        </div>

        <hr class="divider">

        <!-- Install steps -->
        <div class="steps-title">📋 How to install</div>
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text">Tap <b>Download APK</b> above and wait for it to finish.</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">Open your phone's <b>Downloads</b> folder and tap the file.</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">If prompted, tap <b>"Install anyway"</b> or enable
                <b>"Install from unknown sources"</b> in Settings → Security.</div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-text">Open the app and log in with the account provided
                by the <b>Learning Center</b>.</div>
        </div>

        <hr class="divider">

        <div style="font-size:.8rem; color:#9e9e9e; text-align:center; line-height:1.6">
            Don't have an account? Contact the admin at<br>
            <b style="color:#2e7d32">admin@alamada-lgu.gov.ph</b>
        </div>

    </div><!-- /card-main -->

    <!-- QR code card -->
    <div class="qr-card">
        <div style="font-size:.9rem; font-weight:700; color:#212121; margin-bottom:12px">
            📲 Scan to open this page on your phone
        </div>
        <canvas id="qr-canvas" width="160" height="160"></canvas>
        <p style="font-size:.82rem; color:#616161; margin-top:10px">
            Or share this link:<br>
            <b style="color:#2e7d32; word-break:break-all">{{ url('/download') }}</b>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <a href="{{ route('login') }}">← Staff / Admin Portal</a>
        &nbsp;·&nbsp;
        Alamada LGU Learning Center © {{ date('Y') }}
    </div>

</div><!-- /page-wrap -->

{{-- QR code library (tiny, no external API needed) --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
const PAGE_URL  = '{{ url("/download") }}';

// ── Direct download — APK hosted on this server ──────────────────────────
(function() {
    const btnWrap = document.getElementById('download-btn-wrap');
    const status  = document.getElementById('build-status');
    const apkUrl  = '{{ route("app.install") }}';
    const apkSize = '7.4 MB';

    // Immediately show the download button — no waiting for external API
    status.innerHTML =
        '<span class="dot dot-green"></span>Ready to download';

    btnWrap.innerHTML = `
      <a href="${apkUrl}"
         class="btn-download">
        <i class="fas fa-download" style="font-size:1.1rem"></i>
        Download APK
        <span class="size-badge">${apkSize}</span>
      </a>`;

    document.getElementById('meta-version').textContent = 'v1.0';
    document.getElementById('meta-size').textContent    = apkSize;
})();

// ── Generate QR code for this page ───────────────────────────────────────
new QRCode(document.getElementById('qr-canvas'), {
    text:           PAGE_URL,
    width:          160,
    height:         160,
    colorDark:      '#2e7d32',
    colorLight:     '#ffffff',
    correctLevel:   QRCode.CorrectLevel.M
});
</script>

</body>
</html>
