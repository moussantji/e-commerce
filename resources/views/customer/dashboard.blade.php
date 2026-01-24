<h1>This is customer dashboard</h1>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <a href="{{ route('logout') }}"
            onclick="event.preventDefault();
                        this.closest('form').submit();">
        Log Out
</a>
</form>
