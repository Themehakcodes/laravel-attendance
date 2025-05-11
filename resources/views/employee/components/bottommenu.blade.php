<nav class="navbar navbar-expand-lg navbar-light fixed-bottom bg-yellow-100 py-2">
    <div class="container-fluid justify-content-around px-2">
        <ul class="navbar-nav w-100 justify-content-around d-flex flex-row align-items-center m-0 p-0" style="gap: 0.5rem;">
            <li class="nav-item" style="list-style: none;">
                <a class="nav-link text-center d-flex flex-column align-items-center p-1" href="{{ route('employee.dashboard') }}" style="font-size: 12px;">
                    <i class="fas fa-tachometer-alt" style="font-size: 16px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" style="list-style: none;">
                <a class="nav-link text-center d-flex flex-column align-items-center p-1" href="{{ route('employee.dashboard') }}" style="font-size: 12px;">
                    <i class="fas fa-calendar-check" style="font-size: 16px;"></i>
                    <span>Attendance</span>
                </a>
            </li>

            <li class="nav-item " style="list-style: none; margin-top: -60px;">
                <a class="nav-link text-center d-flex flex-column align-items-center p-4" href="" style="font-size: 16px; color:white; background-color: #232222; border-radius: 50%; width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-camera" style="font-size: 16px;"></i>
                </a>
            </li>

            <li class="nav-item" style="list-style: none;">
                <a class="nav-link text-center d-flex flex-column align-items-center p-1" href="" style="font-size: 12px;">
                    <i class="fas fa-plus" style="font-size: 16px;"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li class="nav-item" style="list-style: none;">
                <a class="nav-link text-center d-flex flex-column align-items-center p-1" href="{{ route('employee.dashboard') }}" style="font-size: 12px;">
                    <i class="fas fa-user" style="font-size: 16px;"></i>
                    <span>Profile</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
