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
        .note { font-size: 12px; color: #999; margin-top: 24px; }
        .footer { text-align: center; margin-top: 32px; font-size: 11px; color: #bbb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><div class="logo-box">UCC-IMS</div></div>
        <h2>Email Verification</h2>
        <p>Use the code below to verify your email address <strong>{{ $email }}</strong>. This code expires in <strong>10 minutes</strong>.</p>
        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>
        <p>If you did not request this, please ignore this email.</p>
        <p class="note">Do not share this code with anyone.</p>
        <div class="footer">© {{ date('Y') }} University of Caloocan City. All rights reserved.</div>
    </div>
</body>
</html>