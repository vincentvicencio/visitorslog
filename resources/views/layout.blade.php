<!DOCTYPE html>
<html lang="en">

    @include('layouts.head')

    <body>
        @if(! page_name('main') || page_name('main') == 'login' ) @yield('content')
        @else
        @include('layouts.sidebar')

        <div id="content" class="ps-0 d-flex flex-column">

            {{-- @if (!View::hasSection('hideSidebar'))
                @include('layouts.header')
            @endif   --}}
            

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
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
