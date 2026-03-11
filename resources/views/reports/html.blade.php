<!-- Simple HTML export that mimics PDF or extends layout -->
<!DOCTYPE html>
<html>

<head>
    <title>{{ $report['title'] }}</title>
</head>

<body>
    <h1>{{ $report['title'] }}</h1>
    <pre>{{ json_encode($report, JSON_PRETTY_PRINT) }}</pre>
</body>

</html>