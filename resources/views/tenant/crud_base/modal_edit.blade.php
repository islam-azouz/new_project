<!--begin::Form-->
{!! Form::model($editData, ['class' => 'FORM_EDIT', 'id' => 'FORM_EDIT', 'go' => url($MODULE_SLUG . '/' . $editData->id), 'method' => 'PUT','files'=>true]) !!}
<!--begin::Modal header-->
<div class="modal-header" id="kt_modal_edit_header">
    <!--begin::Modal title-->
    <h2 class="fw-bolder">{{$MODAL_HEAD_EDIT}}</h2>
    <!--end::Modal title-->
    <!--begin::Close-->
    <div class="btn btn-icon btn-sm btn-active-icon-primary modal_close">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
        <span class="svg-icon svg-icon-1">
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
    <div class="alert alert-danger fade show error_list" role="alert" style="display: none;">
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
    {{-- <div class="scroll-y me-n7 pe-7" id="kt_modal_edit_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_edit_header" data-kt-scroll-wrappers="#kt_modal_edit_scroll" data-kt-scroll-offset="300px"> --}}
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
    <button type="submit" id="modal_edit_submit" class="btn btn-primary btn-primary-custom">
        <span class="indicator-label">{{ __('Submit') }}</span>
        <span class="indicator-progress">{{ __('Please wait') }} ...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>
    <!--end::Button-->
</div>
<!--end::Modal footer-->
{!! Form::close() !!}
<!--end::Form-->

<script>
    $(".FORM_EDIT").validate({
        invalidHandler: function(event, validator) {
            swal.fire({
                text: "{{__('Sorry, fill all required fields and try again')}}."
                , icon: "error"
                , buttonsStyling: !1
                , confirmButtonText: "{{__('Ok, got it!')}}"
                , customClass: {
                    confirmButton: "btn btn-primary btn-primary-custom"
                }
            , });
        }
    });
    var initSelect2 = function() {
        var elements = [].slice.call(document.querySelectorAll('[data-control="select2"], [data-kt-select2="true"]'));

        elements.map(function(element) {
            var options = {
                dir: document.body.getAttribute('direction')
            };

            if (element.getAttribute('data-hide-search') == 'true') {
                options.minimumResultsForSearch = Infinity;
            }

            $(element).select2(options);
        });
    }
    initSelect2();

</script>

@if(auth()->user()->branch_id && !auth()->user()->roles()->where('is_super_admin', true)->exists())
<script>
    var branch_id = $('select[name="branch_id"]');
    barentDiv = branch_id.parent();
    barentDiv.append(`<input type="hidden" name="branch_id" value="{{auth()->user()->branch_id}}">`);
    branch_id.append(`<option value="{{auth()->user()->branch_id}}" selected >{{auth()->user()->branch->name}}</option>`);

    setTimeout(function() {
        branch_id.val("{{auth()->user()->branch_id}}");
        branch_id.trigger('change');
        branch_id.attr('disabled',true);
    }, 100);
</script>
@endif


@stack('page_scripts')
