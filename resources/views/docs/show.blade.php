<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $documentation['title'] }} - {{ config('app.name') }} API Docs</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f3f7fc;
            --bg-soft: #e8eff8;
            --panel: rgba(255, 255, 255, 0.92);
            --panel-2: rgba(247, 250, 255, 0.96);
            --border: rgba(148, 163, 184, 0.22);
            --text: #0f172a;
            --muted: #4b5563;
            --accent: #7dd3fc;
            --accent-2: #f8a84f;
            --ok: #16a34a;
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
            max-width: none;
            margin: 0;
            padding: 16px 16px 48px;
        }

        .topbar {
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .title-block h1 {
            margin: 10px 0 8px;
            font-size: clamp(32px, 4vw, 54px);
            line-height: 0.98;
            letter-spacing: -0.05em;
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

        .lede {
            max-width: 880px;
            color: var(--muted);
            line-height: 1.7;
            margin: 0;
        }

        .meta-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.05);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
        }

        .layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
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
            color: var(--muted);
            margin-top: 4px;
            line-height: 1.45;
        }

        .content {
            width: 100%;
            display: grid;
            gap: 18px;
        }

        .section {
            width: 100%;
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.84);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-head h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.03em;
        }

        .section-head p {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .endpoint-stack {
            display: grid;
            gap: 16px;
        }

        .endpoint {
            width: 100%;
            padding: 22px;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 250, 255, 0.96));
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .endpoint + .endpoint {
            margin-top: 0;
        }

        .endpoint-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .method {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 74px;
            padding: 8px 12px;
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

        .endpoint-path {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.03em;
            word-break: break-word;
        }

        .endpoint-subtitle {
            color: var(--muted);
            line-height: 1.65;
            margin: 2px 0 14px;
        }

        .endpoint-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.05);
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
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

        .params {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .params th, .params td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            vertical-align: top;
        }

        .params th {
            color: var(--muted);
            font-size: 12px;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .field {
            display: grid;
            gap: 8px;
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

        .output {
            margin-top: 12px;
        }

        .output pre {
            white-space: pre-wrap;
            word-break: break-word;
        }

        .notice {
            color: var(--muted);
            margin: 10px 0 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .auth-box {
            display: grid;
            gap: 10px;
            margin-top: 14px;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .auth-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .auth-box small {
            color: var(--muted);
            line-height: 1.55;
        }

        .endpoint-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(340px, 0.75fr);
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
            background: rgba(255, 255, 255, 0.88);
            box-shadow: var(--shadow);
        }

        .endpoint-tester .tester-head {
            margin-bottom: 14px;
        }

        .payload-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .payload-table th,
        .payload-table td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            vertical-align: top;
        }

        .payload-table th {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .request-json {
            margin-top: 12px;
            padding: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #f8fafc;
            width: 100%;
        }

        .request-json .notice {
            color: #cbd5e1;
        }

        .request-json pre {
            background: #0b1220;
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            width: 100%;
        }

        .response-json {
            margin: 12px 0 10px;
            padding: 16px;
            border-radius: 18px;
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #f8fafc;
            width: 100%;
        }

        .response-json .notice {
            color: #cbd5e1;
        }

        .response-json pre {
            background: #0b1220;
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            width: 100%;
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

        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .nav { position: static; max-height: none; top: auto; }
            .endpoint-layout { grid-template-columns: 1fr; }
            .endpoint-tester { position: static; }
        }

        @media (max-width: 760px) {
            .topbar { padding: 18px; }
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
        <div class="title-block">
            <div class="eyebrow">{{ $documentation['key'] }} documentation</div>
            <h1>{{ $documentation['title'] }}</h1>
            <p class="lede">
                {{ $documentation['description'] ?: 'Browse the endpoints below, inspect their payloads, and run live requests from the browser.' }}
            </p>
            <div class="meta-strip">
                <span class="pill">{{ count($documentation['endpoints']) }} endpoints</span>
                @if($documentation['version'])
                    <span class="pill">Version {{ $documentation['version'] }}</span>
                @endif
                <span class="pill">Base URL: {{ $documentation['base_url'] }}</span>
                <span class="pill">Try-it-out enabled</span>
            </div>
        </div>
        <div class="auth-box" style="min-width: min(360px, 100%);">
            <strong>API testing token</strong>
            <small>
                Paste a Sanctum bearer token here when you want to test secured endpoints.
                The token is kept in the browser for this session only.
            </small>
            <input id="global-token" type="text" placeholder="Bearer token">
        </div>
    </header>

    <div class="layout">
        <aside class="nav">
            <h2>Docs</h2>
            <a href="{{ route('docs.index') }}">All documentation</a>
            @foreach($documentations as $doc)
                <a href="{{ route('docs.show', $doc['key']) }}">
                    {{ $doc['title'] }}
                    <small>{{ count($doc['endpoints']) }} endpoints</small>
                </a>
            @endforeach

            <h2 style="margin-top: 18px;">Sections</h2>
            @foreach($documentation['tags'] as $section)
                <a href="#section-{{ \Illuminate\Support\Str::slug($section['name']) }}">
                    {{ $section['name'] }}
                    <small>{{ count($section['endpoints']) }} endpoints</small>
                </a>
            @endforeach
        </aside>

        <section class="content">
            @foreach($documentation['tags'] as $section)
                <article class="section" id="section-{{ \Illuminate\Support\Str::slug($section['name']) }}">
                    <div class="section-head">
                        <div>
                            <h2>{{ $section['name'] }}</h2>
                            <p>{{ $section['description'] ?: 'Endpoints in this group.' }}</p>
                        </div>
                        <span class="badge">{{ count($section['endpoints']) }} endpoints</span>
                    </div>

                    <div class="endpoint-stack">
                        @foreach($section['endpoints'] as $endpoint)
                            <article class="endpoint" id="{{ $endpoint['id'] }}">
                                <div class="endpoint-top">
                                    <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                                    <div class="endpoint-path">{{ $endpoint['path'] }}</div>
                                </div>

                                <div class="endpoint-subtitle">{{ $endpoint['summary'] }}</div>

                                <div class="endpoint-badges">
                                    <span class="badge">{{ $endpoint['secured'] ? 'Sanctum protected' : 'Public' }}</span>
                                    @foreach($endpoint['tags'] as $tag)
                                        <span class="badge">{{ $tag }}</span>
                                    @endforeach
                                    @if($endpoint['operation_id'])
                                        <span class="badge">Op: {{ $endpoint['operation_id'] }}</span>
                                    @endif
                                </div>

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
                                                        <td><code>{{ is_scalar($parameter['example']) || $parameter['example'] === null ? (string) $parameter['example'] : json_encode($parameter['example']) }}</code></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <div class="endpoint-layout">
                                    <div class="endpoint-details">
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
                                            @foreach($endpoint['responses'] as $response)
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
                                                                <tr><td colspan="2">No response fields documented.</td></tr>
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
                                            @endforeach
                                        </div>
                                    </div>

                                    <form class="tester js-tester endpoint-tester" data-base-url="{{ $documentation['base_url'] }}" data-method="{{ strtolower($endpoint['method']) }}" data-path="{{ $endpoint['path'] }}" data-has-body="{{ $endpoint['request'] ? '1' : '0' }}">
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
                                                The request is sent relative to <code>{{ $documentation['base_url'] }}</code> so the tester works against the current app host.
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </article>
                        @endforeach
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

            const method = form.dataset.method || 'get';
            const path = applyPathParams(form.dataset.path || '', form);
            const query = collectQueryParams(form);
            const url = `${form.dataset.baseUrl || ''}${path}${query.toString() ? `?${query.toString()}` : ''}`;
            const headers = {
                Accept: 'application/json',
            };

            const token = globalToken?.value.trim();
            if (token) {
                headers.Authorization = token.toLowerCase().startsWith('bearer ') ? token : `Bearer ${token}`;
            }

            const options = {
                method: method.toUpperCase(),
                headers,
            };

            try {
                const body = parseBody(form);
                if (body !== undefined && !['GET', 'HEAD'].includes(options.method)) {
                    headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(body);
                }

                const response = await fetch(url, options);
                const contentType = response.headers.get('content-type') || '';
                let payload = null;

                if (contentType.includes('application/json')) {
                    payload = await response.json();
                } else {
                    payload = await response.text();
                }

                renderOutput(payload);
            } catch (error) {
                renderOutput(error.message || String(error));
            }
        });
    });
</script>
</body>
</html>
