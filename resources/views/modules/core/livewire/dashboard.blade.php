<div class="p-6" style="font-family: Inter, Arial, sans-serif;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:24px;font-weight:700;">Dashboard</h1>
            <p style="margin:4px 0 0;color:#6b7280;">Bienvenue {{ auth()->user()->full_name ?? auth()->user()->email }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="border:0;background:#dc2626;color:#fff;padding:10px 14px;border-radius:8px;font-weight:600;cursor:pointer;">
                Déconnexion
            </button>
        </form>
    </div>

    <div style="margin-top:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
        <a href="{{ route('sales.invoices') }}" style="text-decoration:none;padding:14px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;color:#111827;">Ventes</a>
        <a href="{{ route('stock.inventory') }}" style="text-decoration:none;padding:14px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;color:#111827;">Stock</a>
        <a href="{{ route('hr.dashboard') }}" style="text-decoration:none;padding:14px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;color:#111827;">RH</a>
        <a href="{{ route('profile') }}" style="text-decoration:none;padding:14px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;color:#111827;">Profil</a>
    </div>
</div>
