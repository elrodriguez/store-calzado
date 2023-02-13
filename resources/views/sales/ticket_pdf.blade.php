<!DOCTYPE html>
<html>
    <head>
        <title>Ticket</title>
        <style>
        .ticket {
            width: 72mm;
            height: auto;
            padding: 0px;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .header img {
            height: 30px;
        }
        .header h1 {
            margin-left: 10px;
            font-size: 20px;
        }
        .total {
            display: flex;
            justify-content: flex-end;
            font-weight: bold;
            margin-top: 10px;
        }
        .text-center {
            text-align: center;
        }
        table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            vertical-align: top;
            border: 1px solid #000;
            border-collapse: collapse;
            padding: 0.3em;
            caption-side: bottom;
        }
        caption {
            padding: 0.3em;
            color: #fff;
            background: #000;
        }
        th {
            background: #eee;
        }
        </style>
    </head>
    <body>
        <div class="ticket">
            <div class="header">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo">
                <h1>{{ env('APP_NAME','Laravel') }}</h1>
            </div>
            <span>Dirección: {{ $local->address }}</span>
            <br>
            <span>Teléfono: {{ $local->phone }}</span>
            <br>
            <br>
            <table class="table">
                <tr>
                    <td>Cant.</td>
                    <td>Producto</td>
                    <td>Precio U.</td>
                    <td>Importe</td>
                </tr>
                @foreach ($products as $item)
                    <tr>
                        <td style="text-align: right">{{ (int) $item->quantity }}</td> 
                        <td>{{ json_decode($item->product,true)['interne'] }}-{{ json_decode($item->product,true)['description'] }}</td>
                        <td style="text-align: right">{{ $item->price }}</td>
                        <td style="text-align: right">{{ $item->total }}</td>
                    </tr>
                @endforeach
            </table>
            <span class="total">Total: {{ $sale->total }}</span>
        </div>
    </body>
</html>
