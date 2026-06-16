<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f4f6f5; padding: 40px 20px; }
        .card { background: #fff; max-width: 480px; margin: 0 auto; border-radius: 12px; padding: 40px; }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo-box { display: inline-flex; background: #1a6b3a; color: #fff; font-weight: 700;
                    font-size: 18px; padding: 10px 20px; border-radius: 8px; }
        h2 { color: #111; font-size: 22px; margin: 0 0 8px; }
        p  { color: #555; font-size: 14px; line-height: 1.6; }
        .otp-box { text-align: center; margin: 28px 0; }
        .otp-code { font-size: 42px; font-weight: 700; letter-spacing: 12px;
                    color: #1a6b3a; background: #f0faf4; padding: 16px 32px;
                    border-radius: 10px; display: inline-block; }
        .warning { background: #fff8f0; border-left: 3px solid #ef9f27;
                   padding: 12px 16px; border-radius: 6px; margin-top: 20px;
                   font-size: 13px; color: #7a5500; }
        .footer { text-align: center; margin-top: 32px; font-size: 11px; color: #bbb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><div class="logo-box">UCC-IMS</div></div>
        <h2>Password Reset Request</h2>
        <p>We received a request to reset the password for <strong>{{ $email }}</strong>. Use the code below. It expires in <strong>10 minutes</strong>.</p>
        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>
        <div class="warning">
            ⚠️ If you did not request a password reset, please ignore this email and secure your account immediately.
        </div>
        <div class="footer">© {{ date('Y') }} University of Caloocan City. All rights reserved.</div>
    </div>
</body>
</html>