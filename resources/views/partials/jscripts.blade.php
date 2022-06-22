@if (!empty($jsScripts))
    @foreach ($jsScripts as $jsScript)
        {!! $jsScript !!}
    @endforeach
@endif