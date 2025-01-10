@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Danh sách người dùng</h2>
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="myTable2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên người dùng </th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    <form action="{{ route('customer.update', $customer->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="selected" value="{{ $customer->selected == 1 ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-{{ $customer->selected == 1 ? 'success' : 'danger' }}">
                                            {{ $customer->selected == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
