<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Selling price</th>
            <th>Date encoded</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->code }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->brand }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->cost }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->tally->number }}</td>
        </tr>
        @endforeach
    </tbody>
</table>