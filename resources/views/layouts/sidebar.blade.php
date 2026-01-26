<!-- Side Menu -->
<div id="side-bar-menu">
    <div class="side-menu" id="sideMenu">
        <div class="side-menu-icon-1">
            <img src="/images/Magellan_pure_white_logo.png" class="logo" alt="">
        </div>   
        <div class="side-menu-icon-2 text-white d-flex flex-column justify-content-between align-items-center">
            <img src="/images/bgg.png" class="side-menu-icon-bg" alt="">
            <div id="live-clock">
                <div class="d-flex justify-content-between fw-bold fs-6 align-items-start">
                    <div id="clock-day" class="mt-n2"></div>
                    <div id="clock-time"></div>
                </div>
                <div id="clock-date" class="fs-6"></div>
            </div>
            <div class="user d-flex">
                <div class="user-pic flex-shrink-0 "><img src="/images/compass.png" class="w-100 h-100 p-1" alt=""></div>
                <div class="user-name ps-2">
                    <div class="fs-6 " style="margin-top:2px;">Welcome</div>
                    <div class="d-block text-truncate fw-bold" style="width:120px; margin-top:-2px;">
                        Clint Antonio antonio antonioantonioantonio
                    </div>
                </div>

            </div>
        </div>  
        <div class="menu fw-bold mt-3 py-1 ps-4">Menu</div>

        <div class="sidebar-menu d-flex flex-column vh-100">

        <a href="{{ url('visitorlog') }}"
        class="sidebar-menu-button {{ Request::is('visitorlog') ? 'selected' : '' }}">
            <i class="bi bi-person-lines-fill fs-6 p-2"></i>
            Visitor Log Sheets
        </a>

        <a href="{{ url('usertype') }}"
        class="sidebar-menu-button {{ Request::is('usertype') ? 'selected' : '' }}">
            <i class="bi bi-people-fill fs-6 p-2"></i>
            User Type
        </a>

        <a href="{{ url('users') }}"
        class="sidebar-menu-button {{ Request::is('users') ? 'selected' : '' }}">
            <i class="bi bi-person-add fs-6 p-2"></i>
            User
        </a>

        <a href="{{ url('visitortype') }}"
        class="sidebar-menu-button {{ Request::is('visitortype') ? 'selected' : '' }}">
            <i class="bi bi-person-badge fs-6 p-2"></i>
            Visitor Type
        </a>

        <a href="{{ url('id') }}"
        class="sidebar-menu-button {{ Request::is('id') ? 'selected' : '' }}">
            <i class="bi bi-person-vcard fs-6 p-2"></i>
            ID Numbers
        </a>

        <a href="{{ url('report') }}"
        class="sidebar-menu-button {{ Request::is('report') ? 'selected' : '' }}">
            <i class="bi bi-journals fs-6 p-2"></i>
            Reports
        </a>

    </div>

        
            <div class="logout-button d-flex mt-auto pe-4 fw-bold">
                <i class="bi bi-box-arrow-left p-2"></i>
                <!-- Log out -->
                 <a  href="{{ route('logout') }}"                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}>Logout</a>
                                     <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
            </div>

        <br>

    </div>
</div>



<script>
function updateClock() {
    const now = new Date();

    // Day (Monday)
    const day = now.toLocaleDateString('en-US', { weekday: 'long' });

    // Date (January 12, 2004)
    const date = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

    // Time (hour:minute)
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // Convert 0 to 12 for 12AM

    const time = `${hours}:${minutes}`;

    // Update HTML
    document.getElementById('clock-day').textContent = day;
    document.getElementById('clock-date').textContent = date;
    document.getElementById('clock-time').textContent = time + ampm;
}

// Update every second to keep minutes accurate
setInterval(updateClock, 1000);

// Initial call
updateClock();
</script>

