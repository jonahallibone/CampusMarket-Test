@if(Auth::check())
  @section('content')
  @include('authenticated.homepage')
@else
  @include('comingsoon')
@endif
@stop
