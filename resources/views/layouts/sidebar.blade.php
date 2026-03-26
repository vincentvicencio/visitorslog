<!-- Side Menu -->
<div id="side-bar-menu">
    <div class="side-menu" id="sideMenu">
        <div class="side-menu-icon-1 position-relative d-flex align-items-center justify-content-center w-100">
            <img src="/images/Magellan_pure_white_logo.png" class="logo d-flex me-3" alt="">
        </div>   
        <div class="side-menu-icon-2 position-relative text-white d-flex flex-column justify-content-between align-items-center w-100">
            <img src="/images/bgg.png" class="position-absolute top-0 start-0 w-100" alt="" style="z-index: 0">
            <div id="live-clock" class="d-flex flex-column">
                <div class="d-flex justify-content-between fw-bold fs-6 align-items-start">
                    <div id="clock-day" class="mt-n2"></div>
                    <div id="clock-time"></div>
                </div>
                <div id="clock-date" class="fs-6"></div>
            </div>
            <div class="user d-flex">
                <div class="user-pic flex-shrink-0 "><img src="/images/compass.png" class="w-100 h-100 p-1" alt="" style="z-index: 0"></div>
                <div class="user-name ps-2">
                    <div class="fs-6 " style="margin-top:2px;">Welcome</div>
                    <div class="d-block text-truncate fw-bold" style="width:120px; margin-top:-2px;">
                          {{ user_name(Auth::user()->id) ?? 'Guest User' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="menu fw-bold mt-3 py-1 ps-4">Menu</div>
        <div class="d-flex flex-column vh-100 mx-2">
            <a href="{{ url('dashboard') }}"
            class="sidebar-menu-button {{ Request::is('dashboard') ? 'selected' : '' }}" data-tab="dashboard">
                <i class="bi bi-person-lines-fill fs-6 p-2"></i>
                Dashboard
            </a>
            <a href="{{ url('visitorslog') }}"
            class="sidebar-menu-button {{ Request::is('visitorslog') ? 'selected' : '' }}" data-tab="visitorslog">
                <i class="bi bi-person-lines-fill fs-6 p-2"></i>
                Log Sheets
            </a>
            @if(Auth::user()->user_type == 1)

                <a href="{{ url('IDtype') }}"
                class="sidebar-menu-button {{ Request::is('IDtype') ? 'selected' : '' }}" data-tab="IDtype">
                    <i class="bi bi-credit-card-2-front fs-6 p-2"></i>
                    ID type
                </a>
                <a href="{{ url('userTypes') }}"
                class="sidebar-menu-button {{ Request::is('userTypes') ? 'selected' : '' }}" data-tab="userTypes">
                    <i class="bi bi-people-fill fs-6 p-2"></i>
                    User Type
                </a>

                <a href="{{ url('registerUser') }}"
                class="sidebar-menu-button {{ Request::is('registerUser') ? 'selected' : '' }}" data-tab="registerUser">
                    <i class="bi bi-person-add fs-6 p-2"></i>
                    User
                </a>

                <a href="{{ url('visitortype') }}"
                class="sidebar-menu-button {{ Request::is('visitortype') ? 'selected' : '' }}" data-tab="visitortype">
                    <i class="bi bi-person-badge fs-6 p-2"></i>
                    Visitor Type
                </a>

                <a href="{{ url('registerId') }}"
                class="sidebar-menu-button {{ Request::is('registerId') ? 'selected' : '' }}" data-tab="registerId">
                    <i class="bi bi-person-vcard fs-6 p-2"></i>
                    ID Numbers
                </a>

                <a href="{{ url('reports') }}"
                class="sidebar-menu-button {{ Request::is('reports') ? 'selected' : '' }}" data-tab="reports">
                    <i class="bi bi-journals fs-6 p-2"></i>
                    Reports
                </a>
                <a href="{{ url('about') }}"
                class="sidebar-menu-button {{ Request::is('about') ? 'selected' : '' }}" data-tab="about">
                    <i class="bi bi-info-circle fs-6 p-2"></i>
                    About
                </a>
            @endif
        </div>
        <!-- Log out -->
        <div class="logout-button d-flex mt-auto pe-4 fw-bold align-items-center justify-content-center mobile" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" id="logout">
            <i class="bi bi-box-arrow-left p-2"></i>
            <span>{{ __('Logout') }}</span>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div><br>

    </div>
</div>

<script>
function updateClock() {
    const now = new Date();

    // Day
    const day = now.toLocaleDateString('en-US', { weekday: 'long' });

    // Date
    const date = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

    // Time hh:mm
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;

    const time = `${hours}:${minutes}`;

    // Update 
    document.getElementById('clock-day').textContent = day;
    document.getElementById('clock-date').textContent = date;
    document.getElementById('clock-time').textContent = time + ampm;
}

// 1s refresh
setInterval(updateClock, 1000);

// Update clock
updateClock();
</script>