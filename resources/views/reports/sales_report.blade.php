<div>
    <h1>Reporte en Proceso</h1>

    <table>
        <thead>
            <th>#</th>
            <th>Producto</th>
            <th>Precio vendido</th>
            <th>cantidad</th>
            <th>total</th>
        </thead>
        <tbody>
            @foreach ($sales as $key => $sale)
            @php
            $product = json_decode($sale->saleProduct[0]->product);
            @endphp
            <tr>
                <td style="text-align:center">{{ $key+1 }}</td>
                <td style="text-align:center">{{ $product->description}}</td>
                <td style="text-align:center">S/. {{ $product->price }}</td>
                <td style="text-align:center">{{ $product->quantity }}</td>
                <td style="text-align:center">S/. {{ $product->price * $product->quantity }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    <hr>
    <h1></h1>
</div>
