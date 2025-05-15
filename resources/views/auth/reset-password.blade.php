@extends('layouts.base-interface')

@section('content')
<div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
    <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-8 leading-tight tracking-wide font-poppins">
        RESET YOUR<br>PASSWORD?
    </h1>
        <form method="POST" action="{{ route('password.store') }}" class="flex flex-col items-center space-y-6 ">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">



            <!-- Champ Email -->
            <div class="w-[745px]" style="border-radius: 77px; ">

                <input   type="email" name="email" id="email" value="{{ request()->email }}" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="Email:">
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <!-- Champ Nouveau mot de passe -->
            <div class="w-[745px]">

                <input type="password" name="password" id="password" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="Password:">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <!-- Champ Confirmer mot de passe -->
            <div class="w-[745px]">

                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="Confirm Password:">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <br>

            <!-- Bouton Réinitialiser -->
            <button type="submit"
                    class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200">
                Reset password
            </button>
        </form>


    </div>
@endsection
