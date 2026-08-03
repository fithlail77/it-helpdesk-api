<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Helpdesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', system-ui, sans-serif; }
        .login-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; max-width: 420px; width: 100%; }
        .login-header { background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 2rem; text-align: center; color: #fff; }
        .login-header i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; }
        .login-header h4 { margin: 0; font-weight: 700; }
        .login-header small { opacity: 0.75; }
        .login-body { padding: 2rem; background: #fff; }
        .form-control { border-radius: 8px; padding: 0.65rem 1rem; border: 1px solid #e2e8f0; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
        .btn-login { border-radius: 8px; padding: 0.65rem; font-weight: 600; background: linear-gradient(135deg, #1e40af, #3b82f6); border: none; }
        .btn-login:hover { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-headset"></i>
            <h4>IT Helpdesk</h4>
            <small>Sistem Manajemen Tiket Harian</small>
        </div>
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger py-2" style="font-size:0.875rem">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:0.875rem">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="admin@itdesk.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium" style="font-size:0.875rem">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-login btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
