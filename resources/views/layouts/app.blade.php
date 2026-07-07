<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-IMS | @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        /* ── RESET ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f5; color: #111; min-height: 100vh; display: flex; }

        /* ── CSS VARIABLES ── */
        :root {
            --green-dark:   #1a6b3a;
            --green-mid:    #22883f;
            --green-light:  #f0faf4;
            --green-accent: #6ed694;
            --sidebar-w:    240px;
            --topbar-h:     64px;
            --text-primary: #111;
            --text-secondary: #666;
            --text-muted:   #aaa;
            --border:       #e8ebe9;
            --card-shadow:  0 2px 12px rgba(0,0,0,0.06);
            --red:          #e24b4a;
            --orange:       #ef9f27;
            --blue:         #3b82f6;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #0f3d22;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
        }

        .brand-icon {
            width: 38px; height: 38px;
            background: #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 17px; flex-shrink: 0;
            padding: 4px;
            overflow: hidden;
        }

        .brand-text-main { font-size: 13px; font-weight: 700; color: #fff; line-height: 1.2; }
        .brand-text-sub  { font-size: 10px; color: rgba(255,255,255,0.45); }

        .sidebar-user {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
        }

        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--green-dark);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0; text-transform: uppercase;
        }

        .user-info-name { font-size: 12px; font-weight: 600; color: #fff; line-height: 1.3; }
        .user-info-role {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 1px 7px; border-radius: 10px;
            display: inline-block; margin-top: 2px;
        }
        .role-user       { background: rgba(110,214,148,0.2); color: #6ed694; }
        .role-admin      { background: rgba(59,130,246,0.2);  color: #93c5fd; }
        .role-superadmin { background: rgba(239,159,39,0.2);  color: #fcd34d; }

        .user-campus { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 1px; display: flex; align-items: center; gap: 4px; }

        .sidebar-nav { flex: 1; padding: 0.75rem 0; overflow-y: auto; }

        .nav-section-label {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,255,255,0.3);
            padding: 0.75rem 1.2rem 0.4rem;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 1.2rem;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.18s;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--green-accent);
        }

        .nav-item i { font-size: 17px; flex-shrink: 0; }

        .nav-item.logout {
            color: rgba(226,75,74,0.8);
            margin-top: auto;
        }
        .nav-item.logout:hover { background: rgba(226,75,74,0.1); color: var(--red); }

        .sidebar-footer {
            padding: 0.75rem 1.2rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 40;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .page-title-bar {
            font-size: 16px; font-weight: 700; color: var(--text-primary);
            display: flex; align-items: center; gap: 8px;
        }

        .page-badge {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 3px 8px; border-radius: 6px;
        }

        .badge-admin      { background: rgba(59,130,246,0.1);  color: #3b82f6; }
        .badge-superadmin { background: rgba(239,159,39,0.1);  color: #ef9f27; }
        .badge-user       { background: rgba(26,107,58,0.1);   color: var(--green-dark); }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .topbar-btn {
            width: 36px; height: 36px;
            border-radius: 8px; border: 1px solid var(--border);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-secondary);
            font-size: 17px; transition: all 0.18s; text-decoration: none;
            position: relative;
        }
        .topbar-btn:hover { background: var(--green-light); color: var(--green-dark); border-color: var(--green-dark); }

        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--green-dark);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            cursor: pointer; text-transform: uppercase;
        }

        .datetime-chip { display: flex; gap: 8px; align-items: center; }

        .chip {
            background: var(--green-dark); color: #fff;
            padding: 5px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            flex: 1;
            min-height: 100vh;
        }

        .main-content { padding: 1.5rem; }

        /* ══════════════════════════════════════
           CARDS & SHARED COMPONENTS
        ══════════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
        }

        .card-body { padding: 1.25rem; }
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--green-dark); }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.2rem;
            display: flex; align-items: center; gap: 1rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.15s;
        }
        .stat-card:hover { transform: translateY(-2px); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }

        .stat-icon.green  { background: #f0faf4; color: var(--green-dark); }
        .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
        .stat-icon.orange { background: #fff8f0; color: #ef9f27; }
        .stat-icon.red    { background: #fff5f5; color: var(--red); }

        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 3px; }
        .stat-sub   { font-size: 11px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 4px; }

        .hero-banner {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-mid) 100%);
            border-radius: 12px;
            padding: 1.8rem 2rem;
            color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem;
            position: relative; overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute; top: -40px; right: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .hero-banner::after {
            content: '';
            position: absolute; bottom: -60px; right: 80px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .hero-left { position: relative; z-index: 1; }
        .hero-greeting { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .hero-sub { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 6px; max-width: 480px; line-height: 1.5; }

        .hero-chips { display: flex; gap: 8px; margin-top: 1rem; }
        .hero-chip {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 14px; border-radius: 8px;
            font-size: 12px; font-weight: 600; color: #fff;
        }
        .hero-chip span { color: rgba(255,255,255,0.65); font-weight: 400; margin-right: 4px; }

        .hero-right { position: relative; z-index: 1; }
        .btn-add {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.3);
            color: #fff; padding: 10px 20px;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: background 0.2s;
        }
        .btn-add:hover { background: rgba(255,255,255,0.25); color: #fff; }

        .two-col   { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
        .three-col { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }

        .quick-action {
            display: flex; flex-direction: column; align-items: center;
            gap: 8px; padding: 1rem 0.5rem;
            background: #fff; border-radius: 10px;
            border: 1px solid var(--border);
            text-decoration: none; color: var(--text-secondary);
            font-size: 12px; font-weight: 500; text-align: center;
            transition: all 0.18s; cursor: pointer;
        }
        .quick-action:hover {
            background: var(--green-light);
            border-color: var(--green-dark);
            color: var(--green-dark);
        }
        .quick-action i { font-size: 22px; color: var(--green-dark); }

        .activity-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--border);
        }
        .activity-item:last-child { border-bottom: none; }

        .activity-dot {
            width: 8px; height: 8px; border-radius: 50%;
            margin-top: 5px; flex-shrink: 0;
        }
        .dot-green  { background: var(--green-dark); }
        .dot-orange { background: var(--orange); }
        .dot-red    { background: var(--red); }

        .activity-title { font-size: 13px; font-weight: 500; color: var(--text-primary); }
        .activity-meta  { font-size: 11px; color: var(--text-muted); margin-top: 2px; display: flex; gap: 10px; }

        .empty-state {
            text-align: center; padding: 2.5rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 36px; margin-bottom: 0.75rem; display: block; }
        .empty-state p { font-size: 13px; }

        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px; border-radius: 10px; margin-bottom: 1rem;
            animation: slideDown 0.35s ease;
        }
        .alert-success { background: #f0faf4; border: 1.5px solid var(--green-dark); }
        .alert-success i { color: var(--green-dark); }
        .alert-error { background: #fff5f5; border: 1.5px solid var(--red); }
        .alert-error i { color: var(--red); }
        .alert-text { font-size: 13px; line-height: 1.5; }
        .alert-text strong { display: block; margin-bottom: 2px; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .user-notice {
            background: linear-gradient(135deg, #f0faf4, #e8f5ee);
            border: 1.5px solid #c3e6cd;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            display: flex; align-items: flex-start; gap: 12px;
            margin-bottom: 1.25rem;
        }
        .user-notice i { color: var(--green-dark); font-size: 20px; flex-shrink: 0; margin-top: 1px; }
        .user-notice-text h4 { font-size: 14px; font-weight: 600; color: var(--green-dark); }
        .user-notice-text p  { font-size: 13px; color: #555; margin-top: 3px; line-height: 1.5; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c5d5ca; border-radius: 4px; }

        /* ══════════════════════════════════════
           FILTER PILLS
        ══════════════════════════════════════ */
        .filter-label {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); margin-bottom: 8px;
        }

        .filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }

        .filter-pill {
            padding: 7px 16px;
            border-radius: 20px;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--text-secondary);
            font-size: 12.5px; font-weight: 500;
            cursor: pointer; transition: all 0.15s;
            font-family: 'Inter', sans-serif;
            text-decoration: none; display: inline-block;
        }

        .filter-pill:hover { border-color: var(--green-dark); color: var(--green-dark); }

        .filter-pill.active {
            background: var(--green-dark);
            border-color: var(--green-dark);
            color: #fff; font-weight: 600;
        }

        /* ══════════════════════════════════════
           GLOBAL MODAL SYSTEM
        ══════════════════════════════════════ */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 300;
            align-items: center; justify-content: center; padding: 1rem;
        }
        .modal-overlay.open { display: flex; }

        .modal-box-sm {
            background: #fff; border-radius: 14px; padding: 1.5rem;
            width: 100%; max-width: 420px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
            animation: modalDropIn 0.2s ease;
        }

        .modal-box-lg {
            background: #fff; border-radius: 14px; padding: 1.5rem;
            width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
            animation: modalDropIn 0.2s ease;
        }

        @keyframes modalDropIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-header-row {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 1.25rem;
        }

        .modal-title-sm {
            font-size: 16px; font-weight: 700; color: var(--text-primary);
            display: flex; align-items: center; gap: 8px;
        }
        .modal-title-sm i { color: var(--green-dark); }

        .modal-close {
            width: 28px; height: 28px; border-radius: 6px;
            border: 1px solid var(--border); background: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); font-size: 14px; flex-shrink: 0;
        }
        .modal-close:hover { background: #fff5f5; color: var(--red); border-color: var(--red); }

        .modal-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 0.75rem; margin-bottom: 0.5rem;
        }
        @media(max-width:560px) { .modal-grid { grid-template-columns: 1fr; } }

        .modal-form-group { margin-bottom: 0.85rem; }

        .modal-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: #555; margin-bottom: 5px;
        }

        .modal-input-wrap { position: relative; }

        .modal-input-icon {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: 14px; pointer-events: none;
        }

        .modal-input-right {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: 14px; cursor: pointer;
        }

        .modal-input {
            width: 100%; padding: 10px 12px;
            border: 1.5px solid #e0e0e0; border-radius: 8px;
            font-size: 13px; font-family: 'Inter', sans-serif;
            color: #111; outline: none; transition: border-color 0.2s; background: #fff;
        }
        .modal-input:focus { border-color: var(--green-dark); }

        .modal-input-wrap .modal-input { padding-left: 34px; }
        .modal-input-wrap.has-right .modal-input { padding-right: 38px; }

        .modal-strength-bar { display: flex; gap: 4px; margin-top: 5px; }
        .modal-seg { flex: 1; height: 3px; border-radius: 2px; background: #e0e0e0; transition: background 0.3s; }

        .modal-hint { font-size: 11px; margin-top: 3px; }
        .modal-hint.error   { color: var(--red); }
        .modal-hint.success { color: var(--green-dark); }

        .modal-btn-primary {
            width: 100%; padding: 11px;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Inter', sans-serif;
            display: flex; align-items: center; justify-content: center;
            gap: 8px; margin-top: 1rem;
            transition: background 0.2s, opacity 0.2s;
        }
        .modal-btn-primary:hover { background: #155a30; }

        .btn-back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 16px; border-radius: 8px;
            border: 1.5px solid var(--border); background: #fff;
            color: var(--text-secondary); font-size: 13px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            font-family: 'Inter', sans-serif; transition: all 0.18s;
        }
        .btn-back-link:hover { border-color: var(--green-dark); color: var(--green-dark); }

        .detail-grid { display: flex; flex-direction: column; gap: 6px; }
        .detail-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 6px 0; border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span { color: var(--text-muted); }
        .detail-row strong { color: var(--text-primary); text-align: right; }

        .detail-section { margin-bottom: 1.25rem; }
        .detail-section-title {
            font-size: 12px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 0.6rem; display: flex; align-items: center; gap: 6px;
        }

        /* ══════════════════════════════════════
           SETTINGS DROPDOWN (used in topbar)
        ══════════════════════════════════════ */
        .settings-wrap { position: relative; }

        .settings-dropdown {
            display: none; position: absolute;
            top: calc(100% + 8px); right: 0;
            background: #fff; border: 1px solid var(--border);
            border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            min-width: 220px; z-index: 200; overflow: hidden;
            animation: dropIn 0.18s ease;
        }
        .settings-dropdown.open { display: block; }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .settings-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--green-light);
        }

        .settings-user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .settings-user-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        .settings-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 1rem;
            font-size: 13px; font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: background 0.15s;
            width: 100%; background: none;
            border: none; cursor: pointer;
            font-family: 'Inter', sans-serif;
            text-align: left;
        }
        .settings-item:hover { background: var(--green-light); color: var(--green-dark); }
        .settings-item i { font-size: 16px; color: var(--green-dark); }

        .settings-logout { color: var(--red) !important; }
        .settings-logout i { color: var(--red) !important; }
        .settings-logout:hover { background: #fff5f5 !important; }

        .settings-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ══════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════ */
        .pagination {
            display: flex; align-items: center;
            gap: 4px; list-style: none; flex-wrap: wrap;
        }

        .pagination li { display: flex; }

        .pagination li span,
        .pagination li a {
            display: flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 10px;
            font-size: 13px; font-weight: 500; border-radius: 8px;
            text-decoration: none; color: var(--text-secondary);
            border: 1px solid var(--border); background: #fff;
            transition: all 0.15s;
        }

        .pagination li a:hover {
            background: var(--green-light);
            border-color: var(--green-dark);
            color: var(--green-dark);
        }

        .pagination li.active span {
            background: var(--green-dark);
            border-color: var(--green-dark);
            color: #fff; font-weight: 600;
        }

        .pagination li.disabled span {
            color: var(--text-muted); background: #fafafa; cursor: not-allowed;
        }

        .pagination-wrap {
            display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap;
            gap: 1rem; padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        .pagination-info { font-size: 12.5px; color: var(--text-muted); }

        /* ══════════════════════════════════════
           UNIFIED DATA TABLE STYLES
        ══════════════════════════════════════ */
        .data-table-wrap { overflow-x: auto; }

        .data-table { width: 100%; border-collapse: collapse; }

        .data-table th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--text-muted); background: #fafafa;
            border-bottom: 1px solid var(--border); white-space: nowrap;
        }

        .data-table td {
            padding: 12px 16px; border-bottom: 1px solid var(--border);
            vertical-align: middle; font-size: 13px; color: var(--text-primary);
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafefe; }

        .cell-primary   { font-weight: 600; color: var(--text-primary); font-size: 13px; }
        .cell-secondary { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; line-height: 1.4; }

        .chip-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            padding: 4px 10px; border-radius: 20px; white-space: nowrap;
        }

        .chip-campus    { background: #eff6ff; color: #2563eb; }
        .chip-type      { background: #f4f0ff; color: #7c3aed; }
        .chip-equipment-zero { background: #f5f5f5; color: #999; }
        .chip-equipment-has  { background: #f0faf4; color: var(--green-dark); }
        .chip-status-active   { background: #f0faf4; color: var(--green-dark); }
        .chip-status-inactive { background: #fff5f5; color: var(--red); }
        .chip-dash { color: var(--text-muted); font-size: 12px; }

        .table-actions { display: flex; gap: 6px; align-items: center; }

        .table-icon-btn {
            width: 30px; height: 30px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; border: none; cursor: pointer;
            transition: opacity 0.15s, transform 0.1s; text-decoration: none;
        }
        .table-icon-btn:hover { opacity: 0.8; transform: translateY(-1px); }

        .table-icon-btn.view   { background: #eff6ff; color: #3b82f6; }
        .table-icon-btn.edit   { background: #f4f0ff; color: #7c3aed; }
        .table-icon-btn.delete { background: #fff5f5; color: var(--red); }
        .table-icon-btn.archive{ background: #fff8f0; color: var(--orange); }

        .btn-table-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif; text-decoration: none;
            transition: opacity 0.18s;
        }
        .btn-table-action:hover { opacity: 0.82; }
        .btn-table-action.green { background: var(--green-light); color: var(--green-dark); }

        /* ══════════════════════════════════════
        BUTTON VARIANTS
        ══════════════════════════════════════ */
        .btn-table-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif; text-decoration: none;
            transition: opacity 0.18s;
        }
        .btn-table-action:hover { opacity: 0.82; }
        .btn-table-action.green { background: var(--green-light); color: var(--green-dark); }
        .btn-table-action.orange{ background: #fff8f0; color: #ef9f27; }
        .btn-table-action.red   { background: #fff5f5; color: var(--red); }

        .btn-sm-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 8px; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif; text-decoration: none;
            background: var(--green-light); color: var(--green-dark);
            transition: opacity 0.18s;
        }
        .btn-sm-action:hover  { opacity: 0.82; }
        .btn-sm-action.orange { background: #fff8f0; color: #ef9f27; }
        .btn-sm-action.red    { background: #fff5f5; color: var(--red); }

        .btn-back-link {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 16px; border-radius: 8px;
            border: 1.5px solid var(--border); background: #fff;
            color: var(--text-secondary); font-size: 13px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            font-family: 'Inter', sans-serif; transition: all 0.18s;
        }
        .btn-back-link:hover { border-color: var(--green-dark); color: var(--green-dark); }

        /* ══════════════════════════════════════
        TAB TOGGLE BUTTONS
        ══════════════════════════════════════ */
        .tab-toggle-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 10px;
            border: 1.5px solid var(--border); background: #fff;
            color: var(--text-secondary); font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer; transition: all 0.18s;
            font-family: 'Inter', sans-serif;
        }
        .tab-toggle-btn:hover { border-color: var(--green-dark); color: var(--green-dark); }
        .tab-toggle-btn.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }

        /* ══════════════════════════════════════
        DETAIL GRID (used in view modals)
        ══════════════════════════════════════ */
        .detail-grid { display: flex; flex-direction: column; gap: 6px; }

        .detail-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 6px 0; border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span   { color: var(--text-muted); }
        .detail-row strong { color: var(--text-primary); text-align: right; }

        .detail-section { margin-bottom: 1.25rem; }
        .detail-section-title {
            font-size: 12px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 0.6rem; display: flex; align-items: center; gap: 6px;
        }

        /* ══════════════════════════════════════
        MISC UTILITIES
        ══════════════════════════════════════ */
        .modal-hint-text { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

        .two-col-sidebar { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }

        .capacity-pill {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 12.5px; font-weight: 500; color: var(--text-secondary);
        }
        .capacity-pill i { font-size: 13px; color: var(--text-muted); }

        @keyframes highlightFlash {
            0%   { background: #fff3cd; }
            50%  { background: #fff3cd; }
            100% { background: transparent; }
        }
        .row-highlight-flash td { animation: highlightFlash 3.5s ease-out; }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .three-col  { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar, .main-wrap { left: 0; margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .two-col { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

@include('partials.sidebar')

<div class="main-wrap">
    @include('partials.topbar')

    <div class="main-content">

        @if(session('error'))
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <div class="alert-text"><strong>Access Denied</strong>{{ session('error') }}</div>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            <i class="ti ti-circle-check"></i>
            <div class="alert-text"><strong>Success</strong>{{ session('success') }}</div>
        </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- ══ ONLY basic page JS lives here — notifications/settings/sound are in topbar.blade.php ══ --}}
<script>
// Live clock
function updateClock() {
    const now = new Date();
    const el  = document.getElementById('live-clock');
    if (el) el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

// Mobile sidebar toggle
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

// Settings dropdown toggle (topbar gear icon)
function toggleSettings() {
    const dd = document.getElementById('settings-dropdown');
    if (!dd) return;
    const isOpen = dd.classList.contains('open');
    // Close notif if open
    const nd = document.getElementById('notif-dropdown');
    if (nd) nd.classList.remove('open');
    dd.classList.toggle('open', !isOpen);
}

// Close settings when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('settings-wrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('settings-dropdown');
        if (dd) dd.classList.remove('open');
    }
});

// Change password modal
function openChangePassword() {
    const sd = document.getElementById('settings-dropdown');
    if (sd) sd.classList.remove('open');
    const modal = document.getElementById('change-password-modal');
    if (modal) modal.classList.add('open');
}

function closeChangePassword() {
    const modal = document.getElementById('change-password-modal');
    if (modal) modal.classList.remove('open');
}

function toggleModalPass(id, icon) {
    const inp = document.getElementById(id);
    if (!inp) return;
    if (inp.type === 'password') { inp.type = 'text';     icon.classList.replace('ti-eye','ti-eye-off'); }
    else                         { inp.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
}

function modalStrength() {
    const val  = document.getElementById('new-pass')?.value || '';
    const segs = ['ms1','ms2','ms3','ms4'].map(id => document.getElementById(id)).filter(Boolean);
    let score  = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e24b4a','#ef9f27','#1D9E75','#1a6b3a'];
    segs.forEach((s,i) => s.style.background = i < score ? colors[score-1] : '#e0e0e0');
}

function modalMatch() {
    const pass = document.getElementById('new-pass')?.value  || '';
    const conf = document.getElementById('conf-pass')?.value || '';
    const hint = document.getElementById('conf-pass-hint');
    if (!hint || !conf) return;
    if (pass === conf) { hint.textContent = 'Passwords match.';        hint.className = 'modal-hint success'; }
    else               { hint.textContent = 'Passwords do not match.'; hint.className = 'modal-hint error'; }
}

// Mobile menu visibility
if (window.innerWidth <= 768) { const btn = document.getElementById('menu-btn'); if (btn) btn.style.display = 'flex'; }
window.addEventListener('resize', () => {
    const btn = document.getElementById('menu-btn');
    if (btn) btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
});
</script>

@stack('scripts')
</body>
</html>