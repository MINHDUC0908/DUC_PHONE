<nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
    <button class="btn btn-outline-secondary" id="toggle-btn" onclick="toggleSidebar()">
        <i id="toggle-icon" class="bi bi-list"></i>
    </button>
{{--     
    @if (Route::currentRouteName() == 'home')
        <a class="navbar-brand ms-3" href="#">Dashboard</a>
    @elseif (Route::currentRouteName() == 'category.list'|| Route::currentRouteName() == 'category.create' || Route::currentRouteName() == 'category.edit')
        <a class="navbar-brand ms-3" href="#">Category</a>
    @elseif (Route::currentRouteName() == 'brand.list' || Route::currentRouteName() == 'brand.create'|| Route::currentRouteName() == 'brand.edit' || Route::currentRouteName() == 'brand.show')
        <a class="navbar-brand ms-3" href="#">Brand</a>
    @endif --}}
    <div class="ms-auto dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            @if(Auth::user()->image)
                <img src="{{ asset('storage/profile_images/' . Auth::user()->image) }}" 
                    alt="{{ Auth::user()->name }}" 
                    class="img-thumbnail rounded-circle border"
                    style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
            @endif
            <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
        </a>
        
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
                <a class="dropdown-item" href="{{route("profile.index")}}">
                    <i class="bi bi-person me-2"></i> 
                    Profile
                </a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
    </div>    
</nav>
