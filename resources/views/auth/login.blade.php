<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-IMS | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #1a6b3a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        .bg-photo {
            position: fixed; inset: 0;
            background: url('{{ asset("images/ucc-background.jpg") }}') center/cover no-repeat;
            opacity: 0.15;
            z-index: 0;
        }

        .card-wrap {
            position: relative; z-index: 1;
            display: flex;
            width: 100%; max-width: 900px;
            min-height: 520px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        }

        /* LEFT */
        .left-panel {
            flex: 0.85;
            background: rgba(20, 90, 48, 0.92);
            padding: 3rem 2.2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }

        .ucc-logo {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.92);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 700; color: #1a6b3a;
            margin-bottom: 1.5rem;
        }

        .left-panel h2 { font-size: 22px; font-weight: 700; line-height: 1.3; margin-bottom: 0.5rem; }
        .left-panel p  { font-size: 13px; color: rgba(255,255,255,0.65); margin-bottom: 2rem; }

        .feature-list { list-style: none; }
        .feature-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: rgba(255,255,255,0.8);
            margin-bottom: 0.75rem;
        }
        .feature-list li i {
            width: 20px; height: 20px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; color: #6ed694; flex-shrink: 0;
        }

        /* RIGHT */
        .right-panel {
            flex: 1;
            background: #fff;
            padding: 2.8rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* SUCCESS BANNER */
        .alert-success {
            display: flex; align-items: flex-start; gap: 10px;
            background: #f0faf4;
            border: 1.5px solid #1a6b3a;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 1.5rem;
            animation: slideDown 0.4s ease;
        }
        .alert-success i { color: #1a6b3a; font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-success-text { font-size: 13px; color: #1a6b3a; line-height: 1.5; }
        .alert-success-text strong { font-weight: 600; display: block; margin-bottom: 2px; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ERROR BANNER */
        .alert-error {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fff5f5;
            border: 1.5px solid #e24b4a;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 1.5rem;
            animation: slideDown 0.4s ease;
        }
        .alert-error i { color: #e24b4a; font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-error-text { font-size: 13px; color: #c0392b; line-height: 1.5; }
        .alert-error-text strong { font-weight: 600; display: block; margin-bottom: 2px; }

        .brand-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 40px; height: 40px;
            background: #1a6b3a;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
        }

        .brand-name { font-size: 14px; font-weight: 600; color: #111; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: #888; }

        .page-title { font-size: 24px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .page-desc  { font-size: 13px; color: #888; margin-bottom: 1.8rem; }

        .form-group { margin-bottom: 1rem; }

        .form-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: #555; margin-bottom: 6px;
            display: flex; align-items: center; gap: 6px;
        }
        .form-label i { font-size: 13px; color: #1a6b3a; }

        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: 15px; pointer-events: none;
        }
        .input-right {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: 15px; cursor: pointer;
        }

        .form-control {
            width: 100%; padding: 11px 14px 11px 36px;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px; font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #111; background: #fff;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-control:focus { border-color: #1a6b3a; }
        .form-control.has-right { padding-right: 40px; }
        .form-control.error { border-color: #e24b4a; }

        .form-footer {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
            margin-top: 0.25rem;
        }

        .remember-wrap {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #555; cursor: pointer;
        }
        .remember-wrap input { accent-color: #1a6b3a; cursor: pointer; }

        .forgot-link {
            font-size: 13px; color: #1a6b3a;
            font-weight: 600; text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 13px;
            background: #1a6b3a; color: #fff;
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background 0.2s, transform 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #155a30; transform: translateY(-1px); }
        .btn-login:disabled { background: #9ec9b0; cursor: not-allowed; transform: none; }

        .register-link {
            text-align: center; font-size: 13px;
            color: #888; margin-top: 1.2rem;
        }
        .register-link a { color: #1a6b3a; font-weight: 600; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }

        .hint { font-size: 11px; margin-top: 4px; }
        .hint.error { color: #e24b4a; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 300;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.open { display: flex; }

        .modal-box-lg {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
        }

        .modal-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .modal-title-sm {
            font-size: 16px;
            font-weight: 700;
            color: #111;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal-title-sm i { color: #1a6b3a; }
        .modal-close {
            width: 28px; height: 28px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background: #fff;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #999;
            font-size: 14px;
        }
        .modal-close:hover { background: #fff5f5; color: #e24b4a; border-color: #e24b4a; }
    </style>
</head>
<body>

<div class="bg-photo"></div>

<div class="card-wrap">

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <img src="{{ asset('images/ucc.png') }}" alt="UCC Seal" 
                style="width:88px; height:88px; border-radius:10%; 
                        box-shadow: 0 0 0 4px rgba(255,255,255,0.2); margin-bottom: 1.25rem; display:block;">
        <h2>UCC Inventory<br>Management System</h2>
        <p>Your one-stop platform for managing university assets.</p>
        <ul class="feature-list">
            <li><i class="ti ti-check"></i> Track laboratory equipment</li>
            <li><i class="ti ti-check"></i> Manage assignments &amp; returns</li>
            <li><i class="ti ti-check"></i> Receive maintenance alerts</li>
            <li><i class="ti ti-check"></i> Generate reports &amp; analytics</li>
            <li><i class="ti ti-check"></i> Role-based access control</li>
        </ul>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">

        <div class="brand-row">
            <div class="brand-icon"><i class="ti ti-package"></i></div>
            <div>
                <div class="brand-name">UCC-IMS</div>
                <div class="brand-sub">Inventory Management System</div>
            </div>
        </div>

        {{-- SUCCESS BANNER (shown after registration) --}}
        @if(request()->has('registered'))
        <div class="alert-success">
            <i class="ti ti-circle-check"></i>
            <div class="alert-success-text">
                <strong>Account created successfully!</strong>
                You can now log in with your email and password.
            </div>
        </div>
        @endif

        {{-- ERROR BANNER (wrong credentials) --}}
        @if($errors->has('email') || session('error'))
        <div class="alert-error">
            <i class="ti ti-alert-circle"></i>
            <div class="alert-error-text">
                <strong>Login failed</strong>
                {{ $errors->first('email') ?? session('error') }}
            </div>
        </div>
        @endif

        <div class="page-title">Welcome Back</div>
        <p class="page-desc">Sign in to access your inventory dashboard.</p>

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="form-group">
                <div class="form-label"><i class="ti ti-mail"></i> Email Address</div>
                <div class="input-wrap">
                    <i class="ti ti-mail input-icon"></i>
                    <input type="email" name="email" class="form-control @error('email') error @enderror"
                           placeholder="your.email@ucc.edu.ph"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <div class="form-label"><i class="ti ti-lock"></i> Password</div>
                <div class="input-wrap">
                    <i class="ti ti-lock input-icon"></i>
                    <input type="password" name="password" id="password"
                           class="form-control has-right" placeholder="Enter your password" required>
                    <i class="ti ti-eye input-right" id="toggle-pass"
                       onclick="togglePassword()"></i>
                </div>
            </div>

            <div class="form-footer">
                <label class="remember-wrap">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login" id="btn-login">
                <i class="ti ti-login"></i> Sign In
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Register here →</a>
        </div>

    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('toggle-pass');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
    }
}

// Button loading state on submit
document.getElementById('login-form').addEventListener('submit', function () {
    const btn = document.getElementById('btn-login');
    btn.disabled     = true;
    btn.innerHTML    = '<i class="ti ti-loader-2"></i> Signing in...';
});
</script>

@include('partials.developer_modal')

</body>
</html>