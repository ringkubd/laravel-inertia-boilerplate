@component('mail::message')
A message is waiting for your response on  <a href="https://diploma.isdb-bisew.org" target="_blank">IsDB-BISEW Madrasah Program website.</a>

@component('mail::button', ['url' => 'https://diploma.isdb-bisew.org'])
Go To
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
