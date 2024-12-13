@if (session('message'))
    <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('message') }}
    </div>
@endif
