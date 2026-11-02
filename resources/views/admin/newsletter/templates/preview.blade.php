<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>テンプレートプレビュー - {{ $template->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .preview-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .preview-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .preview-header h1 {
            margin: 0;
            color: #333;
        }
        .preview-header .meta {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .preview-subject {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .preview-body {
            line-height: 1.6;
            color: #333;
        }
        .preview-body-text {
            white-space: pre-wrap;
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
        }
        .close-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .close-btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>{{ $template->name }}</h1>
            <div class="meta">
                作成日: {{ $template->created_at->format('Y年m月d日 H:i') }} | 
                更新日: {{ $template->updated_at->format('Y年m月d日 H:i') }}
            </div>
        </div>

        <div class="preview-subject">
            件名: {{ $template->subject }}
        </div>

        @if($template->body_html)
        <div class="preview-body">
            <h3>HTML本文:</h3>
            <div style="border: 1px solid #e5e7eb; padding: 15px; border-radius: 4px;">
                {!! $template->body_html !!}
            </div>
        </div>
        @endif

        @if($template->body_text)
        <div class="preview-body" style="margin-top: 20px;">
            <h3>プレーンテキスト本文:</h3>
            <div class="preview-body-text">{{ $template->body_text }}</div>
        </div>
        @endif

        <a href="javascript:window.close()" class="close-btn">閉じる</a>
    </div>
</body>
</html>


