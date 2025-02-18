@extends('tenant.layouts.main')

@push('page_styles')

<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ global_asset('assets/plugins/custom/datatables-a/datatables.a.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->

<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ global_asset('assets/plugins/custom/datatables/datatables.bundle' . $dir . '.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->

@if ($rtl)
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ global_asset('assets/plugins/custom/datatables-a/datatables.a.rtl.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->
@endif

@endpush

{{-- @section('breadcrumb')

@endsection --}}

@section('content')
    <div class="content d-flex flex-column flex-column-fluid mt-20" id="kt_content">
        <x-toolbar>
            @include('tenant.layouts.includes.breadcrumb', ['MODULE_NAME' => $MODULE_NAME])
            <!--begin::Toolbar-->
            <div class="d-flex flex-grow-1 justify-content-end align-center-custom-s" data-kt-docs-table-toolbar="base">

                @if(isset($EXCEL_IMPORT))
                {{--  Import/Export Excel --}}
                    @include('excel.excel_button', ['type' => $EXCEL_IMPORT])
                {{--  Import/Export Excel --}}
                @endif

                <!--begin::buttons-->
                @includeFirst(["tenant.$VIEW_FOLDER.buttons", 'tenant.crud_base.buttons'])
                <!--end::buttons-->

                @if (isset($BUTTON_ADD_NAME))
                    @can('create', $MODEL)
                        <!--begin::Add -->
                        <button type="button" class="btn btn-primary ADD me-2 shadow border-i-toggler rounded-0"
                            data-bs-toggle="modal" data-bs-target="#MODAL_ADD">
                            <i class="bi bi-plus-lg"></i>
                            {{ $BUTTON_ADD_NAME }}
                        </button>
                        <!--end::Add -->

                        <!--begin::Modal Add -->
                        @includeFirst(["tenant.$VIEW_FOLDER.modal_add", 'tenant.crud_base.modal_add'])
                        <!--end::Modal Add -->
                    @endcan
                @endif

                <!--begin::Filter-->
                @includeFirst(["tenant.$VIEW_FOLDER.filter", 'tenant.crud_base.filter'])
                <!--end::Filter-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                <div class="fw-bolder me-5">
                    <span class="me-2" data-kt-docs-table-select="selected_count"></span>{{ __('Selected') }}
                </div>

                <button type="button" onclick="confirmDelete()"
                    class=" btn me-auto shadow border-i-toggler rounded-0 btn-danger"
                    data-kt-docs-table-select="delete_selected">{{ __('Delete Selected') }}</button>
                <!--begin::Modal Multiple edit -->
                @if(isset($MULTIPLE_EDIT) && $MULTIPLE_EDIT == true)
                    @can('update', $MODEL)
                    <button type="button" class="btn btn-info  me-2 shadow border-i-toggler rounded-0"
                    data-bs-toggle="modal" data-bs-target="#MODAL_MULTIPLE_EDIT" id="MULTIPLE_EDIT_BUTTON">
                    <i class="bi bi-pencil"></i>
                    {{ __('Multiple Edit') }}
                    </button>
                    @includeFirst(["tenant.$VIEW_FOLDER.multiple_edit", 'tenant.crud_base.multiple_edit'])
                    @endcan

                @endif
                <!--end::Modal Multiple edit -->
            </div>
            <!--end::Group actions-->



        </x-toolbar>


        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid respo-mobile" id="kt_post">
            <div id="singl_page" class="flex-40 no-print">
                <!--begin::Container-->
                <div id="kt_content_container" class="container-xxl pt-2">
                    <!--begin::Card-->
                    <div class="card border rounded-0 card-dark">
                        <!--begin::Card header-->
                        <div class="card-header border-0 p-1 px-2 minh-50px rounded-0">
                            <!--begin::Card title-->
                            <div class="card-title my-2">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-2 position-absolute ms-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-docs-table-filter="search"
                                        class="form-control form-control-solid rounded-0 w-250px ps-15 py-2 ben-search"
                                        placeholder="{{ __('Search') }}" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar pointer my-2">
                                <div class="dropdown py-3">
                                    <span class="dropdown-toggle text-muted px-2 py-3" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                                        {{-- <i class="bi bi-tools"></i> --}}
                                        <i class="ki-duotone ki-setting-3 size-icons-custom-s">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                    <div id="columns-dropdown" class="dropdown-menu" aria-labelledby="triggerId">

                                    </div>
                                </div>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body p-2 pt-0">
                            <!-- JUST PREVIOUS ALERT MESSAGES POSITION IDENTIFIER -->
                            <form class="delete" id="delete_form" go="{{ url('/') }}/{{ $MODULE_SLUG }}/">
                                <input name="_method" type="hidden" value="DELETE">
                                {{ csrf_field() }}
                                <!--begin::Datatable-->
                                <table id="kt_datatable"
                                    class="table align-middle table-row-dashed fs-6 gy-2 custom-table-hover">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-5 gs-0 dt-dark">
                                            <th class="w-10px pe-2">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                        data-kt-check-target="#kt_datatable .form-check-input"
                                                        value="1" />
                                                </div>
                                            </th>

                                            @if ($TABLE_FIELDS)
                                                @foreach ($TABLE_FIELDS as $field_key => $field_title)
                                                    @if ($field_key != 'created_at')
                                                        <th class="pe-2">{{ $field_title }}</th>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if ($JOIN_TABLE_FIELDS)
                                                @foreach ($JOIN_TABLE_FIELDS as $fieldData)
                                                    @foreach ($fieldData as $field)
                                                        <th class="pe-2">{{ $field }}</th>
                                                    @endforeach
                                                @endforeach
                                            @endif

                                            @if (isset($BASE_QUERIES['customFields']))
                                                @php
                                                    $customFields = $BASE_QUERIES['customFields']->where(
                                                        'show_on_table',
                                                        1,
                                                    );
                                                @endphp
                                                @if ($customFields->count())
                                                    @foreach ($customFields as $customField)
                                                        <th class="pe-2">{{ $customField->label }}</th>
                                                    @endforeach
                                                @endif
                                            @endif

                                            @if (isset($TABLE_FIELDS['created_at']))
                                                <th>{{ $TABLE_FIELDS['created_at'] }}</th>
                                            @endif

                                            <th class="pe-2">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    </tbody>
                                </table>
                                <!--end::Datatable-->
                            </form>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->

                </div>
                <!--end::Container-->
            </div>
            @include('tenant.layouts.includes.form_maker.view_area')
        </div>
        <!--end::Post-->
    </div>

    @include('tenant.layouts.includes.form_maker.modal_edit')
@endsection

@push('page_scripts')
    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="{{ global_asset('assets/plugins/custom/datatables/datatables.bundle' . $dir . '.js') }}"></script>


    <!--end::Page Vendors Javascript-->
    <!--begin::Page Custom Javascript(used by this page)-->

    <script src="{{ global_asset('assets/js/custom/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script src="{{ global_asset('assets/js/custom/jquery-validation/dist/additional-methods.js') }}"></script>
    <script src="{{ global_asset('assets/js/custom/jquery-validation/jquery-validation.init.js') }}"></script>
    <!--end::Page Custom Javascript-->

    <!-- begin::rlt translation datatable and jquery validation -->
    @if ($rtl)
        {{-- <script src="{{ global_asset('assets/plugins/custom/datatables/datatables.bundle.rtl.js') }}"></script> --}}
        <script src="{{ global_asset('assets/js/custom/jquery-validation/dist/localization/messages_ar.js') }}"></script>
    @endif
    <!-- end::rlt translation datatable and jquery validation -->

    <!--begin::Crud Datatable -->
    @includeFirst(["tenant.$VIEW_FOLDER.crud_datatable", 'tenant.layouts.includes.form_maker.crud_datatable'])
    <!--end::Crud Datatable -->

    <!--begin::Crud Scripts -->
    @includeFirst(["tenant.$VIEW_FOLDER.crud_scripts", 'tenant.layouts.includes.form_maker.crud_scripts'])
    <!--end::Crud Scripts -->

    <!--begin::Custom Scripts -->
    @includeIf("tenant.$VIEW_FOLDER.custom_scripts")
    <!--end::Custom Scripts -->
@endpush
