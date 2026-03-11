<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .insights {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .grid td {
            width: 33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #eee;
        }

        .grid-val {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .grid-lbl {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }

        table.data th {
            background-color: #f8fafc;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        table.data td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            color: #999;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 class="title">{{ $report['title'] }}</h1>
        <div class="subtitle">Period: {{ $report['period'] ?? 'N/A' }} | Generated:
            {{ $report['metadata']['generated_at'] ?? now() }}</div>
    </div>

    @if(!empty($report['insights']))
        <div class="insights">
            <strong>AI Insights Summary:</strong><br><br>
            {!! nl2br(e($report['insights'])) !!}
        </div>
    @endif

    <table class="grid">
        <tr>
            @php $count = 0; @endphp
            @foreach($report['metrics'] ?? [] as $key => $metric)
                @if(!is_array($metric['data']['value'] ?? null))
                    @if($count > 0 && $count % 3 == 0)
                        </tr>
                    <tr> @endif
                    <td>
                        <span class="grid-lbl">{{ $metric['label'] }}</span>
                        <span
                            class="grid-val">{{ rtrim(rtrim(number_format((float) ($metric['data']['value'] ?? 0), 2), '0'), '.') }}</span>
                    </td>
                    @php $count++; @endphp
                @endif
            @endforeach
        </tr>
    </table>

    @foreach($report['metrics'] ?? [] as $key => $metric)
        @if(is_array($metric['data']['value'] ?? null))
            <h3>{{ $metric['label'] }} Breakdown</h3>
            <table class="data">
                <thead>
                    <tr>
                        @foreach(array_keys(current($metric['data']['value'])) as $th)
                            <th>{{ strtoupper($th) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($metric['data']['value'] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{{ is_array($cell) ? json_encode($cell) : $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="footer">
        Generated securely by AI Analytics Engine
    </div>

</body>

</html>