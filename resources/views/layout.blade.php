<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body>

    @if(! page_name('main') || page_name('main') == 'login' ) @yield('content')
    @else

    {{-- @include('layouts.sidebar') --}}

    <div id="content" class="ps-0 d-flex flex-column">

        @include('layouts.header')

        @yield('content')

        <!-- Notification -->
        @include('components.triggers.toast')
        @include('components.triggers.delete')
        <!-- Notification -->

        @stack('scripts')
        @yield('footer-scripts')

    </div>
    @endif

    @include('layouts.footer')
</body>

</html>