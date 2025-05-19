<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport serwisów</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #888; padding: 4px 8px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Raport serwisów ({{ $from }} - {{ $to }})</h2>
    <p>Liczba serwisów: <b>{{ $serviceCount }}</b></p>
    <p>Przychody: <b>{{ number_format($incomeSum, 2) }} zł</b></p>
    <p>Koszty: <b>{{ number_format($costsSum, 2) }} zł</b></p>

    <h3>Awaryjność urządzeń</h3>
    <table>
        <thead>
            <tr>
                <th>Urządzenie</th>
                <th>Liczba awarii</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deviceFailures as $row)
                <tr>
                    <td>{{ $row->device->model ?? 'Brak' }} ({{ $row->device->serial_number ?? '' }})</td>
                    <td>{{ $row->failures }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Efektywność serwisantów</h3>
    <table>
        <thead>
            <tr>
                <th>Serwisant</th>
                <th>Liczba serwisów</th>
            </tr>
        </thead>
        <tbody>
            @foreach($technicianStats as $row)
                <tr>
                    <td>{{ $row->user->first_name ?? 'Brak' }} {{ $row->user->last_name ?? '' }}</td>
                    <td>{{ $row->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>