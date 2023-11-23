<table>
    <thead>
        <tr>
            <th>Date encoded</th>
            <th>Code</th>
            <th>Supplier</th>
            <th>Brand</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Selling price</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->created_at->format('m-d-y') }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->supplier }}</td>
            <td>{{ $item->brand }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->cost }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->tally->number }}</td>
        </tr>
        @endforeach
    </tbody>
</table>