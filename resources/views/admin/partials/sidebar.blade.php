<nav class="sidebar vh-100" id="sidebar">
    <div class="header">
        <span class="header-icon bi bi-person-circle"></span>
        <span class="header-text">Admin</span>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a href="{{route('home')}}" class="nav-link">
                <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
            </a>
        </li>
        @role('Admin')
        <li class="nav-item">
            <a href="{{route('user.index')}}" class="nav-link">
              <i class="bi bi-basket3 me-2"></i> <span>User</span>
            </a>
          </li>
        @endrole
        <li class="nav-item">
            <a href="{{ route('category.list') }}" class="nav-link">
                <i class="bi bi-list-ul me-2"></i></i> <span>Categories</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('brand.list') }}" class="nav-link">
                <i class="bi bi-shop me-2"></i> <span>Brands</span>
            </a>
        </li>  
        <li class="nav-item">
            <a href="{{route('product.list')}}" class="nav-link">
                <i class="bi bi-box-seam me-2"></i> <span>Product Management</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('colors.index')}}" class="nav-link">
                <i class="bi bi-palette me-2"></i> <span>Color</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('orders.list')}}" class="nav-link">
                <i class="bi bi-cart me-2"></i><span>Orders</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" id="chatLink" class="nav-link">
                <i class="bi bi-chat-dots me-2"></i><span>Chat</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('new.list')}}" class="nav-link">
                <i class="bi bi-newspaper me-2"></i> <span>News</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('customer.list')}}" class="nav-link">
                <i class="bi bi-person-circle fs-5 me-2"></i> <span>Customer Management </span>
            </a>
        </li>
    </ul>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerId = localStorage.getItem('selectedCustomerId');
        const chatLink = document.getElementById('chatLink');
        if (customerId) {
            chatLink.href = `{{ route('admin.seen-message', ['id' => ':id']) }}`.replace(':id', customerId);
        }
    });
</script>