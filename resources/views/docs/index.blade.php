<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} API Docs</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f3f7fc;
            --bg-soft: #e8eff8;
            --panel: rgba(255, 255, 255, 0.92);
            --panel-border: rgba(148, 163, 184, 0.22);
            --text: #0f172a;
            --muted: #4b5563;
            --accent: #7dd3fc;
            --accent-2: #f8a84f;
            --success: #22c55e;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(125, 211, 252, 0.28), transparent 30%),
                radial-gradient(circle at top right, rgba(248, 168, 79, 0.18), transparent 26%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 48%, #e8eff8 100%);
            min-height: 100vh;
        }

        .wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 64px 20px 80px;
        }

        .hero {
            display: grid;
            gap: 20px;
            margin-bottom: 28px;
        }

        .eyebrow {
            display: inline-flex;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(125, 211, 252, 0.12);
            color: var(--accent);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 700;
        }

        h1 {
            font-size: clamp(38px, 7vw, 66px);
            line-height: 0.95;
            letter-spacing: -0.05em;
            margin: 0;
            max-width: 12ch;
        }

        .lede {
            max-width: 720px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.65;
            margin: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 34px;
        }

        .card {
            border: 1px solid var(--panel-border);
            border-radius: 24px;
            background: var(--panel);
            padding: 24px;
            box-shadow: 0 20px 55px rgba(15, 23, 42, 0.10);
            backdrop-filter: blur(14px);
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 26px;
            letter-spacing: -0.03em;
        }

        .card p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.6;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
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
            font-weight: 600;
        }

        .actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            padding: 12px 16px;
            text-decoration: none;
            color: #06111f;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), #c6f0ff);
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .actions a:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(125, 211, 252, 0.25);
        }

        .note {
            margin-top: 22px;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 800px) {
            .grid { grid-template-columns: 1fr; }
            .wrap { padding-top: 40px; }
        }
    </style>
</head>
<body>
    <main class="wrap">
        <div class="hero">
            <div class="eyebrow">Developer Documentation</div>
            <h1>Interactive API docs built from the OpenAPI specs</h1>
            <p class="lede">
                Browse the customer API and the platform API as real pages, inspect request and response payloads,
                and try calls directly from the browser.
            </p>
        </div>

        <section class="grid">
            @foreach($documentations as $documentation)
                <article class="card">
                    <h2>{{ $documentation['title'] }}</h2>
                    <p>{{ $documentation['description'] ?: 'A structured reference for the endpoints in this API group.' }}</p>
                    <div class="meta">
                        <span class="pill">{{ count($documentation['endpoints']) }} endpoints</span>
                        @if($documentation['version'])
                            <span class="pill">v{{ $documentation['version'] }}</span>
                        @endif
                        <span class="pill">Try-it-out enabled</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('docs.show', $documentation['key']) }}">Open docs</a>
                    </div>
                </article>
            @endforeach
        </section>

        <p class="note">
            The docs page is generated from the checked-in OpenAPI specs, so it stays in step with the API surface
            as long as the specs are regenerated.
        </p>
    </main>
</body>
</html>
