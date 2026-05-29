@extends('layout.master-mini')
@section('content')

    <div class="content-wrapper d-flex align-items-center justify-content-center auth theme-one" style="background-image: url({{ url('assets/images/auth/login_1.jpg') }}); background-size: cover;">
        <div class="row w-100">
            <div class="col-lg-4 mx-auto">
                <div class="auto-form-wrapper">
                    <form action="{{ route('login')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="label">Username</label>
                            <div class="input-group">
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Username"
                                       value="{{ old('email') }}"
                                       required
                                       autocomplete="email"
                                       autofocus>
                                @error('password')
                                <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="mdi mdi-check-circle-outline">{{ $message }}</i>
                                        </span>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="*********"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                >
                                @error('password')
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="mdi mdi-check-circle-outline">{{ $message }}</i>
                                        </span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary submit-btn btn-block">Login</button>
                        </div>
                        <div class="form-group d-flex justify-content-between">
                            {{--
                            <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" checked> Keep me signed in </label>
                            </div>

                            <a href="#" class="text-small forgot-password text-black">Forgot Password</a>
                            --}}
                        </div>
                    </form>
                </div>

                <p class="footer-text text-center">copyright © 2026 KingWizard Security Agency. All rights reserved.</p>
            </div>
        </div>
    </div>

@endsection


