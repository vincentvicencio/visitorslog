<div id="header">

    <div class="navbar navbar-expand-md shadow-sm">
    <!-- if(auth()->check() && auth()->user()->user_type == 1) -->
        <button class="btn" id="sidebarToggle">
                <i class="bi bi-list"></i>
        </button>
    <!-- endif -->
    <img src="/images/Magellan_pure_white_logo.png" class="" alt="">
    
    <div style="margin-left: auto; color:white; font-weight:bold; letter-spacing:1px;">@auth
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                          @endauth
                          @guest
                            Guest User
                          @endguest</div>
            
    </div>
</div>