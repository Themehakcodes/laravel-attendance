<!doctype html>


<head>
    @include('admin.components.style')

</head>

<body>

            @include('admin.components.asidemenu')


            <main class="content">
                @include('admin.components.navbar')


                @yield('admincontent')

                <!-- Content wrapper -->
                @include('admin.components.footer')
            </main>



    <!-- Core JS -->
    @include('admin.components.script')

</body>


</html>
