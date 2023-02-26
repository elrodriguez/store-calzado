<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <div></div>
        <table style="width: 100%;border: 1px solid #000;border-collapse: collapse;">
            <tr>
                <td style="text-align: left;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Documento</td>
                <td style="text-align: left;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Cliente</td>
                <td style="text-align: left;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Total</td>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($sales as $sale)
                <tr>
                    <td style="vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $sale->description }} - {{ $sale->number }}</td> 
                    <td style="vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $sale->full_name }}</td>
                    <td style="text-align: right;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $sale->total  }}</td>
                </tr>
                @php
                    $total = $total + $sale->total;
                @endphp
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right;vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Total Vendido</td> 
                <td style="text-align: right;vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">{{ $total }}</td>

            </tr>
        </table>
    </div>
</body>
</html>