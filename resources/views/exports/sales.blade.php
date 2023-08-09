<table>
    <thead>
        <tr class="font-bold">
            <th>{{ $headDate }}</th>
            <th>Code</th>
            <th>Item</th>
            <th>Brand</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Selling price</th>
            <th>Discount</th>
            <th>Net price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td></td>
            <td>{{ $sale->item['code'] }}</td>
            <td>{{ $sale->item['name'] }}</td>
            <td>{{ $sale->item['brand'] }}</td>
            <td>{{ $sale->item['description'] }}</td>
            <td>{{ $sale->item['cost'] }}</td>
            <td>{{ $sale->item['price'] }}</td>
            <td>{{ $sale->discount }}</td>
            <td>{{ $sale->net }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total</td>
            <td>{{ $total }}</td>
        </tr>
    </tbody>
</table>