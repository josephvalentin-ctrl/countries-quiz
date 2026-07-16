@include('layout.header')

@if($correct)
    <h2>Correct!</h2>
@else
    <h2>Not quite right.</h2>
    <p>The correct capital is {{ $correctCapital }}.</p>
@endif

<div class="text-center mt-4">
    <a href="{{ route('index') }}" class="btn btn-primary">
        Next Question
    </a>
</div>

@include('layout.footer')
