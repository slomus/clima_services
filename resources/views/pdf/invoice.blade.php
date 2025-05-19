<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Faktura #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
        }
        .header:after {
            content: "";
            display: table;
            clear: both;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .client-info {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .total {
            text-align: right;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2>{{ $company['name'] }}</h2>
            <p>{{ $company['address'] }}<br>
            Tel: {{ $company['phone'] }}<br>
            Email: {{ $company['email'] }}<br>
            NIP: {{ $company['tax_id'] }}</p>
        </div>
        <div class="invoice-info">
            <h1>FAKTURA</h1>
            <p>
                Numer: FV/{{ $invoice->id }}/{{ date('Y') }}<br>
                Data wystawienia: {{ $invoice->issue_date->format('d.m.Y') }}<br>
                Termin płatności: {{ $invoice->due_date->format('d.m.Y') }}<br>
            </p>
        </div>
    </div>

    <div class="client-info">
        <h3>Nabywca:</h3>
        <p>
            {{ $client->first_name }} {{ $client->last_name }}<br>
            @if($client->address_street)
                {{ $client->address_street }} {{ $client->address_home_number }}
                @if($client->address_apartment_number)
                    /{{ $client->address_apartment_number }}
                @endif
                <br>
            @endif
            @if($client->address_post_code)
                {{ $client->address_post_code }}
            @endif
            @if($client->address_city_id && $client->city)
                {{ $client->city->name }}<br>
            @endif
            @if($client->email)
                Email: {{ $client->email }}<br>
            @endif
            @if($client->phone)
                Tel: {{ $client->phone }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lp.</th>
                <th>Nazwa usługi</th>
                <th>Ilość</th>
                <th>Cena netto</th>
                <th>VAT</th>
                <th>Wartość brutto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $invoice->for }}</td>
                <td>1</td>
                <td>{{ number_format($invoice->amount / 1.23, 2) }} PLN</td>
                <td>23%</td>
                <td>{{ number_format($invoice->amount, 2) }} PLN</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p>
            <strong>Razem do zapłaty: {{ number_format($invoice->amount, 2) }} PLN</strong>
        </p>
    </div>

    <div class="payment-info">
        <h3>Dane do przelewu:</h3>
        <p>
            {{ $company['name'] }}<br>
            Numer konta: 00 0000 0000 0000 0000 0000 0000<br>
            Tytuł przelewu: FV/{{ $invoice->id }}/{{ date('Y') }}
        </p>
    </div>

    <div class="footer">
        <p>Dokument wygenerowany elektronicznie, nie wymaga podpisu.</p>
    </div>
</body>
</html>