@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Error!</strong> Revise los campos obligatorios.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success">
        {{Session::get('success')}}
    </div>
@endif

@if(Session::has('messages'))
    <div class="alert alert-info">
        {{Session::get('messages')}}
    </div>
@endif

@if(Session::has('info'))
    <div class="alert alert-info">
        {{Session::get('info')}}
    </div>
@endif