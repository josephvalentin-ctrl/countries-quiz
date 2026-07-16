@include('layout.header')

<h1 class="lead">
    Welcome to the quiz!<br>Guess the Capital
</h1>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<h2>{{ $name }}</h2>

<form method="POST" action="{{ route('post.answer') }}">
    @csrf

    <input type="hidden" name="country" value="{{ $name }}">

    <div class="d-flex flex-column align-items-center gap-2">

        @foreach($options as $option)

            <label class="btn btn-outline-primary quiz-option">
                <input
                    type="radio"
                    name="capital"
                    value="{{ $option }}"
                    class="form-check-input me-2"
                    required>

                {{ $option }}
            </label>

        @endforeach

    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-success">
            Submit Answer
        </button>
    </div>

</form>

@include('layout.footer')
