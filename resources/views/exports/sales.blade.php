<table>
    <thead>
        <tr class="font-bold">
            <th>{{ $headDate }}</th>
            <th>Code</th>
            <th>Brand</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Selling price</th>
            <th>Discount</th>
            <th>Net price</th>
            <th>M-UP</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td></td>
            <td>{{ $sale->item['code'] }}</td>
            <td>{{ $sale->item['brand'] }}</td>
            <td>{{ $sale->item['description'] }}</td>
            <td>{{ $sale->item['cost'] }}</td>
            <td>{{ $sale->item['price'] }}</td>
            <td>{{ $sale->discount }}</td>
            <td>{{ $sale->net }}</td>
            <td>{{ $sale->markup }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total</td>
            <td>{{ $totalNet }}</td>
            <td>{{ $totalMarkup }}</td>
        </tr>
    </tbody>
</table>