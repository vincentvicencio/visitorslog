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
            {{-- @include('components.triggers.logempModal') --}} {{-- redirects to form page instead --}}
            <!-- Notification -->

            @stack('scripts')
            @yield('footer-scripts')

            
            <div type="hidden" id="usertypeCheck" data-type="{{ Auth::user()->user_type == 1 ? 1 : 0 }}"></div>

        </div>
        @endif

        <div class="modal fade" id="sessionExpiredModal" tabindex="-1" aria-labelledby="sessionExpiredModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div class="fs-1 mb-2 text-primary"><i class="bi bi-clock-history"></i></div>
                        <h5 class="fw-bold mb-2" id="sessionExpiredModalLabel">Session expired</h5>
                        <p class="text-muted mb-4">Your session has expired. Please refresh to continue.</p>
                        <button type="button" class="btn btn-primary px-4" id="sessionRefreshButton">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer')
        
    </body>
</html>


@push('scripts')
@vite(['resources/js/visitors.js'])
@endpush

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
