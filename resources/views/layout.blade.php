<!DOCTYPE html>
<html lang="en">
@if (!View::hasSection('hideSidebar'))
    @include('layouts.sidebar')
@endif
@include('layouts.head')

<body>

    @if(! page_name('main') || page_name('main') == 'login' ) @yield('content')
    @else

    {{-- @include('layouts.sidebar') --}}

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

<!-- Bootstrap JS AFTER jQuery -->
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}

</body>

</html>

