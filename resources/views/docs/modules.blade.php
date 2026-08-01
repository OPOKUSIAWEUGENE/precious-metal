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
                radial-gradient(circle at 18% 82%, rgba(34, 197, 94, 0.08), transparent 18%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 48%, #e8eff8 100%);
        }

        a { color: inherit; }

        .shell {
            width: 100%;
            margin: 0;
            padding: 16px 16px 52px;
        }

        .topbar {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 26px;
            border: 1px solid var(--border);
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.12);
            margin-bottom: 18px;
        }

        .topbar::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
            background: linear-gradient(180deg, var(--accent-2), var(--accent));
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
            max-width: 13ch;
        }

        .lede {
            max-width: 980px;
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

        .auth-box {
            display: grid;
            gap: 10px;
            min-width: min(360px, 100%);
            margin-top: 2px;
            padding: 16px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
        }

        .auth-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .auth-box small {
            color: var(--muted);
            line-height: 1.55;
        }

        .auth-box input {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            color: var(--text);
            padding: 12px 14px;
            font: inherit;
            outline: none;
        }

        .auth-box input:focus {
            border-color: rgba(125, 211, 252, 0.5);
            box-shadow: 0 0 0 4px rgba(125, 211, 252, 0.14);
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
            grid-template-columns: 312px minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }

        .nav {
            position: sticky;
            top: 16px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            box-shadow: 0 22px 56px rgba(15, 23, 42, 0.10);
            border-left: 6px solid rgba(248, 168, 79, 0.38);
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
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card,
        .audience-card,
        .module-card,
        .endpoint-card,
        .callout,
        .mini-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .summary-card,
        .callout {
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .summary-card strong,
        .callout strong {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .summary-card::before,
        .callout::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--accent), rgba(125, 211, 252, 0));
        }

        .summary-card p,
        .callout p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .audience-card {
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .audience-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, var(--accent-2), rgba(248, 168, 79, 0));
        }

        .audience-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        .audience-head h2 {
            margin: 0 0 8px;
            font-size: 30px;
            letter-spacing: -0.04em;
        }

        .audience-head p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
            max-width: 900px;
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

        .refs {
            display: grid;
            gap: 8px;
            margin-top: 12px;
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

        .module-stack {
            display: grid;
            gap: 14px;
            margin-top: 14px;
        }

        .module-card {
            padding: 18px;
            position: relative;
            overflow: hidden;
            border-radius: 24px;
        }

        .module-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }

        .module-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 8px;
        }

        .module-head h4 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.03em;
        }

        .module-head p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .module-copy {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .module-grid {
            display: grid;
            gap: 14px;
            margin-top: 16px;
        }

        .mini-card {
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .mini-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #22c55e, rgba(34, 197, 94, 0));
        }

        .mini-card h5 {
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

        details.endpoint {
            position: relative;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 22px;
            background: var(--endpoint-bg, rgba(255, 255, 255, 0.84));
            overflow: hidden;
        }

        .module-stack > details.endpoint:nth-child(4n + 1) {
            --endpoint-accent: #7dd3fc;
            --endpoint-bg: rgba(125, 211, 252, 0.12);
        }

        .module-stack > details.endpoint:nth-child(4n + 2) {
            --endpoint-accent: #f8a84f;
            --endpoint-bg: rgba(248, 168, 79, 0.12);
        }

        .module-stack > details.endpoint:nth-child(4n + 3) {
            --endpoint-accent: #22c55e;
            --endpoint-bg: rgba(34, 197, 94, 0.10);
        }

        .module-stack > details.endpoint:nth-child(4n + 4) {
            --endpoint-accent: #a78bfa;
            --endpoint-bg: rgba(167, 139, 250, 0.10);
        }

        details.endpoint::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, var(--endpoint-accent, var(--accent)), rgba(255, 255, 255, 0));
        }

        details.endpoint + details.endpoint {
            margin-top: 12px;
        }

        details.endpoint summary {
            cursor: pointer;
            list-style: none;
            padding: 16px 18px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        details.endpoint summary::-webkit-details-marker {
            display: none;
        }

        details.endpoint[open] summary {
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            background: linear-gradient(90deg, var(--endpoint-bg, rgba(125, 211, 252, 0.10)), rgba(255, 255, 255, 0.10));
        }

        .endpoint-path {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.03em;
            word-break: break-word;
        }

        .endpoint-summary-text {
            color: var(--muted);
            flex-basis: 100%;
            line-height: 1.5;
            margin-top: 2px;
        }

        .endpoint-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
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

        .endpoint-body {
            padding: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), var(--endpoint-bg, rgba(247, 250, 255, 0.96)));
        }

        .endpoint-description {
            color: var(--muted);
            line-height: 1.65;
            margin: 0 0 14px;
        }

        .endpoint-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(380px, 0.9fr);
            gap: 18px;
            align-items: start;
            margin-top: 14px;
        }

        .endpoint-details {
            min-width: 0;
            display: grid;
            gap: 14px;
        }

        .endpoint-tester {
            position: sticky;
            top: 18px;
            padding: 18px;
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10);
        }

        .tester {
            height: 100%;
        }

        .tester-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .tester-head h4 {
            margin: 0;
            font-size: 16px;
        }

        .block {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(255, 255, 255, 0.03);
            padding: 14px;
        }

        .block h4 {
            margin: 0 0 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #d5def0;
        }

        .params, .payload-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .params th, .params td,
        .payload-table th, .payload-table td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            vertical-align: top;
        }

        .params th, .payload-table th {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        pre {
            margin: 0;
            overflow: auto;
            padding: 14px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #0f172a;
            line-height: 1.6;
            font-size: 13px;
        }

        .request-json,
        .response-json {
            margin-top: 12px;
            padding: 16px;
            border-radius: 18px;
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #f8fafc;
            width: 100%;
        }

        .request-json .notice,
        .response-json .notice,
        .endpoint-note {
            color: #cbd5e1;
        }

        .request-json pre,
        .response-json pre {
            background: #0b1220;
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            width: 100%;
        }

        .endpoint-note {
            margin-top: 10px;
            padding: 12px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.04);
            color: var(--muted);
            line-height: 1.55;
        }

        .endpoint-note strong {
            color: var(--text);
        }

        .field-type {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            color: #1d4ed8;
        }

        .field-required {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 54px;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(15, 23, 42, 0.06);
        }

        .field,
        .form-grid {
            display: grid;
            gap: 8px;
        }

        .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .field label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--text);
            padding: 12px 14px;
            font: inherit;
            outline: none;
        }

        .field textarea {
            min-height: 170px;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        .field textarea[data-role="output"] {
            min-height: 240px;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: rgba(125, 211, 252, 0.5);
            box-shadow: 0 0 0 4px rgba(125, 211, 252, 0.14);
        }

        .span-2 { grid-column: span 2; }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        button {
            border: 0;
            border-radius: 14px;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--accent), #d8f7ff);
            color: #07111f;
            font-weight: 900;
            cursor: pointer;
            transition: transform 150ms ease, box-shadow 150ms ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(125, 211, 252, 0.22);
        }

        .secondary {
            background: rgba(15, 23, 42, 0.07);
            color: var(--text);
        }

        .notice {
            color: var(--muted);
            margin: 10px 0 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .empty-state {
            padding: 16px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.04);
            color: var(--muted);
            line-height: 1.6;
        }

        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .nav { position: static; max-height: none; top: auto; }
            .summary { grid-template-columns: 1fr; }
            .endpoint-layout { grid-template-columns: 1fr; }
            .endpoint-tester { position: static; }
        }

        @media (max-width: 760px) {
            .topbar { padding: 18px; }
            .audience-card { padding: 18px; }
            .module-card { padding: 16px; }
            .endpoint-body { padding: 14px; }
            .form-grid { grid-template-columns: 1fr; }
            .span-2 { grid-column: span 1; }
        }
    </style>
</head>
<body>
@php
    $pretty = static fn ($value) => $value === null
        ? 'No payload documented.'
        : json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $inlineValue = static fn ($value) => $value === null
        ? '—'
        : (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
@endphp
<main class="shell">
    <header class="topbar">
        <div>
            <div class="eyebrow">Module documentation</div>
            <h1>Audience-based module docs with expandable endpoints</h1>
            <p class="lede">
                This page is grouped by audience first, then by module. Each module occupies a full-width row, and
                each endpoint can be expanded to reveal request payloads, response payloads, and the live test kit.
            </p>
            <div class="top-actions">
                <a class="button" href="{{ route('docs.show', 'platform') }}">Open platform API docs</a>
                <a class="button secondary" href="{{ route('docs.show', 'customer') }}">Open customer API docs</a>
                <a class="button secondary" href="{{ route('docs.index') }}">Back to docs home</a>
            </div>
        </div>
        <div class="auth-box">
            <strong>Shared API token</strong>
            <small>
                Paste a bearer token here once and it will be used by every tester on this page.
                The value is stored in your browser for the current session.
            </small>
            <input id="global-token" type="text" placeholder="Bearer token">
        </div>
    </header>

    <div class="layout">
        <aside class="nav">
            <h2>Audience</h2>
            @foreach($audiences as $audience)
                <a href="#audience-{{ \Illuminate\Support\Str::slug($audience['key']) }}">
                    {{ $audience['title'] }}
                    <small>{{ $audience['summary'] }}</small>
                </a>
            @endforeach
        </aside>

        <section class="content">
            <div class="summary">
                <div class="summary-card">
                    <strong>Platform user</strong>
                    <p>Internal operators who manage users, customers, assets, rates, fees, and approvals.</p>
                </div>
                <div class="summary-card">
                    <strong>Customer</strong>
                    <p>External users who authenticate themselves and use wallets, balances, quotes, transfers, sales, purchases, and withdrawals.</p>
                </div>
            </div>

            @foreach($audiences as $audience)
                <article class="audience-card" id="audience-{{ \Illuminate\Support\Str::slug($audience['key']) }}">
                    <div class="audience-head">
                        <div>
                            <h2>{{ $audience['title'] }}</h2>
                            <p>{{ $audience['summary'] }}</p>
                        </div>
                        <span class="badge">{{ count($audience['modules']) }} modules</span>
                    </div>

                    <div class="subsection">
                        <h3>What this audience does</h3>
                        <ul style="margin: 10px 0 0; padding-left: 20px; color: var(--muted);">
                            @foreach($audience['what_it_does'] as $item)
                                <li style="margin-bottom: 6px; line-height: 1.55;">{{ $item }}</li>
                            @endforeach
                        </ul>
                        <div class="refs">
                            @foreach($audience['references'] as $label => $reference)
                                <div class="ref">
                                    <span>{{ $label }}</span>
                                    <code>{{ $reference }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if(! empty($audience['auth']))
                        <div class="subsection">
                            <h3>Authentication</h3>
                            <p>{{ $audience['auth']['description'] }}</p>
                            <div class="refs">
                                @foreach($audience['auth']['references'] as $label => $reference)
                                    <div class="ref">
                                        <span>{{ $label }}</span>
                                        <code>{{ $reference }}</code>
                                    </div>
                                @endforeach
                            </div>

                            <div class="module-stack">
                                @foreach($audience['auth']['endpoints'] as $endpoint)
                                    <details class="endpoint" id="{{ $endpoint['id'] }}">
                                        <summary>
                                            <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                                            <div class="endpoint-path">{{ $endpoint['path'] }}</div>
                                            <div class="endpoint-meta">
                                                <span class="badge">{{ $endpoint['secured'] ? 'Sanctum protected' : 'Public' }}</span>
                                                @if($endpoint['docs_title'])
                                                    <span class="badge">{{ $endpoint['docs_title'] }}</span>
                                                @endif
                                                @if($endpoint['operation_id'])
                                                    <span class="badge">Op: {{ $endpoint['operation_id'] }}</span>
                                                @endif
                                            </div>
                                            <div class="endpoint-summary-text">{{ $endpoint['summary'] }}</div>
                                        </summary>

                                        <div class="endpoint-body">
                                            @if($endpoint['description'])
                                                <p class="endpoint-description">{{ $endpoint['description'] }}</p>
                                            @elseif($endpoint['notes'])
                                                <p class="endpoint-description">{{ $endpoint['notes'] }}</p>
                                            @endif

                                            <div class="endpoint-layout">
                                                <div class="endpoint-details">
                                                    @if($endpoint['parameters'])
                                                        <div class="block">
                                                            <h4>Parameters</h4>
                                                            <table class="params">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Name</th>
                                                                        <th>Location</th>
                                                                        <th>Required</th>
                                                                        <th>Description</th>
                                                                        <th>Example</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($endpoint['parameters'] as $parameter)
                                                                        <tr>
                                                                            <td>{{ $parameter['name'] }}</td>
                                                                            <td>{{ $parameter['in'] }}</td>
                                                                            <td>{{ $parameter['required'] ? 'Yes' : 'No' }}</td>
                                                                            <td>{{ $parameter['description'] ?: 'No description provided.' }}</td>
                                                                            <td><code>{{ $inlineValue($parameter['example']) }}</code></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif

                                                    <div class="block">
                                                        <h4>Request payload</h4>
                                                        @if($endpoint['request'])
                                                            <div class="notice">Content type: <code>{{ $endpoint['request']['content_type'] }}</code></div>
                                                            <div class="notice">Required: {{ $endpoint['request']['required'] ? 'Yes' : 'No' }}</div>
                                                            <div class="request-json">
                                                                <div class="notice">JSON body</div>
                                                                <pre style="margin-top: 10px;">{{ $pretty($endpoint['request']['example']) }}</pre>
                                                            </div>
                                                            <div style="margin-top: 10px;">
                                                                <table class="payload-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Field</th>
                                                                            <th>Type</th>
                                                                            <th>Required</th>
                                                                            <th>Description</th>
                                                                            <th>Example</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($endpoint['request']['fields'] as $field)
                                                                            <tr>
                                                                                <td><code>{{ $field['name'] }}</code></td>
                                                                                <td><span class="field-type">{{ $field['type'] }}</span></td>
                                                                                <td><span class="field-required">{{ $field['required'] ? 'Yes' : 'No' }}</span></td>
                                                                                <td>{{ $field['description'] ?: 'No description provided.' }}</td>
                                                                                <td><code>{{ $inlineValue($field['example']) }}</code></td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="5">No request fields documented.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <p class="notice">This endpoint does not define a JSON request body.</p>
                                                        @endif
                                                    </div>

                                                    <div class="block">
                                                        <h4>Response payloads</h4>
                                                        @forelse($endpoint['responses'] as $response)
                                                            <div class="badge" style="margin-bottom: 10px;">HTTP {{ $response['status'] }}</div>
                                                            <div class="notice">{{ $response['description'] ?: 'No description provided.' }}</div>
                                                            @if($response['content_type'])
                                                                <div class="notice">Content type: <code>{{ $response['content_type'] }}</code></div>
                                                            @endif
                                                            <div class="response-json">
                                                                <div class="notice">JSON body</div>
                                                                <pre style="margin-top: 10px;">{{ $pretty($response['body']) }}</pre>
                                                            </div>
                                                            <div style="margin-top: 10px;">
                                                                <table class="payload-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Field</th>
                                                                            <th>Type</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($response['fields'] as $field)
                                                                            <tr>
                                                                                <td><code>{{ $field['name'] }}</code></td>
                                                                                <td><span class="field-type">{{ $field['type'] }}</span></td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="2">No response fields documented.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if($response['schema'])
                                                                <div style="margin-top: 10px;">
                                                                    <details>
                                                                        <summary class="notice" style="cursor:pointer;">View response schema</summary>
                                                                        <pre style="margin-top: 10px;">{{ $pretty($response['schema']) }}</pre>
                                                                    </details>
                                                                </div>
                                                            @endif
                                                            @if(! $loop->last)
                                                                <div style="height: 14px;"></div>
                                                            @endif
                                                        @empty
                                                            <p class="notice">No response payloads documented.</p>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <form class="tester js-tester endpoint-tester" data-base-url="{{ $audience['base_url'] }}" data-method="{{ strtolower($endpoint['method']) }}" data-path="{{ $endpoint['path'] }}" data-has-body="{{ $endpoint['request'] ? '1' : '0' }}">
                                                    <div class="tester">
                                                        <div class="tester-head">
                                                            <h4>API testing</h4>
                                                            <span class="badge">Live request builder</span>
                                                        </div>

                                                        @php
                                                            $pathParams = collect($endpoint['parameters'])->where('in', 'path')->values();
                                                            $queryParams = collect($endpoint['parameters'])->where('in', 'query')->values();
                                                        @endphp

                                                        <div class="form-grid">
                                                            @foreach($pathParams as $parameter)
                                                                <div class="field">
                                                                    <label>{{ $parameter['name'] }} path parameter</label>
                                                                    <input type="text" name="path[{{ $parameter['name'] }}]" data-kind="path" value="{{ is_scalar($parameter['example']) ? $parameter['example'] : '' }}" placeholder="{{ $parameter['name'] }}">
                                                                </div>
                                                            @endforeach

                                                            @foreach($queryParams as $parameter)
                                                                <div class="field">
                                                                    <label>{{ $parameter['name'] }} query parameter</label>
                                                                    <input type="text" name="query[{{ $parameter['name'] }}]" data-kind="query" value="{{ is_scalar($parameter['example']) ? $parameter['example'] : '' }}" placeholder="{{ $parameter['name'] }}">
                                                                </div>
                                                            @endforeach

                                                            @if($endpoint['request'])
                                                                <div class="field span-2">
                                                                    <label>JSON body</label>
                                                                    <textarea name="body" data-kind="body">{{ $pretty($endpoint['request']['example']) }}</textarea>
                                                                </div>
                                                            @endif

                                                            <div class="field span-2">
                                                                <label>Response output</label>
                                                                <textarea readonly data-role="output" placeholder="The response from the API will appear here."></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="btn-row">
                                                            <button type="submit">Send request</button>
                                                            <button type="button" class="secondary" data-action="reset">Reset example</button>
                                                        </div>

                                                        <p class="notice">
                                                            The request is sent relative to <code>{{ $audience['base_url'] }}</code> so the tester works against the current app host.
                                                        </p>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="subsection">
                        <h3>Modules</h3>
                        <p>Each row below is a module the audience can use or manage. Open an endpoint to inspect payloads and test it live.</p>

                        <div class="module-grid">
                            @foreach($audience['modules'] as $module)
                                <article class="module-card">
                                    <div class="module-head">
                                        <div>
                                            <h4>{{ $module['name'] }}</h4>
                                            <p>{{ $module['purpose'] }}</p>
                                        </div>
                                        <span class="badge">{{ count($module['endpoints']) }} endpoints</span>
                                    </div>

                                    <div class="refs">
                                        @foreach($module['references'] as $label => $reference)
                                            <div class="ref">
                                                <span>{{ $label }}</span>
                                                <code>{{ $reference }}</code>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($module['endpoints'])
                                        <div class="module-stack">
                                            @foreach($module['endpoints'] as $endpoint)
                                                <details class="endpoint" id="{{ $endpoint['id'] }}">
                                                    <summary>
                                                        <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                                                        <div class="endpoint-path">{{ $endpoint['path'] }}</div>
                                                        <div class="endpoint-meta">
                                                            <span class="badge">{{ $endpoint['secured'] ? 'Sanctum protected' : 'Public' }}</span>
                                                            @if($endpoint['docs_title'])
                                                                <span class="badge">{{ $endpoint['docs_title'] }}</span>
                                                            @endif
                                                            @if($endpoint['operation_id'])
                                                                <span class="badge">Op: {{ $endpoint['operation_id'] }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="endpoint-summary-text">{{ $endpoint['summary'] }}</div>
                                                    </summary>

                                                    <div class="endpoint-body">
                                                        @if($endpoint['description'])
                                                            <p class="endpoint-description">{{ $endpoint['description'] }}</p>
                                                        @elseif($endpoint['notes'])
                                                            <p class="endpoint-description">{{ $endpoint['notes'] }}</p>
                                                        @endif

                                                        <div class="endpoint-layout">
                                                            <div class="endpoint-details">
                                                                @if($endpoint['parameters'])
                                                                    <div class="block">
                                                                        <h4>Parameters</h4>
                                                                        <table class="params">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Name</th>
                                                                                    <th>Location</th>
                                                                                    <th>Required</th>
                                                                                    <th>Description</th>
                                                                                    <th>Example</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($endpoint['parameters'] as $parameter)
                                                                                    <tr>
                                                                                        <td>{{ $parameter['name'] }}</td>
                                                                                        <td>{{ $parameter['in'] }}</td>
                                                                                        <td>{{ $parameter['required'] ? 'Yes' : 'No' }}</td>
                                                                                        <td>{{ $parameter['description'] ?: 'No description provided.' }}</td>
                                                                                        <td><code>{{ $inlineValue($parameter['example']) }}</code></td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @endif

                                                                <div class="block">
                                                                    <h4>Request payload</h4>
                                                                    @if($endpoint['request'])
                                                                        <div class="notice">Content type: <code>{{ $endpoint['request']['content_type'] }}</code></div>
                                                                        <div class="notice">Required: {{ $endpoint['request']['required'] ? 'Yes' : 'No' }}</div>
                                                                        <div class="request-json">
                                                                            <div class="notice">JSON body</div>
                                                                            <pre style="margin-top: 10px;">{{ $pretty($endpoint['request']['example']) }}</pre>
                                                                        </div>
                                                                        <div style="margin-top: 10px;">
                                                                            <table class="payload-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Field</th>
                                                                                        <th>Type</th>
                                                                                        <th>Required</th>
                                                                                        <th>Description</th>
                                                                                        <th>Example</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @forelse($endpoint['request']['fields'] as $field)
                                                                                        <tr>
                                                                                            <td><code>{{ $field['name'] }}</code></td>
                                                                                            <td><span class="field-type">{{ $field['type'] }}</span></td>
                                                                                            <td><span class="field-required">{{ $field['required'] ? 'Yes' : 'No' }}</span></td>
                                                                                            <td>{{ $field['description'] ?: 'No description provided.' }}</td>
                                                                                            <td><code>{{ $inlineValue($field['example']) }}</code></td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr>
                                                                                            <td colspan="5">No request fields documented.</td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @else
                                                                        <p class="notice">This endpoint does not define a JSON request body.</p>
                                                                    @endif
                                                                </div>

                                                                <div class="block">
                                                                    <h4>Response payloads</h4>
                                                                    @forelse($endpoint['responses'] as $response)
                                                                        <div class="badge" style="margin-bottom: 10px;">HTTP {{ $response['status'] }}</div>
                                                                        <div class="notice">{{ $response['description'] ?: 'No description provided.' }}</div>
                                                                        @if($response['content_type'])
                                                                            <div class="notice">Content type: <code>{{ $response['content_type'] }}</code></div>
                                                                        @endif
                                                                        <div class="response-json">
                                                                            <div class="notice">JSON body</div>
                                                                            <pre style="margin-top: 10px;">{{ $pretty($response['body']) }}</pre>
                                                                        </div>
                                                                        <div style="margin-top: 10px;">
                                                                            <table class="payload-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Field</th>
                                                                                        <th>Type</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @forelse($response['fields'] as $field)
                                                                                        <tr>
                                                                                            <td><code>{{ $field['name'] }}</code></td>
                                                                                            <td><span class="field-type">{{ $field['type'] }}</span></td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr>
                                                                                            <td colspan="2">No response fields documented.</td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        @if($response['schema'])
                                                                            <div style="margin-top: 10px;">
                                                                                <details>
                                                                                    <summary class="notice" style="cursor:pointer;">View response schema</summary>
                                                                                    <pre style="margin-top: 10px;">{{ $pretty($response['schema']) }}</pre>
                                                                                </details>
                                                                            </div>
                                                                        @endif
                                                                        @if(! $loop->last)
                                                                            <div style="height: 14px;"></div>
                                                                        @endif
                                                                    @empty
                                                                        <p class="notice">No response payloads documented.</p>
                                                                    @endforelse
                                                                </div>
                                                            </div>

                                                            <form class="tester js-tester endpoint-tester" data-base-url="{{ $audience['base_url'] }}" data-method="{{ strtolower($endpoint['method']) }}" data-path="{{ $endpoint['path'] }}" data-has-body="{{ $endpoint['request'] ? '1' : '0' }}">
                                                                <div class="tester">
                                                                    <div class="tester-head">
                                                                        <h4>API testing</h4>
                                                                        <span class="badge">Live request builder</span>
                                                                    </div>

                                                                    @php
                                                                        $pathParams = collect($endpoint['parameters'])->where('in', 'path')->values();
                                                                        $queryParams = collect($endpoint['parameters'])->where('in', 'query')->values();
                                                                    @endphp

                                                                    <div class="form-grid">
                                                                        @foreach($pathParams as $parameter)
                                                                            <div class="field">
                                                                                <label>{{ $parameter['name'] }} path parameter</label>
                                                                                <input type="text" name="path[{{ $parameter['name'] }}]" data-kind="path" value="{{ is_scalar($parameter['example']) ? $parameter['example'] : '' }}" placeholder="{{ $parameter['name'] }}">
                                                                            </div>
                                                                        @endforeach

                                                                        @foreach($queryParams as $parameter)
                                                                            <div class="field">
                                                                                <label>{{ $parameter['name'] }} query parameter</label>
                                                                                <input type="text" name="query[{{ $parameter['name'] }}]" data-kind="query" value="{{ is_scalar($parameter['example']) ? $parameter['example'] : '' }}" placeholder="{{ $parameter['name'] }}">
                                                                            </div>
                                                                        @endforeach

                                                                        @if($endpoint['request'])
                                                                            <div class="field span-2">
                                                                                <label>JSON body</label>
                                                                                <textarea name="body" data-kind="body">{{ $pretty($endpoint['request']['example']) }}</textarea>
                                                                            </div>
                                                                        @endif

                                                                        <div class="field span-2">
                                                                            <label>Response output</label>
                                                                            <textarea readonly data-role="output" placeholder="The response from the API will appear here."></textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="btn-row">
                                                                        <button type="submit">Send request</button>
                                                                        <button type="button" class="secondary" data-action="reset">Reset example</button>
                                                                    </div>

                                                                    <p class="notice">
                                                                        The request is sent relative to <code>{{ $audience['base_url'] }}</code> so the tester works against the current app host.
                                                                    </p>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </details>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="empty-state">This module has no public HTTP endpoints. It is used internally by services and other module flows.</div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</main>

<script>
    const storageKey = 'api-docs-global-token';
    const globalToken = document.getElementById('global-token');

    if (globalToken) {
        globalToken.value = localStorage.getItem(storageKey) || '';
        globalToken.addEventListener('input', () => {
            localStorage.setItem(storageKey, globalToken.value);
        });
    }

    const jsonPretty = (value) => {
        if (value === null || value === undefined || value === '') {
            return '';
        }

        if (typeof value === 'string') {
            return value;
        }

        return JSON.stringify(value, null, 2);
    };

    const applyPathParams = (template, form) => {
        return template.replace(/\{([^}]+)\}/g, (_, name) => {
            const input = form.querySelector(`[name="path[${name}]"]`);
            return encodeURIComponent(input?.value || '');
        });
    };

    const collectQueryParams = (form) => {
        const search = new URLSearchParams();
        form.querySelectorAll('[data-kind="query"]').forEach((input) => {
            if (input.value !== '') {
                search.set(input.name.replace(/^query\[(.+)\]$/, '$1'), input.value);
            }
        });
        return search;
    };

    const parseBody = (form) => {
        const textarea = form.querySelector('[data-kind="body"]');
        if (!textarea) {
            return undefined;
        }

        const raw = textarea.value.trim();
        if (!raw) {
            return undefined;
        }

        return JSON.parse(raw);
    };

    const extractDataResponse = (payload) => {
        if (payload && typeof payload === 'object') {
            if (Object.prototype.hasOwnProperty.call(payload, 'data')) {
                return payload.data;
            }

            return '';
        }

        return payload;
    };

    document.querySelectorAll('.js-tester').forEach((form) => {
        const output = form.querySelector('[data-role="output"]');
        const resetButton = form.querySelector('[data-action="reset"]');
        const defaultBody = form.querySelector('[data-kind="body"]')?.value ?? '';
        const defaultPathValues = Array.from(form.querySelectorAll('[data-kind="path"]')).reduce((carry, input) => {
            carry[input.name] = input.value;
            return carry;
        }, {});
        const defaultQueryValues = Array.from(form.querySelectorAll('[data-kind="query"]')).reduce((carry, input) => {
            carry[input.name] = input.value;
            return carry;
        }, {});

        const renderOutput = (value) => {
            output.value = typeof value === 'string' ? value : jsonPretty(value);
        };

        resetButton?.addEventListener('click', () => {
            form.querySelectorAll('[data-kind="path"]').forEach((input) => {
                input.value = defaultPathValues[input.name] || '';
            });
            form.querySelectorAll('[data-kind="query"]').forEach((input) => {
                input.value = defaultQueryValues[input.name] || '';
            });
            const body = form.querySelector('[data-kind="body"]');
            if (body) {
                body.value = defaultBody;
            }
            renderOutput('Example values restored.');
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const method = form.dataset.method?.toUpperCase() || 'GET';
            const baseUrl = form.dataset.baseUrl || '';
            const rawPath = form.dataset.path || '';
            const url = new URL(baseUrl.replace(/\/$/, '') + applyPathParams(rawPath, form));
            const query = collectQueryParams(form);

            query.forEach((value, key) => url.searchParams.set(key, value));

            const headers = {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            };

            const token = localStorage.getItem(storageKey);
            if (token) {
                headers.Authorization = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
            }

            const options = {
                method,
                headers,
            };

            if (!['GET', 'HEAD'].includes(method)) {
                try {
                    options.body = JSON.stringify(parseBody(form) ?? {});
                } catch (error) {
                    renderOutput(`Invalid JSON body: ${error.message}`);
                    return;
                }
            }

            renderOutput('Sending request...');

            try {
                const response = await fetch(url.toString(), options);
                const contentType = response.headers.get('content-type') || '';
                let payload;

                if (contentType.includes('application/json')) {
                    payload = await response.json();
                } else {
                    payload = await response.text();
                }

                renderOutput(extractDataResponse(payload));
            } catch (error) {
                renderOutput(`Request failed: ${error.message}`);
            }
        });
    });
</script>
</body>
</html>
