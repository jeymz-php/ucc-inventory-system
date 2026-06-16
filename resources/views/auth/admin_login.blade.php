<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-IMS | Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif; min-height: 100vh;
            background: #0f3d22; display: flex;
            align-items: center; justify-content: center;
            padding: 1rem; position: relative;
        }
        .bg { position: fixed; inset: 0;
            background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/University_of_Caloocan_City_-_South_Campus.jpg/1280px-University_of_Caloocan_City_-_South_Campus.jpg') center/cover;
            opacity: 0.1; }

        .card {
            position: relative; z-index: 1;
            background: #fff; border-radius: 16px;
            padding: 2.5rem; width: 100%; max-width: 420px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
        }

        .brand-row { display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; }
        .brand-icon {
            width: 44px; height: 44px; background: #1a6b3a;
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; color: #fff; font-size: 20px;
        }
        .brand-name { font-size: 15px; font-weight: 700; color: #111; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: #888; }

        .admin-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff8f0; border: 1.5px solid #ef9f27;
            color: #b87800; font-size: 11px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 1.2rem;
        }

        .maintenance-notice {
            background: #fff5f5; border: 1.5px solid #e24b4a;
            border-radius: 8px; padding: 10px 14px;
            font-size: 12px; color: #c0392b;
            margin-bottom: 1.2rem;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .maintenance-notice i { flex-shrink: 0; margin-top: 1px; }

        h2 { font-size: 22px; font-weight: 700; color: #111; margin-bottom: 4px; }
        p  { font-size: 13px; color: #888; margin-bottom: 1.5rem; }

        .form-group { margin-bottom: 1rem; }
        .form-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: #555; margin-bottom: 6px;
            display: flex; align-items: center; gap: 6px;
        }
        .form-label i { color: #1a6b3a; font-size: 13px; }

        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 15px; pointer-events: none; }
        .input-right { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 15px; cursor: pointer; }

        .form-control {
            width: 100%; padding: 11px 14px 11px 36px;
            border: 1.5px solid #e0e0e0; border-radius: 8px;
            font-size: 14px; font-family: 'Inter', sans-serif;
            color: #111; outline: none; transition: border-color 0.2s;
        }
        .form-control:focus { border-color: #1a6b3a; }
        .form-control.has-right { padding-right: 40px; }
        .form-control.error { border-color: #e24b4a; }

        .alert-error {
            background: #fff5f5; border: 1.5px solid #e24b4a;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; color: #c0392b; margin-bottom: 1rem;
            display: flex; gap: 8px; align-items: flex-start;
        }

        .btn-login {
            width: 100%; padding: 13px; background: #1a6b3a; color: #fff;
            border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background 0.2s; margin-top: 0.5rem;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #155a30; }

        .back-link { text-align: center; font-size: 13px; color: #888; margin-top: 1rem; }
        .back-link a { color: #1a6b3a; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<div class="bg"></div>
<div class="card">

    <div class="brand-row">
        <div class="brand-icon"><i class="ti ti-shield-lock"></i></div>
        <div>
            <div class="brand-name">UCC-IMS</div>
            <div class="brand-sub">Administrator Access</div>
        </div>
    </div>

    <div class="admin-badge"><i class="ti ti-shield"></i> Admin & Super Admin Only</div>

    @php $isDown = \App\Models\SystemStatus::isDown(); @endphp
    @if($isDown)
    <div class="maintenance-notice">
        <i class="ti ti-alert-triangle"></i>
        System is currently under maintenance. Only administrators can log in.
    </div>
    @endif

    @if($errors->any())
    <div class="alert-error">
        <i class="ti ti-alert-circle"></i>
        {{ $errors->first('email') }}
    </div>
    @endif

    <h2>Administrator Login</h2>
    <p>Sign in with your admin credentials to access the system.</p>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="form-group">
            <div class="form-label"><i class="ti ti-mail"></i> Email Address</div>
            <div class="input-wrap">
                <i class="ti ti-mail input-icon"></i>
                <input type="email" name="email" class="form-control @error('email') error @enderror"
                       placeholder="admin@ucc-caloocan.edu.ph"
                       value="{{ old('email') }}" required autofocus>
            </div>
        </div>
        <div class="form-group">
            <div class="form-label"><i class="ti ti-lock"></i> Password</div>
            <div class="input-wrap">
                <i class="ti ti-lock input-icon"></i>
                <input type="password" name="password" id="pass"
                       class="form-control has-right" required>
                <i class="ti ti-eye input-right" onclick="togglePass()"></i>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="ti ti-login"></i> Sign In as Administrator
        </button>
    </form>

    <div class="back-link">
        Regular user? <a href="{{ route('login') }}">Go to User Login →</a>
    </div>
</div>

<script>
function togglePass() {
    const inp  = document.getElementById('pass');
    const icon = event.target;
    if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('ti-eye','ti-eye-off'); }
    else { inp.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
}
</script>
</body>
</html>