<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucwords(str_replace('-', ' ', $type)) }} report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { color: #0c4a6e; font-size: 18px; }
        p { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #082f49; color: white; text-align: left; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>{{ ucwords(str_replace('-', ' ', $type)) }} report</h1>
    <p>Generated {{ now()->format('M d, Y H:i') }}</p>
    <table>
        <thead><tr>@foreach($headers as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>@foreach($row as $value)<td>{{ $value ?? '—' }}</td>@endforeach</tr>
        @empty
            <tr><td colspan="{{ count($headers) }}">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
