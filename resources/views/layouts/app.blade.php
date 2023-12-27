<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'Default Title')</title>
    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-flags.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet" />


    <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
        font-feature-settings: "cv03", "cv04", "cv11";
    }

    /* Dropdown styling */
    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 10px;
        min-width: 120px;
        z-index: 1;
    }

    .dropdown-menu.show {
        display: block;
    }

    /* Additional styling for better appearance */
    .avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .small {
        font-size: 80%;
    }
    </style>
</head>

<body>
    <script src="{{ asset('dist/js/demo-theme.min.js') }}"></script>
    <div class="page">
        <!-- navbar.blade.php -->
        <x-navbar></x-navbar>
        <div class="page-wrapper">
            <!-- Page header -->
            <!-- page-header.blade.php -->
            <!-- <x-alerts> </x-alerts> -->
            <!-- custom-alerts.blade.php -->
            @yield('posts.index')
            @yield('posts.create')
            @yield('posts.edit')
            @yield('posts.detail')
            @yield('login')
            @yield('categories.index')
            @yield('categories.create')
            @yield('categories.edit')
            @yield('categories.detail')
            @yield('editprofile')
        </div>
        <!-- footer.blade.php -->
        <x-footer> </x-footer>
    </div>
</body>
<!-- Thêm mã JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var userDropdown = document.getElementById('userDropdown');
    var dropdownMenu = document.getElementById('userDropdownMenu');

    userDropdown.addEventListener('click', function(event) {
        event.stopPropagation(); // Ngăn chặn sự kiện click từ việc lan tỏa lên các phần tử cha
        dropdownMenu.classList.toggle('show');
    });

    // Đóng dropdown khi click bên ngoài
    window.addEventListener('click', function(event) {
        if (!event.target.matches('#userDropdown')) {
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            }
        }
    });
});
</script>
<script type="text/javascript">
$(document).ready(function() {
    // Kiểm tra xem có thông điệp success trong Session không
    var successMessage = "{{ session('success') }}";
    if (successMessage) {
        // Hiển thị toast khi có thông điệp success
        $.toast({
            heading: 'Success',
            text: successMessage,
            showHideTransition: 'slide',
            icon: 'success'
        });
    }
});
$(document).ready(function() {
    // Kiểm tra xem có thông điệp error trong Session không
    var errorMessage = "{{ session('error') }}";
    if (errorMessage) {
        // Hiển thị toast khi có thông điệp error
        $.toast({
            heading: 'Error',
            text: errorMessage,
            showHideTransition: 'slide',
            icon: 'error'
        });
    }
});
</script>

</html>