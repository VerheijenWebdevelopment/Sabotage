@component('mail::message')
# {{ $titleText }}

{!! $text !!}<br><br>

@component('mail::button', ['url' => route('auth.reset-password', ['email' => $user->email, 'code' => $user->recovery_code])])
    {{ $buttonText }}
@endcomponent

{!! $closingText !!} 
@endcomponent
