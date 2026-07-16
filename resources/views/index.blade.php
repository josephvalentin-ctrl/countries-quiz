@include('layout.header')

<h1 class="lead">
    Welcome to the quiz!
</h1>

<h2 class="text-center">{{ $name }}</h2>

@foreach($options as $option)
     <button class="btn btn-primary m-2"style="min-width: 120px; min-height: 50px;">
        {{ $option }}
    </button>
@endforeach

@include('layout.footer')
