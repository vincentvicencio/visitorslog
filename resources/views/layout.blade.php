<!DOCTYPE html>
<html lang="en">

    @include('layouts.head')

    <body class="{{ Auth::check() && Auth::user()->user_type != 1 ? 'non-admin-body' : '' }}">
        @if (!View::hasSection('hideSidebar') && Auth::user()->user_type == 1)
                @include('layouts.sidebar')
        @endif
        @if(! page_name('main') || page_name('main') == 'login' ) @yield('content')
        @else

        <div id="content" class="ps-0 d-flex flex-column">

            @if (!View::hasSection('hideSidebar'))
                @include('layouts.header')
            @endif  
            
            @yield('content')

            <!-- Notification -->
            @include('components.triggers.toast')
            @include('components.triggers.delete')
            @include('components.triggers.visitorTypeModal')
            @include('components.triggers.viewImage')
            <!-- Notification -->

            @stack('scripts')
            @yield('footer-scripts')

        </div>
        @endif

        @include('layouts.footer')
    </body>
</html>


<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
