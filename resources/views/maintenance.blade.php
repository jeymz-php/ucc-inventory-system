<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCC-IMS | System Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #0f3d22;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; position: relative; overflow: hidden;
        }
        .bg { position: fixed; inset: 0;
            background: url('{{ asset("images/ucc-background.jpg") }}') center/cover;
            opacity: 0.08; }

        .card {
            position: relative; z-index: 1;
            background: #fff; border-radius: 20px;
            padding: 3rem 2.5rem; max-width: 520px; width: 100%;
            text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,0.3);
        }

        .status-icon {
            width: 80px; height: 80px; border-radius: 50%;
            background: #fff5f5; border: 3px solid #e24b4a;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem; font-size: 36px; color: #e24b4a;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(226,75,74,0.3); }
            50%       { box-shadow: 0 0 0 12px rgba(226,75,74,0); }
        }

        .brand { display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 1.5rem; }
        .brand-box {
            background: #1a6b3a; color: #fff;
            font-size: 13px; font-weight: 700;
            padding: 6px 14px; border-radius: 8px;
        }

        h1 { font-size: 26px; font-weight: 700; color: #111; margin-bottom: 0.5rem; }
        .subtitle { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 1.5rem; }

        .reason-box {
            background: #fff8f0; border: 1.5px solid #ef9f27;
            border-radius: 10px; padding: 1rem 1.2rem;
            margin-bottom: 1.5rem; text-align: left;
        }
        .reason-label { font-size: 11px; font-weight: 700; text-transform: uppercase;
                        letter-spacing: 1px; color: #b87800; margin-bottom: 4px; }
        .reason-text  { font-size: 13px; color: #555; line-height: 1.5; }

        .info-row {
            display: flex; gap: 8px; justify-content: center;
            margin-bottom: 1.5rem; flex-wrap: wrap;
        }
        .info-chip {
            background: #f4f6f5; border-radius: 8px;
            padding: 6px 14px; font-size: 12px; color: #555;
            display: flex; align-items: center; gap: 6px;
        }
        .info-chip i { color: #1a6b3a; font-size: 14px; }

        .admin-link {
            display: inline-flex; align-items: center; gap: 8px;
            color: #1a6b3a; font-size: 13px; font-weight: 600;
            text-decoration: none; padding: 10px 20px;
            border: 1.5px solid #1a6b3a; border-radius: 8px;
            transition: all 0.2s;
        }
        .admin-link:hover { background: #1a6b3a; color: #fff; }

        .footer { font-size: 11px; color: #bbb; margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="bg"></div>
<div class="card">
    <div class="brand">
        <div class="brand-box">UCC-IMS</div>
        <span style="font-size:13px; color:#888;">Inventory Management System</span>
    </div>

    <div class="status-icon"><i class="ti ti-tools"></i></div>

    <h1>System Under Maintenance</h1>
    <p class="subtitle">
        We're currently performing maintenance on the UCC Inventory Management System.
        We'll be back online shortly. We apologize for the inconvenience.
    </p>

    @if($status?->reason)
    <div class="reason-box">
        <div class="reason-label"><i class="ti ti-info-circle"></i> Reason</div>
        <div class="reason-text">{{ $status->reason }}</div>
    </div>
    @endif

    <div class="info-row">
        <div class="info-chip">
            <i class="ti ti-clock"></i>
            Since {{ $status?->changed_at?->format('M d, Y h:i A') ?? 'N/A' }}
        </div>
        <div class="info-chip">
            <i class="ti ti-refresh"></i>
            <span id="uptime">Calculating...</span>
        </div>
    </div>

    <a href="{{ route('admin.login') }}" class="admin-link">
        <i class="ti ti-shield-lock"></i> Administrator Login
    </a>

    <div class="footer">© {{ date('Y') }} University of Caloocan City. All rights reserved.</div>
</div>

<script>
const since = new Date('{{ $status?->changed_at?->toISOString() }}');
function updateUptime() {
    const diff = Math.floor((Date.now() - since) / 1000);
    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;
    document.getElementById('uptime').textContent =
        `Down for ${h}h ${m}m ${s}s`;
}
setInterval(updateUptime, 1000);
updateUptime();
</script>
</body>
</html>