<!doctype html>


<head>
    @include('employee.components.style')

</head>

<body>

            {{-- @include('employee.components.asidemenu') --}}


            <main class="content">
                @include('employee.components.navbar')


                @include('employee.components.bottommenu')
                @yield('admincontent')



            </main>



    <!-- Core JS -->
    @include('employee.components.script')

</body>


</html>
