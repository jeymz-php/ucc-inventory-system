<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UCC-IMS | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f5;
               display: flex; align-items: center; justify-content: center;
               min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 16px; padding: 3rem 2.5rem;
                text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.08); max-width: 420px; }
        .icon { font-size: 48px; margin-bottom: 1rem; }
        h2 { color: #1a6b3a; font-size: 22px; margin-bottom: 0.5rem; }
        p  { color: #888; font-size: 14px; margin-bottom: 1.5rem; }
        a  { color: #fff; background: #1a6b3a; padding: 10px 24px;
             border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📦</div>
        <h2>Welcome, {{ auth()->user()->name }}!</h2>
        <p>You're now logged in to UCC-IMS. Dashboard is coming soon.</p>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="color:#fff; background:#1a6b3a; padding:10px 24px;
                    border-radius:8px; border:none; font-size:14px; font-weight:600; cursor:pointer;">
                Logout
            </button>
        </form>
    </div>
</body>
</html>