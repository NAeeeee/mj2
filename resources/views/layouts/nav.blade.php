<nav class="navbar navbar-expand-lg navbar-dark bg-dark topbar">
    <div class="container px-lg-5">
        <a class="navbar-brand" href="/">Home</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('notice.index') }}">Notice</a></li>
                    @auth
                        @php
                            $msg = \App\Models\Message::where('receiver_id', auth()->id())
                                ->where('div', 'R')
                                ->where('is_read', false)
                                ->where('save_status','Y')
                                ->exists();
                        @endphp
                        @if(auth()->user()->email_verified_at !== null)
                            @if( auth()->user()->is_admin === 'Y' )
                            <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit', ['id' => auth()->id()]) }}">Info</a></li>
                            @endif
                            <li class="nav-item"><a class="nav-link" href="{{ route('message.inbox') }}">
                                @if($msg)<i class="bi bi-envelope-check"></i>
                                @else
                                    Message
                                @endif</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endif
                    @endauth
                        <!-- <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li> -->
                </ul>
            </div>
    </div>
</nav>