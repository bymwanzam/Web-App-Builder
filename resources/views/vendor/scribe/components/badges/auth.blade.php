@if($authenticated)@component('scribe::components.badges.base', ['colour' => "darkred", 'text' => 'Requires Authentication'])
@endcomponent
@endif
