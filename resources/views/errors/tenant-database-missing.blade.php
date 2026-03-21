<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço Indisponível - Plataforma NR1</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 2.5rem; max-width: 480px; width: 100%; text-align: center; }
        .icon { width: 64px; height: 64px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .icon svg { color: #dc2626; }
        h1 { font-size: 1.375rem; font-weight: 700; color: #111827; margin-bottom: .5rem; }
        p { font-size: .9375rem; color: #6b7280; line-height: 1.6; margin-bottom: 1rem; }
        .code { font-family: monospace; background: #f3f4f6; padding: .25rem .5rem; border-radius: 4px; font-size: .875rem; color: #374151; }
        a { display: inline-block; margin-top: 1.5rem; padding: .625rem 1.5rem; background: #4f46e5; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: .9375rem; }
        a:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h1>Banco de Dados Indisponível</h1>
        <p>O banco de dados deste tenant ainda não foi criado ou não está disponível no momento.</p>
        @if($tenantId)
            <p>Tenant: <span class="code">{{ $tenantId }}</span></p>
        @endif
        <p style="font-size:.875rem; color:#9ca3af;">Entre em contato com o administrador do sistema para resolver este problema.</p>
        <a href="http://localhost/admin">Ir para o Painel Admin</a>
    </div>
</body>
</html>
