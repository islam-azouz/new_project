<div class="modal fade ovr" data-bs-focus="false" id="MODAL_ADD" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered @if(is_numeric(rtrim($MODAL_SIZE, 'px'))) mw-{{ $MODAL_SIZE }} @else modal-{{ $MODAL_SIZE }} @endif">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            {!! Form::open(['class' => 'FORM_ADD form', 'go' => url($MODULE_SLUG ), 'id' => 'FORM_ADD','files' => true]) !!}
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_header">
                <!--begin::Modal title-->
                <h2 class="fw-bolder">{{ $MODAL_HEAD_ADD }}</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary modal_close">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1 cus-svg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>

            <!--end::Modal header-->

            <div class="">
                <!-- errors -->
                <div class="alert alert-danger fade show error_list " role="alert" style="display: none;">
                    <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
                    <div class="alert-text ">
                        <ul></ul>
                    </div>
                </div>
                <!-- errors -->
            </div>
            <!--begin::Modal body-->
            <div class="modal-body py-5 px-lg-5">
                <!--begin::Scroll-->
                {{-- <div class="scroll-y me-n7 pe-7" id="kt_modal_add_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_header" data-kt-scroll-wrappers="#kt_modal_add_scroll" data-kt-scroll-offset="300px"> --}}
                    <!--begin::Dynamic Inputs -->
                    @include('tenant.layouts.includes.form_maker.inputs_maker')
                    <!--end::Dynamic Inputs -->

                    <!--begin::Custom Inputs -->
                    @includeIf("tenant.$VIEW_FOLDER.custom_inputs")
                    <!--end::Custom Inputs -->
                {{-- </div> --}}
                <!--end::Scroll-->
            </div>
            <!--end::Modal body-->
            <!--begin::Modal footer-->
            <div class="modal-footer flex-center">
                <!--begin::Button-->
                <button type="reset" class="btn btn-danger me-3 modal_cancel btn-light-cancel">{{ __('Discard') }}</button>
                <!--end::Button-->
                <!--begin::Button-->
                <button type="submit" id="modal_add_submit" class="btn btn-primary btn-primary-custom">
                    <span class="indicator-label">{{ __('Submit') }}</span>
                    <span class="indicator-progress">{{ __('Please wait') }} ...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                <!--end::Button-->
            </div>
            <!--end::Modal footer-->
            {!! Form::close() !!}
            <!--end::Form-->
        </div>
    </div>
</div>
