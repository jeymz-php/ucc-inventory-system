<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-IMS | Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #1a6b3a;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; position: relative; overflow: hidden;
        }

        .bg-photo {
            position: fixed; inset: 0;
            background: url('{{ asset("images/ucc-background.jpg") }}') center/cover no-repeat;
            opacity: 0.15; z-index: 0;
        }

        .card-wrap {
            position: relative; z-index: 1;
            display: flex; width: 100%; max-width: 900px;
            min-height: 520px; border-radius: 16px;
            overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        }

        /* LEFT */
        .left-panel {
            flex: 0.85; background: rgba(20,90,48,0.92);
            padding: 3rem 2.2rem;
            display: flex; flex-direction: column; justify-content: center; color: #fff;
        }

        .ucc-logo {
            width: 64px; height: 64px; background: rgba(255,255,255,0.92);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 700; color: #1a6b3a; margin-bottom: 1.5rem;
        }

        .left-panel h2 { font-size: 22px; font-weight: 700; line-height: 1.3; margin-bottom: 0.5rem; }
        .left-panel p  { font-size: 13px; color: rgba(255,255,255,0.65); margin-bottom: 2rem; }

        .steps-guide { list-style: none; }
        .steps-guide li {
            display: flex; align-items: flex-start; gap: 12px;
            font-size: 13px; color: rgba(255,255,255,0.8);
            margin-bottom: 1rem;
        }
        .step-num {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0; margin-top: 1px;
        }

        /* RIGHT */
        .right-panel {
            flex: 1; background: #fff;
            padding: 2.8rem 2.5rem;
            display: flex; flex-direction: column; justify-content: center;
        }

        .brand-row {
            display: flex; align-items: center; gap: 10px; margin-bottom: 2rem;
        }
        .brand-icon {
            width: 40px; height: 40px; background: #1a6b3a;
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; color: #fff; font-size: 18px;
        }
        .brand-name { font-size: 14px; font-weight: 600; color: #111; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: #888; }

        .page-title { font-size: 24px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .page-desc  { font-size: 13px; color: #888; margin-bottom: 1.8rem; line-height: 1.6; }

        /* PANELS */
        .panel { display: none; }
        .panel.active { display: block; }

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
            border: 1.5px solid #e0e0e0; border-radius: 8px;
            font-size: 14px; font-family: 'Inter', sans-serif;
            color: #111; background: #fff;
            transition: border-color 0.2s; outline: none;
        }
        .form-control:focus  { border-color: #1a6b3a; }
        .form-control.error  { border-color: #e24b4a; }
        .form-control.has-right { padding-right: 40px; }

        .hint { font-size: 11px; margin-top: 4px; }
        .hint.error   { color: #e24b4a; }
        .hint.success { color: #1a6b3a; }

        /* OTP row */
        .otp-row { display: flex; gap: 8px; }
        .otp-row .input-wrap { flex: 1; }
        .btn-send {
            padding: 11px 16px; background: #1a6b3a; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; white-space: nowrap; font-family: 'Inter', sans-serif;
            transition: background 0.2s; flex-shrink: 0;
        }
        .btn-send:hover    { background: #155a30; }
        .btn-send:disabled { background: #9ec9b0; cursor: not-allowed; }

        /* OTP digits */
        .otp-inputs { display: flex; gap: 8px; margin-top: 1rem; }
        .otp-digit {
            width: 44px; height: 52px; text-align: center;
            font-size: 22px; font-weight: 700;
            border: 2px solid #e0e0e0; border-radius: 8px; outline: none;
            color: #111; font-family: 'Inter', sans-serif; transition: border-color 0.2s;
        }
        .otp-digit:focus  { border-color: #1a6b3a; }
        .otp-digit.filled { border-color: #1a6b3a; }

        .timer { font-size: 12px; color: #888; margin-top: 6px; }
        .timer span { color: #1a6b3a; font-weight: 600; }

        /* Password section (hidden until OTP verified) */
        #password-section { display: none; margin-top: 1.2rem; }
        #password-section.show { display: block; }

        .verified-badge {
            display: none; align-items: center; gap: 8px;
            background: #f0faf4; border: 1.5px solid #1a6b3a;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; color: #1a6b3a; font-weight: 500;
            margin-bottom: 1rem;
        }
        .verified-badge.show { display: flex; }

        /* Strength */
        .strength-bar  { display: flex; gap: 4px; margin-top: 6px; }
        .strength-seg  { flex: 1; height: 3px; border-radius: 2px; background: #e0e0e0; transition: background 0.3s; }
        .strength-label { font-size: 11px; color: #aaa; margin-top: 4px; }

        /* Buttons */
        .btn-primary {
            width: 100%; padding: 13px; background: #1a6b3a; color: #fff;
            border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background 0.2s, transform 0.15s; margin-top: 1rem;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary:hover    { background: #155a30; transform: translateY(-1px); }
        .btn-primary:disabled { background: #9ec9b0; cursor: not-allowed; transform: none; }

        .back-link {
            text-align: center; font-size: 13px; color: #888; margin-top: 1.2rem;
        }
        .back-link a { color: #1a6b3a; font-weight: 600; text-decoration: none; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 100;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }

        .modal-box {
            background: #fff; border-radius: 16px; padding: 2rem;
            width: 100%; max-width: 420px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            text-align: center;
        }

        .modal-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: #f0faf4; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1.2rem;
            font-size: 28px; color: #1a6b3a;
        }

        .modal-title { font-size: 20px; font-weight: 700; color: #111; margin-bottom: 0.5rem; }
        .modal-desc  { font-size: 13px; color: #666; line-height: 1.7; margin-bottom: 1.5rem; }

        .modal-tips {
            background: #f9f9f9; border-radius: 10px;
            padding: 1rem; text-align: left; margin-bottom: 1.5rem;
        }
        .modal-tips p {
            font-size: 12px; color: #555; margin-bottom: 0.5rem;
            display: flex; align-items: flex-start; gap: 8px; line-height: 1.5;
        }
        .modal-tips p i { color: #1a6b3a; font-size: 14px; flex-shrink: 0; margin-top: 1px; }

        .btn-got-it {
            width: 100%; padding: 13px; background: #1a6b3a; color: #fff;
            border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background 0.2s;
        }
        .btn-got-it:hover { background: #155a30; }

        @media (max-width: 640px) {
            .card-wrap { flex-direction: column; }
            .left-panel { padding: 2rem; min-height: 180px; }
            .right-panel { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="bg-photo"></div>

<div class="card-wrap">

    {{-- LEFT --}}
    <div class="left-panel">
        <img src="{{ asset('images/ucc.png') }}" alt="UCC Seal" 
                style="width:88px; height:88px; border-radius:10%; 
                        box-shadow: 0 0 0 4px rgba(255,255,255,0.2); margin-bottom: 1.25rem; display:block;">
        <h2>Reset your<br>Password</h2>
        <p>Follow the steps to securely reset your account password.</p>
        <ul class="steps-guide">
            <li>
                <div class="step-num">1</div>
                Enter your registered UCC-IMS email address.
            </li>
            <li>
                <div class="step-num">2</div>
                Check your email for the 6-digit verification code.
            </li>
            <li>
                <div class="step-num">3</div>
                Enter the code and set your new password.
            </li>
        </ul>
    </div>

    {{-- RIGHT --}}
    <div class="right-panel">

        <div class="brand-row">
            <div class="brand-icon"><i class="ti ti-package"></i></div>
            <div>
                <div class="brand-name">UCC-IMS</div>
                <div class="brand-sub">Inventory Management System</div>
            </div>
        </div>

        <div class="page-title">Forgot Password</div>
        <p class="page-desc">Enter your registered email and we'll send you a verification code to reset your password.</p>

        {{-- EMAIL + OTP --}}
        <div class="form-group">
            <div class="form-label"><i class="ti ti-mail"></i> Email Address</div>
            <div class="otp-row">
                <div class="input-wrap">
                    <i class="ti ti-mail input-icon"></i>
                    <input type="email" class="form-control" id="fp-email"
                           placeholder="your.name@email.com">
                </div>
                <button class="btn-send" id="btn-send-code" onclick="sendCode()">
                    Send Code
                </button>
            </div>
            <div class="hint" id="email-hint"></div>
            <div class="timer" id="fp-timer" style="display:none">
                Resend code in <span id="fp-timer-count">60</span>s
            </div>
        </div>

        {{-- OTP DIGITS --}}
        <div id="otp-section" style="display:none">
            <div class="form-group">
                <div class="form-label"><i class="ti ti-shield-check"></i> Verification Code</div>
                <div class="otp-inputs" id="fp-otp-inputs">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                </div>
                <div class="hint" id="otp-hint"></div>
            </div>

            <button class="btn-primary" onclick="verifyCode()" id="btn-verify-code">
                <i class="ti ti-shield-check"></i> Verify Code
            </button>
        </div>

        {{-- PASSWORD SECTION (shows after OTP verified) --}}
        <div id="password-section">
            <div class="verified-badge show" id="verified-badge">
                <i class="ti ti-circle-check"></i>
                Email verified — now set your new password.
            </div>

            <div class="form-group">
                <div class="form-label"><i class="ti ti-lock"></i> New Password</div>
                <div class="input-wrap has-right">
                    <i class="ti ti-lock input-icon"></i>
                    <input type="password" class="form-control has-right" id="fp-password"
                           placeholder="Minimum 8 characters" oninput="checkStrength()">
                    <i class="ti ti-eye input-right" onclick="togglePass('fp-password', this)"></i>
                </div>
                <div class="strength-bar">
                    <div class="strength-seg" id="s1"></div>
                    <div class="strength-seg" id="s2"></div>
                    <div class="strength-seg" id="s3"></div>
                    <div class="strength-seg" id="s4"></div>
                </div>
                <div class="strength-label" id="strength-label"></div>
            </div>

            <div class="form-group">
                <div class="form-label"><i class="ti ti-lock-check"></i> Confirm New Password</div>
                <div class="input-wrap">
                    <i class="ti ti-lock-check input-icon"></i>
                    <input type="password" class="form-control has-right" id="fp-confirm"
                           placeholder="Re-enter your new password" oninput="checkMatch()">
                    <i class="ti ti-eye input-right" onclick="togglePass('fp-confirm', this)"></i>
                </div>
                <div class="hint" id="confirm-hint"></div>
            </div>

            <button class="btn-primary" onclick="resetPassword()" id="btn-reset">
                <i class="ti ti-lock-check"></i> Reset Password
            </button>
        </div>

        <div class="back-link">
            Remembered your password? <a href="{{ route('login') }}">Sign In →</a>
        </div>

    </div>
</div>

{{-- REMINDER MODAL --}}
<div class="modal-overlay" id="reminder-modal">
    <div class="modal-box">
        <div class="modal-icon"><i class="ti ti-bulb"></i></div>
        <div class="modal-title">Password Reset Successful!</div>
        <div class="modal-desc">
            Your password has been updated. Here are some tips to keep your account secure.
        </div>
        <div class="modal-tips">
            <p><i class="ti ti-key"></i> Use a strong password with uppercase, numbers, and symbols.</p>
            <p><i class="ti ti-device-floppy"></i> Store your password in a secure password manager.</p>
            <p><i class="ti ti-refresh-alert"></i> Don't reuse old passwords across different systems.</p>
            <p><i class="ti ti-eye-off"></i> Never share your password with anyone, including IT staff.</p>
        </div>
        <button class="btn-got-it" onclick="goToLogin()">
            Got it — Sign In Now
        </button>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let verifiedEmail = '';
let verifiedCode  = '';
let timerInterval = null;

// ── SEND CODE ─────────────────────────────────────────────
async function sendCode() {
    const email = document.getElementById('fp-email').value.trim();
    const hint  = document.getElementById('email-hint');
    const btn   = document.getElementById('btn-send-code');

    if (!email) { setHint(hint, 'Please enter your email address.', 'error'); return; }

    btn.disabled    = true;
    btn.textContent = 'Sending...';

    try {
        const res  = await fetch('{{ route("password.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ email })
        });
        const data = await res.json();

        if (!res.ok) {
            setHint(hint, data.errors?.email?.[0] || data.message, 'error');
            btn.disabled    = false;
            btn.textContent = 'Send Code';
            return;
        }

        setHint(hint, 'Code sent! Check your email.', 'success');
        document.getElementById('fp-email').disabled = true;
        document.getElementById('otp-section').style.display = 'block';
        startTimer(btn);
        focusOtp();

    } catch (e) {
        setHint(hint, 'Something went wrong. Try again.', 'error');
        btn.disabled    = false;
        btn.textContent = 'Send Code';
    }
}

function startTimer(btn) {
    let secs = 60;
    const timerEl = document.getElementById('fp-timer');
    const countEl = document.getElementById('fp-timer-count');
    timerEl.style.display = 'block';
    countEl.textContent   = secs;
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        secs--;
        countEl.textContent = secs;
        if (secs <= 0) {
            clearInterval(timerInterval);
            timerEl.style.display = 'none';
            btn.disabled    = false;
            btn.textContent = 'Resend Code';
            document.getElementById('fp-email').disabled = false;
        }
    }, 1000);
}

function focusOtp() {
    const digits = document.querySelectorAll('#fp-otp-inputs .otp-digit');
    digits.forEach((d, i) => {
        d.value = '';
        d.classList.remove('filled');
        d.addEventListener('input', () => {
            d.value = d.value.replace(/\D/g, '');
            if (d.value) { d.classList.add('filled'); if (digits[i+1]) digits[i+1].focus(); }
            else d.classList.remove('filled');
        });
        d.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !d.value && digits[i-1]) digits[i-1].focus();
        });
    });
    digits[0].focus();
}

// ── VERIFY CODE ───────────────────────────────────────────
async function verifyCode() {
    const email  = document.getElementById('fp-email').value.trim();
    const digits = document.querySelectorAll('#fp-otp-inputs .otp-digit');
    const code   = Array.from(digits).map(d => d.value).join('');
    const hint   = document.getElementById('otp-hint');

    if (code.length < 6) { setHint(hint, 'Please enter the full 6-digit code.', 'error'); return; }

    const btn = document.getElementById('btn-verify-code');
    btn.disabled    = true;
    btn.innerHTML   = '<i class="ti ti-loader-2"></i> Verifying...';

    try {
        const res  = await fetch('{{ route("password.verify") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ email, code })
        });
        const data = await res.json();

        if (!res.ok) {
            setHint(hint, data.message, 'error');
            btn.disabled  = false;
            btn.innerHTML = '<i class="ti ti-shield-check"></i> Verify Code';
            return;
        }

        // Code verified — show password section, hide OTP button
        verifiedEmail = email;
        verifiedCode  = code;
        setHint(hint, 'Code verified!', 'success');
        btn.style.display = 'none';

        // Lock OTP digits
        digits.forEach(d => d.disabled = true);

        // Show password fields
        document.getElementById('password-section').classList.add('show');
        document.getElementById('fp-password').focus();

    } catch (e) {
        setHint(hint, 'Something went wrong. Try again.', 'error');
        btn.disabled  = false;
        btn.innerHTML = '<i class="ti ti-shield-check"></i> Verify Code';
    }
}

// ── PASSWORD ──────────────────────────────────────────────
function togglePass(id, icon) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text';     icon.classList.replace('ti-eye', 'ti-eye-off'); }
    else                         { inp.type = 'password'; icon.classList.replace('ti-eye-off', 'ti-eye'); }
}

function checkStrength() {
    const val  = document.getElementById('fp-password').value;
    const segs = ['s1','s2','s3','s4'].map(id => document.getElementById(id));
    const lbl  = document.getElementById('strength-label');
    let score  = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e24b4a','#ef9f27','#1D9E75','#1a6b3a'];
    const labels = ['Weak','Fair','Good','Strong'];
    segs.forEach((s,i) => s.style.background = i < score ? colors[score-1] : '#e0e0e0');
    lbl.textContent = val.length ? (labels[score-1] || '') : '';
    lbl.style.color = score ? colors[score-1] : '#aaa';
}

function checkMatch() {
    const pass    = document.getElementById('fp-password').value;
    const confirm = document.getElementById('fp-confirm').value;
    const hint    = document.getElementById('confirm-hint');
    if (!confirm) { hint.textContent = ''; return; }
    if (pass === confirm) setHint(hint, 'Passwords match.', 'success');
    else                  setHint(hint, 'Passwords do not match.', 'error');
}

// ── RESET ─────────────────────────────────────────────────
async function resetPassword() {
    const pass    = document.getElementById('fp-password').value;
    const confirm = document.getElementById('fp-confirm').value;
    const hint    = document.getElementById('confirm-hint');
    const btn     = document.getElementById('btn-reset');

    if (pass.length < 8)  { setHint(hint, 'Password must be at least 8 characters.', 'error'); return; }
    if (pass !== confirm)  { setHint(hint, 'Passwords do not match.', 'error'); return; }

    btn.disabled  = true;
    btn.innerHTML = '<i class="ti ti-loader-2"></i> Resetting...';

    try {
        const res  = await fetch('{{ route("password.reset") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                email:                 verifiedEmail,
                code:                  verifiedCode,
                password:              pass,
                password_confirmation: confirm,
            })
        });
        const data = await res.json();

        if (!res.ok) {
            setHint(hint, data.message, 'error');
            btn.disabled  = false;
            btn.innerHTML = '<i class="ti ti-lock-check"></i> Reset Password';
            return;
        }

        // Show reminder modal
        document.getElementById('reminder-modal').classList.add('open');

    } catch (e) {
        setHint(hint, 'Something went wrong. Try again.', 'error');
        btn.disabled  = false;
        btn.innerHTML = '<i class="ti ti-lock-check"></i> Reset Password';
    }
}

// ── MODAL ─────────────────────────────────────────────────
function goToLogin() {
    window.location.href = '{{ route("login", ["reset" => true]) }}';
}

// ── HELPER ────────────────────────────────────────────────
function setHint(el, msg, type) {
    el.textContent = msg;
    el.className   = 'hint' + (type ? ' ' + type : '');
}
</script>

</body>
</html>