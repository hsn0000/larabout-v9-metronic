<x-template-layout type="auth">
    <div class="login-form text-center text-white p-7 position-relative overflow-hidden">
        <!--begin::Login Header-->
        <div class="d-flex flex-center mb-15">
            <a href="{{ url('') }}">
                <img src="{{ asset('template/media/logos/stars.png') }}" class="max-h-75px" alt="" />
            </a>
        </div>
        <!--end::Login Header-->
        <!--begin::Login Sign in form-->
        <div class="login-signin">
            <div class="mb-4">
                <h3 class="opacity-40 font-weight-normal">Sign In To {{ config('app.name') }}</h3>
                <p class="opacity-40">Enter your details to login to your account:</p>
            </div>
            <div class="mt-10">
                <x-jet-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="post" class="form" action="{{ route('login') }}" autocomplete="off" id="kt_login_signin_form">
                    @csrf
                    <div class="form-group">
                        <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8" type="text" placeholder="Email or Username" name="name" autocomplete="off" tabindex="1" />
                    </div>
                    <div class="form-group">
                        <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8" type="password" placeholder="Password" name="password" tabindex="2" />
                    </div>
                    <div class="form-group text-center mt-10">
                        <button id="kt_login_signin_submit" class="btn btn-pill btn-primary opacity-90 px-15 py-3" type="submit" tabindex="3">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
        <!--end::Login Sign in form-->
    </div>
</x-template-layout>
