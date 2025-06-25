@extends('layouts.app2')
        
        <!-- Page Content-->
        @section('content')

        @auth
            @if (Auth::check())
                @php
                    $user = Auth::user();

                    if ($user->is_admin === 'Y') {
                        $url = route('admin.list');
                        $title = 'User';
                        $class = 'bi bi-person-circle';
                    } else {
                        $url = route('profile.edit', ['id' => $user->id]);
                        $title = 'Info';
                        $class = 'bi bi-info-circle';
                    }
                @endphp
            @endif
        @endauth
        <header class="py-5">
            <div class="container px-lg-5">
                <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                    <div class="m-4 m-lg-5">
                        <h1 class="display-5 fw-bold">welcome, mj world >.<</h1>
                        <!-- <p class="fs-4">Bootstrap utility classes are used to create this jumbotron since the old component has been removed from the framework. Why create custom CSS when you can use utilities?</p> -->
                        <a class="btn btn-primary btn-lg mt-3" href="#!">Happy</a>
                    </div>
                </div>
            </div>
        </header>
        
        <section class="pt-4 mt-5">
            <div class="container px-lg-5">
                <!-- Page Features-->
                <div class="row gx-lg-5">
                    <!-- info -->
                    <a class="col-lg-6 col-xxl-4 mb-5" href="{{ $url }}">
                        <div>
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="{{ $class }}"></i></div>
                                    <h2 class="fs-4 fw-bold">{{ $title }}</h2>
                                    <!-- <p class="mb-0">With Bootstrap 5, we've created a fresh new layout for this template!</p> -->
                                </div>
                            </div>
                        </div>
                    </a>

                    @php
                        if ($user->is_admin && $user->is_admin == 'Y') {
                            $url = url('/boards');
                        } else {
                            $url = url('/request?id=' . $user->id);
                        }
                    @endphp

                    <a class="col-lg-6 col-xxl-4 mb-5" href="{{ $url }}">
                        <div>
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                                    <h2 class="fs-4 fw-bold">Board</h2>
                                    <!-- <p class="mb-0">Board</p> -->
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- 
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-patch-check"></i></div>
                                <h2 class="fs-4 fw-bold">A name you trust</h2>
                                <p class="mb-0">Start Bootstrap has been the leader in free Bootstrap templates since 2013!</p>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>
        @endsection


