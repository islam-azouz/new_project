<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <!--begin::Sidebar-->
    @include('tenant.layouts.sidebar')
    <!--end::Sidebar-->
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            @include('tenant.layouts.app-toolbar')
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    @yield('content')
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Footer-->
        @include('tenant.layouts.app-footer')
        <!--end::Footer-->
    </div>
    <!--end:::Main-->
</div>
