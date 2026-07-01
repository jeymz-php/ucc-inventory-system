<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f4f6f5; padding: 40px 20px; }
        .card { background: #fff; max-width: 480px; margin: 0 auto; border-radius: 12px; padding: 40px; }
        .logo-box { display: inline-flex; background: #1a6b3a; color: #fff; font-weight: 700; font-size: 18px; padding: 10px 20px; border-radius: 8px; }
        .logo { text-align: center; margin-bottom: 24px; }
        h2 { color: #111; font-size: 22px; margin: 0 0 8px; }
        p  { color: #555; font-size: 14px; line-height: 1.6; }
        .success-box { background: #f0faf4; border-left: 3px solid #1a6b3a; padding: 12px 16px; border-radius: 6px; margin: 20px 0; font-size: 13px; color: #1a6b3a; }
        .footer { text-align: center; margin-top: 32px; font-size: 11px; color: #bbb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-box">{{ $user->source === 'cs' ? 'UCC-CS' : 'UCC-IMS' }}</div>
        </div>
        <h2>Account Approved! 🎉</h2>
        <p>Hello, <strong>{{ $user->name }}</strong>!</p>
        <p>Your account has been reviewed and <strong>approved</strong> by an administrator.</p>
        <div class="success-box">
            ✅ You can now log in to the {{ $user->source === 'cs' ? 'UCC Consumable Management System' : 'UCC Inventory Management System' }} using your registered email and password.
        </div>
        <p>If you have any issues logging in, please contact your system administrator.</p>
        <div class="footer">© {{ date('Y') }} University of Caloocan City. All rights reserved.</div>
    </div>
</body>
</html>