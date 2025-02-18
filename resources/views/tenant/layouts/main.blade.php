<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    @include('tenant.layouts.meta.meta-main')
    <!--begin::Fonts(mandatory for all pages)-->
    @include('tenant.layouts.styles.styles-main')
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"
    class="app-default">
    <!--begin::Theme mode setup on page load-->
    @include('tenant.layouts.theme-mode-script')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('tenant.layouts.header.header-main')
            <!--end::Header-->
            <!--begin::Wrapper-->
            @include('tenant.layouts.app-wrapper')
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    <!--begin::Drawers-->
    @include('tenant.layouts.drawers.drawers-main')
    <!--end::Drawers-->
    <!--begin::Scrolltop-->
    @include('tenant.layouts.scrolltop')
    <!--end::Scrolltop-->
    <!--begin::Modals-->
    @include('tenant.layouts.app_modals.modals-main')
    <!--end::Modals-->
    <!--begin::Javascript-->
    @include('tenant.layouts.javascript.javascript-main')
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
