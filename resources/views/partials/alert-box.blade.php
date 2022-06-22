@if (count($errors) > 0)
    <div class="alert alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <span class="close"></span>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
        <span class="close"></span>
    </div>
@endif

@if (session('messages'))
    <div class="alert alert-success">
        @if (is_array(session('messages')))
            <ul class="no-bullet">
                @foreach (session('messages') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @else
            {{ session('messages') }}
        @endif
        <span class="close"></span>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-info">
        {{ session('status') }}
        <span class="close"></span>
    </div>
@endif