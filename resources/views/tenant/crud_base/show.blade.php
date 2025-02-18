@if (auth()->user()->mode == 'dark')
<style>
    tr[data-id="{{ $MODEL->id }}"] {
        /* background-color: #2C446C; */
        background-color:  #184438;
        border-color: #0E1B31;
    }

</style>
@else
<style>
    tr[data-id="{{ $MODEL->id }}"] {
        /* background-color: #DEEEF6; */
        /* background-color: #b3f2e2; */
        background-color: #DEF2EC;
        border-color: #DEEEF6;
    }

</style>
@endif

@stack('styles')
@stack('page_styles')

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-line-tabs no-print fs-5 px-0 minh-50px" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link p-6 m-0 py-6 active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">{{ __('Details') }}</button>
    </li>


    @if(!empty($custom_tabs_ids))
    @foreach($custom_tabs_ids as $id => $label)
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link p-6 m-0 py-6" id="{{ $id }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $id }}" type="button" role="tab" aria-controls="{{ $id }}" aria-selected="false">{{ $label }}</button>
    </li>
    @endforeach
    @endif

    <li class="nav-item" role="presentation">
        <button class="text-black nav-link p-6 m-0 py-6" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">{{ __('Activity') }}</button>
    </li>
</ul>

<div class="px-lg-13 no-print">
    <!-- errors -->
    <div class="alert alert-danger fade show error_list py-10 " role="alert" style="display: none;">
        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
        <div class="alert-text ">
            <ul></ul>
        </div>
    </div>
    <!-- errors -->
</div>

<!-- Tab panes -->
<div class="tab-content mt-2 no-print">

    <div class="tab-pane active p-0" id="details" role="tabpanel" aria-labelledby="details-tab">
        <div class="d-flex justify-content-between no-print show-toolbar row mx-0 gap-2">
            {{-- <div class="d-flex justify-content-between no-print show-toolbar ps-2 pe-2 pb-2 bb-5"> --}}
            <div class="left-content align-self-center pb-2 bb-5">
                @yield('toolbar-left')
            </div>
            <div class="actions px-2 pb-2 bb-5 d-flex hover-scroll-overlay-y position-static text-nowrap">
                @yield('actions')

                @if(!isset($skipDefaultActions))
                @can('update', $MODEL)
                <button type="button" class="btn btn-sm border border-secondary custom-size" href="{{ url("$MODULE_SLUG/$MODEL->id/edit") }}" title="{{__("Edit Details")}}" data-bs-toggle="modal" data-bs-target="#MODAL_EDIT">
                    <i class="bi bi-pencil-square fs-4 me-2"></i>{{ __('Edit') }}
                </button>
                @endcan

                @can('delete', $MODEL)
                <button type="button" onClick="confirmDelete({{ $MODEL->id }})" class="btn btn-sm border border-secondary btn-danger custom-size">
                    <i class="bi bi-trash fs-4 me-2"></i>{{ __('Delete') }}
                </button>
                @endcan
                @endif
            </div>
        </div>
        <div class="row d-flex justify-content-between flex-wrap my-5 px-1 mx-1">
            @if(empty($skipDefaultDetails))
            <!--begin::Dynamic Inputs -->
            @include('tenant.layouts.includes.show_fields_maker')
            <!--end::Dynamic Inputs -->
            @endif

            <!--begin::Custom Inputs -->
            @yield('custom_details')
            <!--end::Custom Inputs -->
        </div>
    </div>

    <!--begin::Activity Tab -->
    @include('tenant.layouts.includes.activity_tab')
    <!--end::Activity Tab -->

    @yield('custom_tabs')
</div>

@yield('print_preview')

@stack('page_scripts')
