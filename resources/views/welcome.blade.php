<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCC-IMS | Inventory Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .landing-wrap {
            display: flex;
            width: 100%;
            max-width: 960px;
            height: 600px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1.1;
            position: relative;
            background: #1a6b3a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            overflow: hidden;
        }

        .left-bg {
            position: absolute;
            inset: 0;
            background: url('{{ asset("images/ucc-background.jpg") }}') center/cover no-repeat;
            opacity: 0.18;
        }

        .left-content {
            position: relative;
            text-align: center;
            color: #fff;
        }

        .ucc-seal {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255,255,255,0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
            font-size: 36px;
            font-weight: 700;
            color: #1a6b3a;
            letter-spacing: -1px;
        }

        .left-est {
            font-size: 10px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.65);
            background: rgba(255,255,255,0.1);
            border: 0.5px solid rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 1.4rem;
            display: inline-block;
        }

        .left-title {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.2;
            color: #fff;
            margin-bottom: 0.3rem;
        }

        .left-title span { color: #6ed694; }

        .left-subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            max-width: 280px;
            margin: 0.85rem auto 0;
        }

        .left-dots {
            position: absolute;
            bottom: 1.4rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
        }

        .dot.active { background: #fff; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            flex: 0.9;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.8rem;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2.5rem;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: #1a6b3a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .brand-name { font-size: 14px; font-weight: 600; color: #111; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: #888; }

        .welcome-title {
            font-size: 26px;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.5rem;
        }

        .welcome-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 280px;
        }

        .action-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #aaa;
            margin-bottom: 0.85rem;
            font-weight: 500;
        }

        .btn-login,
        .btn-register {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s, background 0.15s;
            font-family: 'Inter', sans-serif;
        }

        .btn-login {
            background: #1a6b3a;
            color: #fff;
            border: none;
            margin-bottom: 0.75rem;
        }

        .btn-login:hover  { background: #155a30; transform: translateY(-1px); color: #fff; }

        .btn-register {
            background: transparent;
            color: #1a6b3a;
            border: 1.5px solid #1a6b3a;
        }

        .btn-register:hover { background: #f0faf4; transform: translateY(-1px); }

        .btn-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .btn-login   .btn-icon { background: rgba(255,255,255,0.18); color: #fff; }
        .btn-register .btn-icon { background: rgba(26,107,58,0.1);   color: #1a6b3a; }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ccc;
            font-size: 11px;
            margin: 0.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .footer-note {
            font-size: 11px;
            color: #bbb;
            text-align: center;
            margin-top: 2rem;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .landing-wrap { flex-direction: column; height: auto; }
            .left-panel   { padding: 2rem; min-height: 220px; }
            .right-panel  { padding: 2rem 1.5rem; }
            .left-dots    { display: none; }
        }
    </style>
</head>
<body>

<div class="landing-wrap">

    {{-- LEFT: Campus Hero --}}
    <div class="left-panel">
        <div class="left-bg"></div>
        <div class="left-content">
            <img src="{{ asset('images/ucc.png') }}" alt="UCC Seal" 
                style="width:88px; height:88px; border-radius:10%; 
                        box-shadow: 0 0 0 4px rgba(255,255,255,0.2); margin: 0 auto 1.25rem; display:block;">
            <div class="left-est">Est. 1975 · Caloocan City</div>
            <div class="left-title">
                University of<br>
                <span>Caloocan City</span>
            </div>
            <p class="left-subtitle">
                Track, manage, and monitor university assets — equipment,
                supplies, and resources — all in one unified platform.
            </p>
        </div>
        <div class="left-dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    {{-- RIGHT: Action Panel --}}
    <div class="right-panel">

        <div class="brand-row">
            <div class="brand-icon">
                <i class="ti ti-package"></i>
            </div>
            <div>
                <div class="brand-name">UCC-IMS</div>
                <div class="brand-sub">Inventory Management System</div>
            </div>
        </div>

        <div class="welcome-title">Get Started</div>
        <p class="welcome-desc">
            Access your inventory dashboard or create a new account
            to begin managing university assets.
        </p>

        <div class="action-label">Choose an option</div>

        <a href="{{ route('login') }}" class="btn-login">
            <div class="btn-label">
                <div class="btn-icon"><i class="ti ti-login"></i></div>
                Log in your account
            </div>
            <span>→</span>
        </a>

        <div class="divider">or</div>

        <a href="{{ route('register') }}" class="btn-register">
            <div class="btn-label">
                <div class="btn-icon"><i class="ti ti-user-plus"></i></div>
                Register an account
            </div>
            <span>→</span>
        </a>

        <div class="footer-note">
            © {{ date('Y') }} University of Caloocan City. All rights reserved.
        </div>

    </div>
</div>

</body>
</html>