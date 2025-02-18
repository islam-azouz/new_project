<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!--begin::Sidebar mobile toggle-->
        @include('tenant.layouts.header.sidebar-mobile-toggle')
        <!--end::Sidebar mobile toggle-->
        <!--begin::Mobile logo-->
        @include('tenant.layouts.header.mobile-logo')
        <!--end::Mobile logo-->
        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <!--begin::Menu wrapper-->
            @include('tenant.layouts.header.menu')
            <!--end::Menu wrapper-->
            <!--begin::Navbar-->
            @include('tenant.layouts.header.navbar')
            <!--end::Navbar-->
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>
