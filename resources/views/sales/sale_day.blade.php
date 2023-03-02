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
        <div>
            <div style="text-align: center">
                <h1>Reporte ventas del Día</h1>
            </div>
            <p><strong>Fecha:</strong> {{ $header['date'] }} </p>
            <p><strong>Usuario:</strong> {{ $header['user_name'] }} </p>
            <p><strong>Tienda:</strong> {{ $header['local_name'] }} </p>
        </div>
        <br>
        <div style="text-align: center">
            <h1>Productos Vendidos</h1>
        </div>
        <table style="width: 100%;border: 1px solid #000;border-collapse: collapse;">
            <tr>
                <td style="text-align: center;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Imagen</td>
                <td style="text-align: left;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Producto</td>
                <td style="text-align: center;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Cantidad</td>
                <td style="text-align: center;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Precio</td>
                <td style="text-align: center;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Descuento</td>
                <td style="text-align: center;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Importe</td>
            </tr>
            @php
                $total_p = 0;
            @endphp
            @foreach ($products as $product)
                <tr>
                    <td style="text-align: center;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">
                        <img src="{{ asset('storage/'.$product->image) }}" width="80px" />
                    </td>
                    <td style="vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $product->interne .' - '.$product->description.' / '.json_decode($product->product,true)['size'] }}</td> 
                    <td style="text-align: right;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $product->quantity }}</td>
                    <td style="text-align: right;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $product->price }}</td>
                    <td style="text-align: right;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $product->discount }}</td>
                    <td style="text-align: right;vertical-align: top;
                    border: 1px solid #000;
                    border-collapse: collapse;
                    padding: 0.3em;
                    caption-side: bottom;">{{ $product->total }}</td>
                </tr>
                @php
                    $total_p = $total_p + $product->total;
                @endphp
            @endforeach
            <tr>
                <td colspan="5" style="text-align: right;vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">Total Vendido</td> 
                <td style="text-align: right;vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em;
                caption-side: bottom;">{{ number_format($total_p, 2, '.', ',') }}</td>

            </tr>
        </table>
        <br>
        <div style="text-align: center">
            <h1>Documentos Emitidos</h1>
        </div>
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
                caption-side: bottom;">{{ number_format($total, 2, '.', ',') }}</td>

            </tr>
        </table>
    </div>
</body>
</html>