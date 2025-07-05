<!doctype html>
<html lang="en">
<head>
    @include('admin.components.style')
</head>

<body class="bg-light">
{{-- 
    <!-- Navbar -->
    @include('admin.components.navbar') --}}

    <!-- Main Content Area with Side Padding -->
    <main class="min-h-screen px-4 sm:px-6 lg:px-8 py-4">
        @yield('admincontent')
    </main>

    {{-- <!-- Footer -->
    @include('admin.components.footer') --}}

    <!-- Scripts -->
    @include('admin.components.script')
</body>
</html>
