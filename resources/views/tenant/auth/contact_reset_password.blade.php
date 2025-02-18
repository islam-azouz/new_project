@include('tenant.layouts.includes.alert_messages')

{!! Form::open(['url' => route('contact-reset-password')]) !!}
{!! Form::email('email', request()->email, ['placeholder' => __('Email')]) !!}
{!! Form::password('password', ['placeholder' => __('New Password')]) !!}
{!! Form::password('password_confirmation', ['placeholder' => __('Confirm Password')]) !!}
{!! Form::hidden('token', $token) !!}

{!! Form::submit('submit') !!}
{!! Form::close() !!}
