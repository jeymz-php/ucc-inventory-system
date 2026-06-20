{{-- Developer Logo Trigger --}}
<button type="button" class="dev-logo-btn" onclick="document.getElementById('developer-modal').classList.add('open');" title="Meet the Developers">
    <i class="ti ti-code"></i>
</button>

{{-- Developer Modal --}}
<div class="modal-overlay" id="developer-modal">
    <div class="modal-box-lg dev-modal-box">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-users"></i> Meet the Developers</div>
            <button class="modal-close" onclick="document.getElementById('developer-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>

        <p class="dev-modal-sub">The team behind the Improvised UCC Inventory Management System.</p>

        <div class="dev-grid">
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/matt.jpg') }}" alt="Ryan Mateo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">RM</div>
                </div>
                <div class="dev-name">Ryan Mateo</div>
                <div class="dev-role">System Analyst / Project Manager</div>
            </div>

            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/greg.png') }}" alt="James Ryan Gregorio" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">JG</div>
                </div>
                <div class="dev-name">James Ryan Gregorio</div>
                <div class="dev-role">Full Stack Developer</div>
            </div>

            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/ureta.png') }}" alt="Jan Ermaine Ureta" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">JU</div>
                </div>
                <div class="dev-name">Jan Ermaine Ureta</div>
                <div class="dev-role">UI / Front-end Developer</div>
            </div>

            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/renz.jpg') }}" alt="Renzel Rodriguez" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">RR</div>
                </div>
                <div class="dev-name">Renzel Rodriguez</div>
                <div class="dev-role">Full Stack Developer</div>
            </div>

            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/ian.jpg') }}" alt="Iankyron Chan" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">IC</div>
                </div>
                <div class="dev-name">Iankyron Chan</div>
                <div class="dev-role">Backend Developer / Database Administrator</div>
            </div>
        </div>

        <div class="dev-footer-note">
            <i class="ti ti-heart" style="color:var(--green-dark, #1a6b3a);"></i>
            Built with dedication for the University of Caloocan City
        </div>
    </div>
</div>

<style>
.dev-logo-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #1a6b3a;
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.25);
    z-index: 50;
    transition: transform 0.2s, background 0.2s;
}
.dev-logo-btn:hover {
    background: #155a30;
    transform: scale(1.08);
}

.dev-modal-box {
    max-width: 720px;
}

.dev-modal-sub {
    font-size: 13px;
    color: #777;
    margin-bottom: 1.5rem;
    margin-top: -0.5rem;
}

.dev-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
@media (max-width: 600px) {
    .dev-grid { grid-template-columns: repeat(2, 1fr); }
}

.dev-card {
    text-align: center;
    padding: 1.25rem 0.75rem;
    border: 1.5px solid #eee;
    border-radius: 14px;
    transition: all 0.2s;
}
.dev-card:hover {
    border-color: #1a6b3a;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    transform: translateY(-2px);
}

.dev-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 0.85rem;
    overflow: hidden;
    border: 3px solid #f0faf4;
    position: relative;
}
.dev-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.dev-avatar-fallback {
    width: 100%; height: 100%;
    background: #1a6b3a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
}

.dev-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #111;
    margin-bottom: 3px;
    line-height: 1.3;
}

.dev-role {
    font-size: 11px;
    color: #1a6b3a;
    font-weight: 500;
    line-height: 1.4;
}

.dev-footer-note {
    text-align: center;
    font-size: 12px;
    color: #999;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
</style>