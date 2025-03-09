@extends('admin.layouts.app')

@section('content')
<style>
    .chat-app .people-list {
        width: 280px;

        left: 0;
        top: 0;
        z-index: 7;
        background: rgb(221, 219, 219);
        height: calc(100vh - 54px);
    }

    .chat-app .chat {
        margin-left: 280px;
        border-left: 1px solid #eaeaea
    }

    .people-list {
        -moz-transition: .5s;
        -o-transition: .5s;
        -webkit-transition: .5s;
        transition: .5s
    }

    .people-list .chat-list li {
        padding: 10px 15px;
        list-style: none;
    }

    .people-list .chat-list li:hover {
        background: #efefef;
        cursor: pointer
    }

    .people-list .chat-list li.active {
        background: #be0a0a
    }

    .people-list .chat-list li .name {
        font-size: 15px
    }

    .people-list .chat-list img {
        width: 45px;
        border-radius: 50%
    }

    .people-list img {
        float: left;
        border-radius: 50%
    }

    .people-list .about {
        float: left;
        padding-left: 8px
    }

    .people-list .status {
        color: #999;
        font-size: 13px
    }
    .online,
    .offline,
    .me {
        margin-right: 2px;
        font-size: 8px;
        vertical-align: middle
    }
    .online {
        color: #86c541
    }

    .offline {
        color: #e47297
    }
    .selected {
        background-color: #553838;
    }
</style>
<div class="container">
    <div class="row clearfix">
        <div class="col-lg-3">
            <div class="card chat-app">
                <div id="plist" class="people-list">
                    <ul class="list-unstyled chat-list mb-0">
                        @foreach ($customers as $customer)
                            <a href="{{ route('admin.seen-message', ['id' => $customer->id]) }}" class="customer-link">
                                <div class="customer-link">
                                    <li class="clearfix" data-id="{{ $customer->id }}">
                                        <div class="flex align-items-center justify-content-center">
                                            @if($customer->image)
                                                <img src="{{ asset('storage/imgCustomer/' . $customer->image) }}" 
                                                    alt="{{ $customer->name }}" 
                                                    class="img-thumbnail rounded-circle"
                                                    style="width: 45px; height: 45px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('icon/avatar.jpg')}}"
                                                    alt="{{ $customer->name }}" 
                                                    class="img-thumbnail rounded-circle"
                                                    style="width: 45px; height: 45px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="about">
                                            <div class="name">{{ $customer->name }}</div>
                                            @if ($customer->status === 'online')
                                                <div class="status">
                                                    <i class="fa fa-circle online"></i> Đang hoạt động
                                                </div>
                                            @else
                                                <div class="status">
                                                    <i class="fa fa-circle offline"></i> Không hoạt động
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                </div>
                            </a>
                        @endforeach
                    </ul>                                  
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            @if (!request()->route('id'))
                <div class="welcome-message d-flex flex-column align-items-center justify-content-center" style="height: 100%; margin-top: 20px;">
                    <h3 class="text-center text-primary">Chào mừng bạn đến với hệ thống chat!</h3>
                    <p class="text-center text-muted" style="max-width: 400px;">
                        Hãy chọn một khách hàng từ danh sách bên trái để bắt đầu trò chuyện.
                    </p>
                </div>            
            @else
                @yield('contentx')
            @endif
        </div>
    </div>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const customerLinks = document.querySelectorAll('.customer-link');
        const selectedCustomerId = localStorage.getItem('selectedCustomerId');
        const hasChatId = window.location.href.includes('/admin/seen-message/'); // Kiểm tra URL có chứa ID không

        customerLinks.forEach(function(link) {
            const customerId = link.querySelector('li').getAttribute('data-id');
            // Chỉ thêm class 'selected' nếu có ID trong URL
            if (customerId === selectedCustomerId && hasChatId) {
                link.classList.add('selected');
            } else {
                link.classList.remove('selected');
            }
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Loại bỏ 'selected' khỏi tất cả các mục
                customerLinks.forEach(function(item) {
                    item.classList.remove('selected');
                });

                // Thêm class 'selected' vào mục được click
                link.classList.add('selected');

                // Lưu ID vào localStorage nếu có ID
                localStorage.setItem('selectedCustomerId', customerId);

                // Chuyển hướng đến trang tương ứng
                window.location.href = link.href;
            });
        });
    });
</script>
@endsection