<!doctype html>


<head>
    @include('employee.components.style')

</head>

<body>

            {{-- @include('employee.components.asidemenu') --}}


            <main class="content">



                @yield('admincontent')



            </main>



    <!-- Core JS -->
    @include('employee.components.script')

</body>


</html>
