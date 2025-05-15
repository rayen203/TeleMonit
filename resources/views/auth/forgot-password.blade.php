@extends('layouts.base-interface')

@section('content')
    <div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
        <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-16 leading-tight tracking-wide font-poppins">
            RESET YOUR<br>PASSWORD?
        </h1>

        <br>
        <br>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 font-poppins">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('email'))
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Email :"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <br>
            <br>
            <br>
           <!-- Boutons -->
            <div class="flex justify-center mt-4 space-x-4">
                <!-- Bouton Back -->
                <a
                href="{{ route('login') }}"
                class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200 flex items-center justify-center"
            >
                Back
            </a>
                <!-- Bouton Envoyer -->
                <button
                    type="submit"
                    class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
                >
                    Send link
                </button>

            </div>


        </form>
    </div>
@endsection
