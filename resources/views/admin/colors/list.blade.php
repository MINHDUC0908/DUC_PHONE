@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách Colors</h2>
        <a href="{{ route('colors.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Color
        </a>
    </div>

    <table class="table table-bordered table-striped" id="myTable2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Color</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($colors as $color)
                <tr>
                    <td>{{ $color->id }}</td>
                    <td>{{ $color->color }}</td>
                    <td>{{ $color->product->product_name ?? 'No Product' }}</td>
                    <td>{{ $color->quantity }}</td>
                    <td>
                        <a href="{{ route('colors.edit', $color->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('colors.destroy', $color->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>                                   
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
