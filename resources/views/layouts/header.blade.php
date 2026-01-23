<div id="header"  style="display: none;">

    <div class="navbar navbar-expand-md shadow-sm" style="background-color: red;">
    @if(auth()->check() && auth()->user()->user_type == 1)
        <button class="btn" id="sidebarToggle">
                <i class="bi bi-list"></i>
        </button>
    @endif

        <div class="test">
        <img src="/images/Magellan_pure_white_logo.png" alt="logo" class="logo">
            <ul class="navbar-nav me-auto"></ul>
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="navlink m-4 " href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown m-3">
                        <a id="navbarDropdown" class="navlink dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->username }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
            
    </div>
</div>