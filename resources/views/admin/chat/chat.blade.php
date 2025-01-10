@extends('admin.chat.index')
@section('contentx')
<style>
    .chat .chat-header {
        padding: 15px 20px;
        border-bottom: 2px solid #f4f7f6;
    }

    .chat .chat-header img {
        float: left;
        border-radius: 40px;
        width: 40px;
    }

    .chat .chat-header .chat-about {
        float: left;
        padding-left: 10px;
    }

    .chat .chat-history {
        padding: 20px;
        height: calc(100vh - 214px);
        overflow-y: auto; 
        border: 1px solid #ddd; 
        padding: 10px; 
    }

    .chat .chat-history ul {
        padding: 0;
        list-style: none;
    }
    .chat .chat-history ul li:last-child {
        margin-bottom: 0;
    }

    .message-time {
        font-size: 12px;
        color: gray;
        text-align: center;
    }

    .message-item {
        margin-bottom: 10px;
    }

    .message {
        border-radius: 10px;
        padding: 10px;
        color: #444;
        line-height: 26px;
        font-size: 16px;
        display: inline-block;
        position: relative;
    }

    .my-message {
        background-color: #c8e6c9; /* Màu nền cho tin nhắn của người dùng */
        text-align: left; /* Căn trái cho tin nhắn người dùng */
    }

    .other-message {
        background-color: #e1f5fe; /* Màu nền cho tin nhắn của Admin */
        text-align: right; /* Căn phải cho tin nhắn Admin */
    }

    .message-data {
        margin-bottom: 15px;
    }

    .message-data img {
        border-radius: 40px;
        width: 40px;
    }

    .message-data-time {
        color: #434651;
        margin-top: 30px;
    }

    .chat-message {
        padding: 20px;
    }

    .float-right {
        float: right;
    }

    .clearfix::after {
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;
    }


    @media only screen and (max-width: 767px) {
        .chat-app .people-list {
            height: 465px;
            width: 100%;
            overflow-x: auto;
            background: #fff;
            left: -400px;
            display: none
        }
        .chat-app .people-list.open {
            left: 0
        }
        .chat-app .chat {
            margin: 0
        }
        .chat-app .chat .chat-header {
            border-radius: 0.55rem 0.55rem 0 0
        }
        .chat-app .chat-history {
            height: 300px;
            overflow-x: auto
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 992px) {
        .chat-app .chat-list {
            height: 650px;
            overflow-x: auto
            
        }
        .chat-app .chat-history {
            height: 600px;
            overflow-x: auto
        }
    }

    @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 1) {
        .chat-app .chat-list {
            height: 480px;
            overflow-x: auto
        }
        .chat-app .chat-history {
            height: calc(100vh - 350px);
            overflow-x: auto
        }
    }
    .emoji-picker-container {
        display: none;
        position: absolute;
        bottom: 60px;
        right: 10px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        width: 300px;
        max-height: 900px;
        overflow-y: auto;
        padding: 10px;
        z-index: 1000;
    }
    /* Kiểu dáng cho mũi tên */
    .scroll-to-top {
        position: absolute;
        top: 80%;           
        left: 68%;        
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background: rgb(31, 31, 31);
        cursor: pointer;
        font-size: 20px;
        color: #ddd;
        z-index: 999;
        display: none
    }
    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 3px;
        height: 400px;
    }

    .emoji-item {
        cursor: pointer;
        font-size: 24px;
        text-align: center;
        padding: 5px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }

    .emoji-item:hover {
        background-color: #f0f0f0;
    }
</style>
    <div class="chat" style="background: rgb(236, 234, 236)">
        <div class="chat-header clearfix">
            <div class="row">
                <div class="col-lg-6">
                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
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
                <div class="col-lg-6 text-right">
                    <a href="#" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                    <a href="#" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                    <a href="#" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                    <a href="#" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
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
                                <div class="message-data" style="margin-right: 20px;">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="avatar">
                                </div>
                                <div style="cursor: pointer; font-size: 12px;" class="message my-message">{{ $message->message }}</div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        
            <!-- Mũi tên xuất hiện khi cuộn lên 200px -->
            <div id="scroll-to-top" class="scroll-to-top" style="text-align: center">
                <i class="fa-regular fa-thumbs-down" style="padding: 10px;"></i>
            </div>
        </div>
        

        <div class="chat-message clearfix">
            <form id="chatForm" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                @csrf
                <div class="input-group">
                    <!-- Input for Message -->
                    <input 
                        name="message" 
                        id="messageInput" 
                        class="form-control" 
                        placeholder="Type your message..." 
                        required 
                        style="font-size: 12px; outline: none;"
                    />
                    <!-- Icon Picker Button -->
                    <button type="button" class="btn btn-light" id="emojiPickerButton" style="margin-left: 5px;">
                        😊
                    </button>

                    <div id="emojiPickerContainer" class="emoji-picker-container">
                        <div id="emojiGrid" class="emoji-grid">
                            <!-- Emoji sẽ được thêm bằng JavaScript -->
                        </div>
                    </div>
                    <!-- File Input for Images -->
                    <label for="imageInput" class="btn btn-light" style="margin-left: 5px;">
                        <i class="fas fa-paperclip"></i>
                        <input 
                            type="file" 
                            id="imageInput" 
                            name="image" 
                            accept="image/*" 
                            style="display: none;" 
                        />
                    </label>
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-light"  style="margin-left: 5px;">
                        <i class="fa-regular fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chatHistory = document.querySelector('.chat-history');
                const chatMessages = document.getElementById('chatMessages');
                const messageInput = document.getElementById('messageInput');
                const scrollToBottomButton = document.getElementById('scroll-to-top');
                let userIsScrollingUp = false; // Biến để kiểm tra người dùng cuộn lên
                function scrollToBottom(smooth = true)
                {
                    if (chatHistory)
                    {
                        chatHistory.scrollTop = chatHistory.scrollHeight;
                    }
                }


                chatHistory.addEventListener('scroll', function() {
                    // Kiểm tra nếu người dùng cuộn lên và đang cách cuối trang ít hơn 300px
                    if (chatHistory.scrollTop < chatHistory.scrollHeight - chatHistory.clientHeight - 5000) {
                        scrollToBottomButton.style.display = 'block'; // Hiển thị mũi tên cuộn xuống
                    } else {
                        scrollToBottomButton.style.display = 'none'; // Ẩn mũi tên khi đang ở cuối trang
                    }
                });
                function scrollBtn(smooth = true) {
                    if (chatHistory) {
                        if (smooth) {
                            chatHistory.scroll({
                                top: chatHistory.scrollHeight,
                                behavior: 'smooth'
                            });
                        } else {
                            chatHistory.scrollTop = chatHistory.scrollHeight;
                        }
                    }
                }
                // Sự kiện click vào mũi tên cuộn xuống
                scrollToBottomButton.addEventListener('click', function() {
                    scrollBtn();
                });

                // Cuộn xuống dưới khi tải trang ban đầu
                scrollToBottom();

                // Cuộn đến trường nhập liệu
                if (messageInput) {
                    messageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    messageInput.focus();
                }

                // Theo dõi khi người dùng cuộn trong lịch sử trò chuyện
                chatHistory.addEventListener('scroll', function() {
                    // Kiểm tra xem người dùng có cuộn lên trên không
                    if (chatHistory.scrollTop < chatHistory.scrollHeight - chatHistory.offsetHeight - 50) {
                        userIsScrollingUp = true; // Người dùng đang cuộn lên
                    } else {
                        userIsScrollingUp = false; // Người dùng đã cuộn xuống dưới
                    }
                });

                // Hàm để cuộn xuống cuối chỉ khi người dùng không cuộn lên
                function scrollOnNewMessage() {
                    if (!userIsScrollingUp) {
                        scrollToBottom();
                    }
                }

                // Tùy chọn: Mutation Observer để phát hiện các thông báo mới
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.addedNodes.length) {
                            scrollOnNewMessage();
                        }
                    });
                });

                // Bắt đầu quan sát danh sách tin nhắn trò chuyện
                if (chatMessages) {
                    observer.observe(chatMessages, {
                        childList: true,
                        subtree: true
                    });
                }
            });




            document.querySelectorAll('.message-item').forEach(item => {
                item.addEventListener('click', function () {
                    // Lấy phần tử thời gian trong tin nhắn hiện tại
                    const timeElement = this.querySelector('.message-data-time');
                    
                    // Nếu thời gian chưa hiển thị, thì ẩn tất cả thời gian của các tin nhắn khác
                    if (timeElement.style.display === 'none' || timeElement.textContent === '') {
                        // Ẩn tất cả thời gian của các tin nhắn khác
                        document.querySelectorAll('.message-data-time').forEach(time => {
                            time.style.display = 'none';
                        });

                        // Lấy thời gian từ data-time
                        const timeString = this.getAttribute('data-time');
                        const date = new Date(timeString);

                        // Định dạng thời gian theo ý muốn
                        const formattedTime = date.toLocaleTimeString([], { 
                            hour: '2-digit', 
                            minute: '2-digit',
                            second: '2-digit' 
                        });

                        // Gán và hiển thị thời gian cho tin nhắn hiện tại
                        timeElement.textContent = formattedTime; // Gán thời gian
                        timeElement.style.display = 'inline';   // Hiển thị
                    } else {
                        // Nếu thời gian đang hiển thị thì ẩn đi
                        timeElement.style.display = 'none';
                    }
                });
            });




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
                    '❤️', '👍', '👏', '🎉', '🌟', '🚀', '💡', '🤔',
                    '😎', '🤗', '😜', '😝', '🤭', '😬', '😳', '😌',
                    '😏', '🤤', '😋', '🤩', '🥳', '💀', '👀', '😋',
                    '🥺', '🧐', '🤓', '🤪', '😈', '👻', '💖', '✨',
                    '💅', '🧠', '🫶', '🍀', '🌈', '🦋', '🌸', '🥑',
                    '🍉', '🍓', '🍌', '🍍', '🍒', '🥥', '🍓', '🥝',
                    '🍒', '🥕', '🍔', '🍟', '🍕', '🍣', '🍦', '🍪',
                    '🍩', '🍮', '🍰', '🧁', '🍫', '🍪', '🎂', '🍬',
                    '🥧', '🍻', '🥂', '🍷', '🍸', '🍹', '🥃', '🍺',
                    '🍾', '🍻', '🫖', '🍲', '🍛', '🍜', '🍣', '🥗',
                    '🥟', '🍗', '🍖', '🍤', '🦐', '🦑', '🍚', '🍙'
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
                    cluster: 'ap1'
                });
            
                // Kết nối với kênh chat
                const channel = pusher.subscribe('chat');
            
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
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="avatar">
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
            
                    // Gửi AJAX request
                    fetch('{{ route("admin.send-message",  ['id' => $customer->id]) }}', {
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
                            const newMessageItem = document.createElement('li');
                            newMessageItem.classList.add('clearfix');
                            newMessageItem.innerHTML = `
                                <div class="message my-message">${data.message}</div>
                            `;
                            chatMessages.appendChild(newMessageItem);
                            input.value = '';
                        } else {
                            alert('Có lỗi xảy ra khi gửi tin nhắn.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        </script>        
@endsection
