@extends('layouts.base-interface')

@section('content')
    <div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
        <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-16 leading-tight tracking-wide font-poppins">
            UPGRADE YOUR<br>PRODUCTIVITY?
        </h1>

        <br>
        <br>
        <x-auth-session-status class="mb-4 text-[#E3EDEF] font-poppins" :status="session('status')" />

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 font-poppins">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
                {{ $errors->first('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf


            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px"
                    id="email"
                    name="email"
                    type="email"
                    :value="old('email') ?? session('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Email:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl "
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <br>


            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px"
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Password:"

                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <br>

            <div class="flex flex-col mb-4 w-[745px] mx-auto">

                <div class="flex items-center mb-4 text-left">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="w-6 h-6 rounded border border-gray-300 accent-indigo-600"
                    />
                    <label for="remember_me" class="ml-2 text-[#E3EDEF] text-base font-semibold font-poppins">
                        {{ __('Remember me') }}
                    </label>
                </div>

                <br>


                <div class="flex justify-between items-center mt-4">
                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-base underline text-[#E3EDEF] font-semibold font-poppins"
                        >
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
                    >
                        LOG IN
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
