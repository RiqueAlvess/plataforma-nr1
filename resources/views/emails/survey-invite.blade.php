<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite para Pesquisa de Clima Organizacional</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #312e81, #4338ca); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; }
        .header p  { color: #c7d2fe; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 36px 40px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { background: #4338ca; color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block; }
        .url-fallback { background: #f3f4f6; border-radius: 4px; padding: 12px 16px; font-size: 12px; color: #6b7280; word-break: break-all; margin-top: 16px; }
        .notice { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 0 4px 4px 0; margin-top: 24px; font-size: 13px; color: #1e40af; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Pesquisa de Clima Organizacional</p>
        </div>
        <div class="body">
            <p>Olá,</p>
            <p>
                Você foi convidado(a) a participar da pesquisa de clima organizacional
                <strong>{{ $campaign->nome }}</strong>.
            </p>
            <p>
                Sua participação é anônima e voluntária. As respostas serão tratadas de forma
                agregada, sem identificação individual, em total conformidade com a LGPD.
            </p>
            <p>Clique no botão abaixo para acessar o questionário:</p>

            <div class="btn-wrap">
                <a href="{{ $surveyUrl }}" class="btn">Responder Pesquisa</a>
            </div>

            <div class="url-fallback">
                Ou copie e cole o link no seu navegador:<br>
                {{ $surveyUrl }}
            </div>

            <div class="notice">
                Este link é de uso único e exclusivo para você. Não compartilhe com outras pessoas.
                Após responder, o link deixará de funcionar.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
