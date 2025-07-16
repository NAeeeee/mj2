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
                            <li class="nav-item"><a class="nav-link" href="{{ route('message.inbox') }}">
                                @if($msg)<i class="bi bi-envelope-check"></i>
                                @else
                                    Message
                                @endif</a>
                            </li>

                        @if( auth()->user()->is_admin == 'Y' )
                            <li class="nav-item">
                                <div class="bg-success text-white rounded-2 btn btn-sm admin-div">
                                    관리자
                                </div>
                            </li>
                        @endif

                            {{-- toggle --}}
                            <li class="nav-item dropdown no-arrow" >
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ auth()->user()->name }}{{ $img->savename ?? '' }} 님</span>&nbsp;
                                    
                                    @php
                                        $imgSrc = ( isset($img['pathDate'], $img['savename']) )
                                            ? asset('storage/img/' . $img['pathDate'] . '/' . $img['savename'])
                                            : asset('img/pro.png');
                                    @endphp
                                    <img class="img-profile rounded-circle"
                                        src="{{ $imgSrc }}" style="width:1.5rem; height:1.5rem;">
                                </a>

                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item cp" onclick="window.open('/infoChange?val={{ auth()->id() }}', 'popup', 'width=600,height=650'); return false;" >
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Info
                                    </a>

                                    @if( auth()->user()->is_admin === 'Y' )
                                    <a class="dropdown-item" href="{{ route('admin.list') }}">
                                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                        User
                                    </a>
                                    @endif

                                    @php
                                        $url = auth()->user()->is_admin === 'Y'
                                            ? route('boards.index')
                                            : route('request.index', ['id' => auth()->id()]);
                                    @endphp
                                    <a class="dropdown-item" href="{{ $url }}">
                                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Board
                                    </a>

                                    @if( auth()->user()->is_admin === 'Y' )
                                    <a class="dropdown-item" href="{{ route('notice.list') }}">
                                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Notice
                                    </a>
                                    @endif

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif
                    @endauth
                        <!-- <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li> -->
                </ul>
            </div>
    </div>
</nav>