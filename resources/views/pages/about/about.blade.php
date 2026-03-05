@extends('layout')
@section('content')
<div class="user-types-container about-header mt-4">
    {{-- header --}}
    <div class="page-header visitortype">
        <div class="header-content">
            <div class="page-title fs-2">About</div>
            <div class="page-subtitle mb-3">Information about the project and its authors</div>
        </div>
    </div>



    <div class="about">
        <div class="school position-relative">
            <div class="text d-flex h-100 align-items-start justify-content-center flex-column">
                <div class="fw-bold fs-5">Development Team</div>
                <div class="sub">This page is dedicated to the developers who built the Visitor Log Sheet</div>
            </div>
        </div>
        <div class="header">Our Developers</div>
        <div class="profiles">
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Ricardo II M. Diño</div>
                    <div class="subdetail">Lead Developer</div>
                    <div class="subdetail2">Focused on backend architecture, core features, and system flow.</div>
                </div>
            </div>
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Ashley Clint B. Antonio</div>
                    <div class="subdetail">Frontend Developer</div>
                    <div class="subdetail2">Handled UI layout, responsive pages, and user experience details.</div>
                </div>
            </div>
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Carmela S. Moya</div>
                    <div class="subdetail">QA / Support Developer</div>
                    <div class="subdetail2">Managed testing, issue validation, and release stability.</div>
                </div>
            </div>
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Charle B. Loreto</div>
                    <div class="subdetail">Developer</div>
                    <div class="subdetail2">Contributed to implementation, refinements, and system improvements.</div>
                </div>
            </div>
        </div>


        <div class="header">Supervised by</div>
        <div class="profiles">
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Vincent Joseph Vicencio</div>
                    <div class="subdetail">Supervisor</div>
                </div>
            </div>
            <div class="profile-holder">
                <div class="details">
                    <div class="name">Harvey Del Rosario</div>
                    <div class="subdetail">Supervisor</div>
                </div>
            </div>
            
        </div>



        <div class="header">Project Partner</div>
        <div class="profiles-partner">
            <div class="profile-holder">
                <div class="image1"><img src="images/magellan logo.png" alt=""></div>
                <div class="details">
                    <div class="name1">Magellan Solutions</div>
                </div>
                <div class="colorblur1 colorblur"></div>
            </div>
            <div class="profile-holder">
                <div class="image2"><img src="images/bsu.png" alt=""></div>
                <div class="details">
                    <div class="name2">Bulacan State University</div>
                </div>
                <div class="colorblur2 colorblur"></div>
            </div>
            
        </div>
        <br><br><br>
    </div>
</div>
    

@endsection
