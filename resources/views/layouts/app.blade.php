<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Helpdesk') - IT Helpdesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: #f0f2f5; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .sidebar { position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; z-index: 1040; transition: transform 0.3s; overflow-y: auto; }
        .sidebar .brand { padding: 1.25rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar .brand h5 { margin: 0; font-weight: 700; font-size: 1.1rem; }
        .sidebar .brand small { color: rgba(255,255,255,0.5); font-size: 0.75rem; }
        .sidebar .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.25rem; color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.9rem; transition: all 0.2s; border-radius: 0; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .sidebar .nav-link.active { color: #fff; background: rgba(59,130,246,0.25); border-left: 3px solid #3b82f6; }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: 1.1rem; }
        .sidebar .nav-section { padding: 1rem 1.25rem 0.4rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); font-weight: 600; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1030; }
        .topbar .page-title { font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0; }
        .content-area { padding: 1.5rem; }
        .stat-card { border: none; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
        .stat-card .stat-label { font-size: 0.8rem; color: #64748b; }
        .badge-status { padding: 0.35em 0.75em; font-weight: 500; font-size: 0.75rem; }
        .table-modern { border-radius: 12px; overflow: hidden; }
        .table-modern thead th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; padding: 0.85rem 1rem; }
        .table-modern tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table-modern tbody tr:hover { background: #f8fafc; }
        .card-modern { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .btn-modern { border-radius: 8px; font-weight: 500; font-size: 0.875rem; padding: 0.5rem 1rem; }
        div.dataTables_wrapper div.dataTables_filter input { border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem; padding: 0.35rem 0.75rem; }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination { margin: 0; gap: 4px; }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-link { border-radius: 6px; border: 1px solid #e2e8f0; color: #475569; font-size: 0.82rem; padding: 0.35rem 0.65rem; min-width: 32px; text-align: center; }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item.active .page-link { background: #3b82f6; border-color: #3b82f6; color: #fff; }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item:not(.active) .page-link:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item.disabled .page-link { color: #cbd5e1; background: transparent; border-color: #e2e8f0; }
        div.dataTables_wrapper div.dataTables_info { font-size: 0.82rem; color: #64748b; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $u = session('web_user'); @endphp

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h5><i class="bi bi-headset me-2"></i>IT Helpdesk</h5>
            <small>Manajemen Tiket</small>
        </div>
        <nav class="mt-3">
            <div class="nav-section">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            @if(($u['role'] ?? '') === 'admin')
            <div class="nav-section">Manajemen</div>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> User
            </a>
            <a href="{{ route('spareparts.index') }}" class="nav-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Sparepart
            </a>
            @endif

            <div class="nav-section">Infrastruktur</div>
            <a href="{{ route('ip-devices.index') }}" class="nav-link {{ request()->routeIs('ip-devices.*') ? 'active' : '' }}">
                <i class="bi bi-router"></i> IP Device
            </a>

            <div class="nav-section">Aktivitas</div>
            <a href="{{ route('activities.index') }}" class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Semua Tiket
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-md-inline" style="font-size:0.85rem">
                    <i class="bi bi-person-circle me-1"></i>{{ $u['name'] ?? 'User' }}
                    <span class="badge bg-secondary ms-1" style="font-size:0.7rem">{{ ucfirst($u['role'] ?? '') }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.datatables').DataTable({
            pageLength: 10,
            lengthChange: false,
            language: {
                search: "Cari:",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: { previous: "&laquo;", next: "&raquo;" }
            }
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
