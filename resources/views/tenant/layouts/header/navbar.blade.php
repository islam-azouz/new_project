<div class="app-navbar flex-shrink-0">
    <!--begin::Search-->
    @include('tenant.layouts.header.navbar.search')
    <!--end::Search-->
    <!--begin::Activities-->
    @include('tenant.layouts.header.navbar.activities')
    <!--end::Activities-->
    <!--begin::Notifications-->
    @include('tenant.layouts.header.navbar.notifications')
    <!--end::Notifications-->
    <!--begin::Chat-->
    @include('tenant.layouts.header.navbar.chat')
    <!--end::Chat-->
    <!--begin::My apps links-->
    @include('tenant.layouts.header.navbar.my-apps')
    <!--end::My apps links-->
    <!--begin::Theme mode-->
    @include('tenant.layouts.header.navbar.theme-mode')
    <!--end::Theme mode-->
    <!--begin::User menu-->
    @include('tenant.layouts.header.navbar.user-menu')
    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    @include('tenant.layouts.header.navbar.header-menu-toggle')
    <!--end::Header menu toggle-->
</div>
