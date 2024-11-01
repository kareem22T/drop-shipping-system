@extends("layout.main")
@section("profile_active", "active")

@section('content')
<div class=" mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">User Profile</div>
                <div class="card-body">
                    {{-- Status message for successful password update --}}
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Profile information --}}
                    <h4>Username: {{ Auth::user()->name }}</h4>
                    <p>Email: {{ Auth::user()->email }}</p>

                    {{-- Logout button --}}
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<br>
<br>
@endsection
