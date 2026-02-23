<div id="header" class="{{ Auth::check() && Auth::user()->user_type != 1 ? 'always-show' : '' }}">

  <div class="navbar navbar-expand-ms shadow-sm fixed-top align-items-center px-3" >
    @if(Auth::user()->user_type == 1)
        <button class="btn position-absolute start-0  p-3 text-white" id="sidebarToggle">
                <i class="bi bi-list"></i>
        </button>
    @endif
    <img src="/images/Magellan_pure_white_logo.png" class="" alt="">
    
    <div class="username-holder">
      <div class="profile-holder ">
        <img src="/images/compass.png" class="w-100 h-100 p-1" alt="" style="z-index: 0">
      </div>
      <div class="name-holder text-truncate">
        @auth
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                          @endauth
                          @guest
                            Guest User
        @endguest
      </div>
    </div>
    <!-- <div style="margin-left: auto; color:white; font-weight:bold; letter-spacing:1px;">
      @auth
        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
      @endauth
      @guest
        Guest User
      @endguest
    </div> -->

    <div class="divider">|</div>
    <div class="align-items-center justify-content-center d-flex">
      <div class="logout-button d-flex mt-auto fw-bold align-items-center justify-content-center " 
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              
        <!-- <i class="bi bi-box-arrow-left p-2"></i> -->
        <span>{{ __('Logout') }}</span>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
      </div>
    </div>            
    </div>
</div>
