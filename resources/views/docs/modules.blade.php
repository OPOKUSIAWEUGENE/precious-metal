<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module Documentation - {{ config('app.name') }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f3f7fc;
            --panel: rgba(255, 255, 255, 0.92);
            --panel-2: rgba(247, 250, 255, 0.96);
            --border: rgba(148, 163, 184, 0.22);
            --text: #0f172a;
            --muted: #4b5563;
            --accent: #7dd3fc;
            --accent-2: #f8a84f;
            --shadow: 0 20px 55px rgba(15, 23, 42, 0.10);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(125, 211, 252, 0.28), transparent 28%),
                radial-gradient(circle at 85% 10%, rgba(248, 168, 79, 0.16), transparent 22%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 48%, #e8eff8 100%);
        }

        a { color: inherit; }

        .shell {
            width: 100%;
            margin: 0;
            padding: 16px;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
            margin-bottom: 18px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(125, 211, 252, 0.16);
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 12px;
            font-weight: 800;
        }

        h1 {
            margin: 10px 0 10px;
            font-size: clamp(34px, 5vw, 62px);
            line-height: 0.96;
            letter-spacing: -0.05em;
            max-width: 12ch;
        }

        .lede {
            max-width: 900px;
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 18px;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            padding: 12px 16px;
            text-decoration: none;
            color: #06111f;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), #d8f7ff);
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .button.secondary {
            background: rgba(15, 23, 42, 0.07);
            color: var(--text);
        }

        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(125, 211, 252, 0.22);
        }

        .layout {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }

        .nav {
            position: sticky;
            top: 16px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
            max-height: calc(100vh - 36px);
            overflow: auto;
        }

        .nav h2 {
            margin: 0 0 12px;
            font-size: 16px;
            letter-spacing: -0.02em;
        }

        .nav a {
            display: block;
            padding: 10px 12px;
            margin-bottom: 8px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--muted);
            background: rgba(15, 23, 42, 0.03);
            border: 1px solid transparent;
            transition: 150ms ease;
        }

        .nav a:hover {
            color: var(--text);
            border-color: rgba(125, 211, 252, 0.28);
            background: rgba(125, 211, 252, 0.10);
        }

        .nav small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            line-height: 1.45;
        }

        .content {
            display: grid;
            gap: 18px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card,
        .module-card,
        .entity-card,
        .endpoint-card,
        .callout {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .summary-card,
        .callout {
            padding: 18px;
        }

        .summary-card strong,
        .callout strong {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .summary-card p,
        .callout p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .module-card {
            padding: 22px;
        }

        .module-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        .module-head h2 {
            margin: 0 0 8px;
            font-size: 26px;
            letter-spacing: -0.03em;
        }

        .module-head p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.06);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .stack {
            display: grid;
            gap: 14px;
        }

        .subhead {
            margin: 18px 0 10px;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .entity-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .entity-card {
            padding: 18px;
        }

        .entity-card h3 {
            margin: 0 0 8px;
            font-size: 18px;
            letter-spacing: -0.02em;
        }

        .entity-card p {
            margin: 0 0 12px;
            color: var(--muted);
            line-height: 1.6;
        }

        .refs {
            display: grid;
            gap: 8px;
        }

        .ref {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            font-size: 13px;
            line-height: 1.5;
        }

        .ref span:first-child {
            min-width: 88px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .endpoint-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .endpoint-table th,
        .endpoint-table td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            vertical-align: top;
        }

        .endpoint-table th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .method {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 78px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .method.get { background: rgba(59, 130, 246, 0.10); color: #2563eb; }
        .method.post { background: rgba(34, 197, 94, 0.10); color: #16a34a; }
        .method.put, .method.patch { background: rgba(234, 179, 8, 0.12); color: #a16207; }
        .method.delete { background: rgba(239, 68, 68, 0.10); color: #dc2626; }

        .endpoint-card {
            padding: 18px;
        }

        .endpoint-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .endpoint-head strong {
            font-size: 17px;
            letter-spacing: -0.02em;
        }

        .endpoint-path {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px;
            color: #1e293b;
            word-break: break-word;
        }

        .endpoint-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .endpoint-note {
            margin-top: 10px;
            padding: 12px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.04);
            color: var(--muted);
            line-height: 1.55;
        }

        .notes {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .subsection {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(148, 163, 184, 0.14);
        }

        .subsection h3 {
            margin: 0 0 8px;
            font-size: 20px;
            letter-spacing: -0.03em;
        }

        .subsection p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .mini-card {
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.8);
        }

        .mini-card h4 {
            margin: 0 0 8px;
            font-size: 17px;
            letter-spacing: -0.02em;
        }

        .mini-card p {
            margin: 0 0 10px;
            color: var(--muted);
            line-height: 1.6;
        }

        .mini-card ul {
            margin: 10px 0 0;
            padding-left: 20px;
            color: var(--muted);
        }

        .mini-card li {
            margin-bottom: 6px;
            line-height: 1.55;
        }

        .notice {
            padding: 14px;
            border-radius: 18px;
            background: rgba(125, 211, 252, 0.10);
            border: 1px solid rgba(125, 211, 252, 0.22);
            color: #0f172a;
            line-height: 1.6;
        }

        code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 0.95em;
        }

        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .nav { position: static; max-height: none; top: auto; }
            .summary { grid-template-columns: 1fr; }
            .entity-grid { grid-template-columns: 1fr; }
            .module-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .topbar { padding: 18px; }
            .module-card { padding: 18px; }
            .endpoint-card { padding: 16px; }
        }
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div>
            <div class="eyebrow">Module documentation</div>
            <h1>What each module does, and which endpoints belong to it</h1>
            <p class="lede">
                This page explains the entities behind the app's module structure in plain language, with the route
                and controller references that back each feature. It is meant to answer questions like:
                "What happens for a platform user in Auth?" and "Which endpoints belong to that flow?"
            </p>
            <div class="top-actions">
                <a class="button" href="{{ route('docs.show', 'platform') }}">Open platform API docs</a>
                <a class="button secondary" href="{{ route('docs.show', 'customer') }}">Open customer API docs</a>
                <a class="button secondary" href="{{ route('docs.index') }}">Back to docs home</a>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="nav">
            <h2>Modules</h2>
            @foreach($modules as $module)
                <a href="#module-{{ \Illuminate\Support\Str::slug($module['key']) }}">
                    {{ $module['title'] }}
                    <small>{{ $module['summary'] }}</small>
                </a>
            @endforeach
        </aside>

        <section class="content">
            <div class="summary">
                <div class="summary-card">
                    <strong>Platform user focus</strong>
                    <p>The internal <code>User</code> model powers admin sign-in, operator management, asset approvals, and the settings that control the platform.</p>
                </div>
                <div class="summary-card">
                    <strong>Customer focus</strong>
                    <p>The <code>Customer</code> model supports self-service login, registration, wallet visibility, transfers, purchases, sales, and withdrawals.</p>
                </div>
            </div>

            @foreach($modules as $module)
                <article class="module-card" id="module-{{ \Illuminate\Support\Str::slug($module['key']) }}">
                    <div class="module-head">
                        <div>
                            <h2>{{ $module['title'] }}</h2>
                            <p>{{ $module['summary'] }}</p>
                        </div>
                        <span class="badge">{{ count($module['auth']['endpoints']) + collect($module['modules'])->sum(fn ($item) => count($item['endpoints'])) }} endpoints</span>
                    </div>

                    <div class="notice">
                        <strong>What this actor does</strong>
                        <ul style="margin: 10px 0 0; padding-left: 20px;">
                            @foreach($module['what_it_does'] as $item)
                                <li style="margin-bottom: 6px;">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if(! empty($module['notes']))
                        <div class="notes">
                            @foreach($module['notes'] as $note)
                                <div class="callout">
                                    <p>{{ $note }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="subsection">
                        <h3>Authentication</h3>
                        <p>{{ $module['auth']['description'] }}</p>
                        <div class="refs" style="margin-top: 12px;">
                            @foreach($module['auth']['references'] as $label => $reference)
                                <div class="ref">
                                    <span>{{ $label }}</span>
                                    <code>{{ $reference }}</code>
                                </div>
                            @endforeach
                        </div>
                        <div class="stack" style="margin-top: 14px;">
                            @foreach($module['auth']['endpoints'] as $endpoint)
                                <div class="endpoint-card">
                                    <div class="endpoint-head">
                                        <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                                        <strong>{{ $endpoint['summary'] }}</strong>
                                    </div>
                                    <div class="endpoint-path">{{ $endpoint['path'] }}</div>
                                    <div class="endpoint-note">
                                        <strong>Controller reference:</strong>
                                        <code>{{ $endpoint['controller'] }}</code>
                                        @if($endpoint['notes'])
                                            <div style="margin-top: 6px;">{{ $endpoint['notes'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="subsection">
                        <h3>Available Modules</h3>
                        @if($module['title'] === 'Customer')
                            <p>For customers, the most important module is the wallets and assets flow. That is what lets them see what they hold and actually do something with it.</p>
                        @else
                            <p>Platform users can reach the operational modules that power administration and asset control.</p>
                        @endif
                        <div class="module-grid">
                            @foreach($module['modules'] as $entity)
                                <div class="mini-card">
                                    <h4>{{ $entity['name'] }}</h4>
                                    <p>{{ $entity['purpose'] }}</p>
                                    <div class="refs">
                                        @foreach($entity['references'] as $label => $reference)
                                            <div class="ref">
                                                <span>{{ $label }}</span>
                                                <code>{{ $reference }}</code>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($entity['endpoints'])
                                        <ul>
                                            @foreach($entity['endpoints'] as $endpoint)
                                                <li>
                                                    <span class="method {{ strtolower($endpoint['method']) }}" style="margin-right: 8px; vertical-align: middle;">{{ $endpoint['method'] }}</span>
                                                    <code>{{ $endpoint['path'] }}</code>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</main>
</body>
</html>
