@if(filled($recipientName))
Hello {{ $recipientName }},
@else
Hello,
@endif

{!! $mailBody !!}

Best Regards,

IUT 12th ICT FEST
IUT Computer Society.
