@if(!empty($TABLE_FILTERS))
<button id="filter_drawer_toggle" type="button" class="btn btn-sm btn-icon btn-white p-6 mt-1 mb-1 border-i-toggler rounded-0 btn-dark-custom" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
    <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
    <span class="svg-icon svg-icon-3">
        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none">
                <path
                    d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                    fill="#FFF" />
            </svg> --}}
        <i class="ki-duotone ki-filter-square size-icons-custom-s">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </span>
    <!--end::Svg Icon-->
</button>

<!--begin::Filter drawer-->
<div id="filter_drawer" class="bg-white no-print drawer drawer-end" data-kt-drawer-width="{default:'100%', 'lg': '25%'}" data-kt-drawer="true" data-kt-drawer-name="filter" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#filter_drawer_toggle" data-kt-drawer-close=".filter_drawer_close">
    <div class="card shadow-none rounded-0 w-100" id="kt-toolbar-filter">
        <!--begin::Header-->
        <div class="card-header" id="filter_drawer_header">
            <h3 class="card-title fw-bolder text-dark px-5">{{__('Filter Options') }}</h3>

            <div class="card-toolbar px-10">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary me-n5 p-5 filter_drawer_close">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1 cus-svg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->

                </button>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body position-relative" id="filter_drawer_body">
            <!--begin::Content-->
            <div id="filter_drawer_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#filter_drawer_body" data-kt-scroll-dependencies="#filter_drawer_header, #filter_drawer_footer" data-kt-scroll-offset="5px">
                <!--begin::Content-->
                <div class="py-5">
                    @if(array_key_exists('date_range', $TABLE_FILTERS) && $TABLE_FILTERS['date_range'] == true)
                    <!--begin::Input group-->
                    <div class="mb-2 mt-2">
                        <label for="" class="form-label">{{__('Created At')}}</label>
                        <input class="form-control form-control-solid" placeholder="{{__('Pick Range Selections')}}" id="filter_by_date_range" />
                    </div>
                    <!--end::Input group-->
                    @endif
                    @foreach($TABLE_FILTERS as $filterKey => $filterValue)
                    @php
                    if(!$filterValue) continue;
                    if($filterKey == 'date_range') continue;
                    $filterInput = collect($DYNAMIC_INPUTS)->where('name', $filterKey)->first();
                    @endphp

                    @if($filterInput)
                    <!--begin::Input group-->
                    <div class="mb-2 mt-2">
                        <label for="" class="form-label">{{ $filterInput->label }}</label>
                        <select id="filter_by_{{ $filterInput->name}}" name="{{ $filterInput->name}}" data-placeholder="{{__('Please Select')}}" @if($filterInput->type != 'select2-ajax') data-control="select2" @endif class="form-select form-select-md filter_inputs" data-allow-clear="true" data-dropdown-parent="#kt-toolbar-filter" @if($filterValue === "Multiple") multiple @endif >
                            <option></option>
                            @if ($filterInput->options)
                            @foreach ($filterInput->options as $optionKey => $optionVal)
                            <option value="{{ $optionKey }}">
                                {{ $optionVal }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        <span class="form-text"></span>
                    </div>
                    <!--end::Input group-->
                    @endif
                    @endforeach
                </div>
                <!--end::Content-->

                @includeIf("tenant.$VIEW_FOLDER.filter_custom")

            </div>
            <!--end::Content-->
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="card-footer py-5 text-center" id="filter_drawer_footer">

            <!--begin::Actions-->
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset">{{__('Reset')}}</button>
                <button type="submit" class="btn btn-primary btn-primary-custom filter_drawer_close" data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">{{__('Apply')}}</button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Footer-->
    </div>
</div>
<!--end::Filter drawer-->
@endif
