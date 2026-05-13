<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Previews - Fast Pace Transfer LTD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #1a2b5f;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: 600;
        }
        .subtitle {
            color: #64748b;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .preview-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .preview-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .preview-card h3 {
            color: #1a2b5f;
            margin-bottom: 8px;
            font-size: 18px;
            font-weight: 600;
        }
        .preview-card p {
            color: #64748b;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .preview-link {
            display: inline-block;
            background: linear-gradient(135deg, #1a2b5f 0%, #2d3f6f 100%);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .preview-link:hover {
            opacity: 0.9;
        }
        .info-box {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .info-box p {
            color: #0c4a6e;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mail Previews</h1>
        <p class="subtitle">Preview email templates for account setup and password reset</p>
        
        <div class="info-box">
            <p><strong>Note:</strong> You can customize the preview by adding query parameters. For example: <code>?email=test@example.com&password=TempPass123</code></p>
        </div>

        <div class="preview-grid">
            @foreach($previews as $preview)
            <div class="preview-card">
                <h3>{{ $preview['name'] }}</h3>
                <p>{{ $preview['description'] }}</p>
                <a href="{{ $preview['route'] }}" target="_blank" class="preview-link">Preview Email</a>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>

