@extends('admin.chat.index')
@section('contentx')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <div class="chat" style="background: rgb(236, 234, 236)">
        <div class="chat-header clearfix">
            <div class="row">
                <div class="col-lg-6">
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
                    <div class="chat-about">
                        <h6 class="m-b-0">{{$customer->name}}</h6>
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
                </div>
            </div>
        </div>

        <div class="chat-history">
            <ul class="m-b-0" id="chatMessages">
                @php
                    $previousMessageTime = null; // Lưu thời gian của tin nhắn trước
                @endphp
                @foreach ($messages as $message)
                    @php
                        $currentMessageTime = \Carbon\Carbon::parse($message->created_at);
                        $shouldShowTime = false;
                        if ($previousMessageTime) {
                            $shouldShowTime = $currentMessageTime->diffInMinutes($previousMessageTime) > 10;
                        } else {
                            $shouldShowTime = true; // Hiển thị thời gian cho tin nhắn đầu tiên
                        }
                    @endphp
        
                    @if ($shouldShowTime)
                        <li class="message-time" style="text-align: center; font-size: 12px; color: gray; margin: 30px 0; ">
                            @php
                                $now = now();
                                $isToday = $currentMessageTime->isToday(); // Kiểm tra có phải trong ngày hôm nay không
                                $isWithin7Days = $currentMessageTime->gt($now->subDays(7)); // Kiểm tra có trong vòng 7 ngày không
                            @endphp
                            
                            @if ($isToday)
                                {{ $currentMessageTime->format('H:i:s') }} 
                            @elseif ($isWithin7Days)
                                {{ $currentMessageTime->format('l H:i:s') }}
                            @else
                                {{ $currentMessageTime->format('d/m/Y H:i:s') }}
                            @endif                        
                        </li>
                    @endif
        
                    @php
                        $previousMessageTime = $currentMessageTime;
                    @endphp
        
                    <li class="clearfix message-item" data-time="{{ $message->created_at }}">
                        @if ($message->sender === 'Admin')
                            <div style="width: 100%; text-align: center; margin-bottom: 20px;">
                                <span class="message-data-time" 
                                    style="display: none; font-size: 12px; color: gray;">
                                </span>
                            </div>
                            <div style="cursor: pointer; font-size: 12px;" class="message other-message float-right">{{ $message->message }}</div>
                        @else
                            <div class="message-data" style="width: 100%; text-align: center; margin-bottom: 20px;">
                                <span class="message-data-time" 
                                    style="display: none; font-size: 12px; color: gray;" >
                                </span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                @if($customer->image)
                                    <img src="{{ asset('storage/imgCustomer/' . $customer->image) }}" 
                                            alt="{{ $customer->name }}" 
                                            class="img-thumbnail rounded-circle"
                                            style="width: 45px; height: 45px; object-fit: cover; margin-right: 10px;">
                                @else
                                    <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                            style="width: 45px; height: 45px; border-radius: 50%; border: 5px solid white; box-shadow: 0 0 0 1px #dee2e6;">
                                        <span class="small fw-medium">{{ substr($customer->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div style="cursor: pointer; font-size: 12px;" class="message my-message">{{ $message->message }}</div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div id="scroll-to-top" class="scroll-to-top" style="text-align: center">
                <i class="fa-regular fa-thumbs-down" style="padding: 10px;"></i>
            </div>
        </div>
        <div class="chat-message clearfix">
            <form id="chatForm" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                @csrf
                <div class="input-group">
                    <input 
                        name="message" 
                        id="messageInput" 
                        class="form-control" 
                        placeholder="Type your message..." 
                        required 
                        style="font-size: 12px; outline: none;"
                    />
                    <button type="button" class="btn btn-light" id="emojiPickerButton" style="margin-left: 5px;">
                        😊
                    </button>
                    <div id="emojiPickerContainer" class="emoji-picker-container">
                        <div id="emojiGrid" class="emoji-grid">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-light"  style="margin-left: 5px;">
                        <i class="fa-regular fa-paper-plane"></i>
                        {{$customer->id}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const chatMessages = document.getElementById('chatMessages');
            let lastMessageTime = null; // Lưu thời gian của tin nhắn cuối cùng
            const emojiPickerButton = document.getElementById('emojiPickerButton');
            const emojiPickerContainer = document.getElementById('emojiPickerContainer');
            const emojiGrid = document.getElementById('emojiGrid');
            const popularEmojis = [
                '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂',
                '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩',
            ];
            // Hiển thị hoặc ẩn emoji picker khi click nút emoji
            emojiPickerButton.addEventListener('click', (event) => {
                emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'block' ? 'none' : 'block';
                event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
            });
            // Tạo emoji trong picker
            popularEmojis.forEach(emoji => {
                const emojiItem = document.createElement('div');
                emojiItem.classList.add('emoji-item');
                emojiItem.textContent = emoji;
                emojiGrid.appendChild(emojiItem);

                // Chèn emoji vào input khi click
                emojiItem.addEventListener('click', () => {
                    input.value += emoji;
                    emojiPickerContainer.style.display = 'none';
                });
            });
            // Đóng emoji picker khi click bên ngoài
            document.addEventListener('click', (event) => {
                if (!emojiPickerContainer.contains(event.target) && event.target !== emojiPickerButton) {
                    emojiPickerContainer.style.display = 'none';
                }
            });
            // Khởi tạo Pusher
            const pusher = new Pusher('c44c17ba15a1c83ce51d', {
                cluster: 'ap1',
                forceTLS: true
            });
            // Kết nối với kênh chat
            const channel = pusher.subscribe("chat.message");

            // Lắng nghe sự kiện ChatMessageSent
            channel.bind('ChatMessageSent', function (data) {
                const newMessageItem = document.createElement('li');
                newMessageItem.classList.add('clearfix');
                const currentTime = new Date();
                const shouldShowTime = !lastMessageTime || (currentTime - lastMessageTime) >= 10 * 60 * 1000;
                if (data.sender === 'Admin') {
                    newMessageItem.innerHTML = `
                        <div class="message-data text-right">
                            ${shouldShowTime ? `<div class="message-time" style="text-align: center; margin-top: 30px;">${currentTime.toLocaleTimeString()}</div>` : ''}
                        </div>
                        <div class="message other-message float-right" style="font-size: 12px; cursor: pointer;">${data.message}</div>
                    `;
                } else if (data.sender === 'Customer') {
                    // Nếu tin nhắn từ Customer
                    newMessageItem.innerHTML = `
                        <div class="message-data text-right">
                            ${shouldShowTime ? `<div class="message-time" style="text-align: center; margin-top: 30px;">${currentTime.toLocaleTimeString()}</div>` : ''}
                        </div>
                        <div style='display: flex; align-items: center;'> 
                            <div class="message-data" style='margin-right: 20px;'>
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
                            <div class="message my-message" style="font-size: 12px; cursor: pointer;">${data.message}</div>  
                        </div>
                    `;
                }
                chatMessages.appendChild(newMessageItem);
                lastMessageTime = currentTime;
            });
            // Lắng nghe sự kiện nhấn phím Enter trong ô input
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendMessage();
                    input.value = '';
                    input.focus();
                }
            });            
            // Lắng nghe sự kiện submit của form
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                sendMessage();
                input.value = '';
                input.focus();
            });
            // Hàm gửi tin nhắn qua AJAX
            function sendMessage() {
                const message = input.value.trim();
                if (!message) return;
                
                fetch('{{ route("admin.send-message", ["id" => $customer->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: message }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        input.value = '';
                        input.focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    </script>        
@endsection
