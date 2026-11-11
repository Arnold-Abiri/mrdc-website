<x-mail::message>
# Dear {{ $enquiry->name }},

Thank you for contacting **Mutoko Rural District Council**.

@if($isAcknowledgement ?? false)
We have received your enquiry regarding **"{{ $enquiry->subject }}"** and it is now with our team for review.
@else
We are writing to inform you that your enquiry regarding **"{{ $enquiry->subject }}"** has been updated.
@endif

**Reference Number:** `{{ $enquiry->public_id }}`  
**Current Status:** **{{ $statusLabel }}**  
@if($enquiry->department)
**Department:** {{ $enquiry->department->name }}  
@endif

@if(!empty($officerNote))
### Official Council Response:
> {!! nl2br(e($officerNote)) !!}
@endif

<x-mail::panel>
**Original Enquiry Summary:**  
{{ Str::limit($enquiry->message, 250) }}
</x-mail::panel>

If you have any further questions or require additional assistance, please reach out to us referencing your unique enquiry ID above.

Kind regards,<br>
**Mutoko Rural District Council**  
*Service, Integrity & Community Development*
</x-mail::message>
