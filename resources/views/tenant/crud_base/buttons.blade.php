@if(isset($MODULE_BOTTONS))
@foreach ($MODULE_BOTTONS as $button)
<button type="button" class="ms-0 btn {{ $button['class'] }} " @if($button['modal']) data-bs-toggle="modal" data-bs-target="#{{ $button['modal'] }}" @endif @if($button['js_function']) onclick="{{ $button['js_function'] }}" @endif>
    <i class="{{ $button['icon'] }}" style="font-size:17px !important;"></i>
    {{ $button['name'] }}
</button>
@endforeach

@includeIf("tenant.$VIEW_FOLDER.buttons_modals")
@endif
