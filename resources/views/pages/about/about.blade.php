@extends('layout')

@section('content')

<div class="user-types-container mt-4">
    <div class="page-header about mb-3">
        <div class="header-content">
            <div class="page-title fs-2">About</div>
            <div class="page-subtitle">Meet the developers behind this project</div>
        </div>
    </div>

    <div class="visitor-log-sheet-table bg-white p-3 p-md-4 about-wrap">
        <div class="about-hero mb-3 mb-md-4">
            <div class="hero-main">
                <div>
                    <h5 class="mb-2">Development Team</h5>
                    <p class="mb-0">
                        This page is dedicated to the developers who built and maintain the Visitor Log System.
                    </p>
                </div>
                <div class="school-logo-wrap">
                    <img src="{{ asset('images/school-logo.png') }}" alt="School Logo" class="school-logo">
                </div>
            </div>
        </div>

        <div class="row g-3 about-grid">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="about-card developer-card h-100">
                    <div class="dev-avatar">A</div>
                    <div class="dev-name">Developer Name 1</div>
                    <div class="dev-role">Lead Developer</div>
                    <div class="dev-description">Focused on backend architecture, core features, and system flow.</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="about-card developer-card h-100">
                    <div class="dev-avatar">B</div>
                    <div class="dev-name">Developer Name 2</div>
                    <div class="dev-role">Frontend Developer</div>
                    <div class="dev-description">Handled UI layout, responsive pages, and user experience details.</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="about-card developer-card h-100">
                    <div class="dev-avatar">C</div>
                    <div class="dev-name">Developer Name 3</div>
                    <div class="dev-role">QA / Support Developer</div>
                    <div class="dev-description">Managed testing, issue validation, and release stability.</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="about-card developer-card h-100">
                    <div class="dev-avatar">D</div>
                    <div class="dev-name">Developer Name 4</div>
                    <div class="dev-role">Developer</div>
                    <div class="dev-description">Contributed to implementation, refinements, and system improvements.</div>
                </div>
            </div>

            <div class="col-12">
                <div class="about-card text-muted text-center py-3">
                    Update the names and roles above to match your actual developer team.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection