<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Previews & Testing - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.5;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        header {
            margin-bottom: 40px;
            text-align: center;
        }
        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }
        .subtitle {
            color: var(--text-muted);
            font-size: 16px;
        }
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 24px;
        }
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .card p {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 24px;
            flex-grow: 1;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        .btn-secondary {
            background-color: white;
            color: var(--text-main);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }
        .send-form {
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }
        .form-group {
            display: flex;
            gap: 8px;
        }
        input[type="email"] {
            flex-grow: 1;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
        }
        input[type="email"]:focus {
            outline: 2px solid var(--primary);
            border-color: transparent;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Email Testing Center</h1>
            <p class="subtitle">Preview templates and send test messages to your inbox</p>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid">
            @foreach($previews as $preview)
                <div class="card">
                    <h3>{{ $preview['name'] }}</h3>
                    <p>{{ $preview['description'] }}</p>
                    
                    <div class="actions">
                        <a href="{{ $preview['preview_url'] }}" target="_blank" class="btn btn-secondary">
                            Live Preview
                        </a>
                    </div>

                    <form action="{{ url('/mail-previews/send') }}" method="POST" class="send-form">
                        @csrf
                        <input type="hidden" name="type" value="{{ $preview['id'] }}">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="recipient@example.com" required>
                            <button type="submit" class="btn btn-primary">Send Test</button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
