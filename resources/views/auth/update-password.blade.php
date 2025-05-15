@extends('layouts.base-interface')

@section('content')
    <div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
        <img src="{{ asset('images/avatar1.png') }}" alt="User Avatar" class="absolute w-[190px] h-[183px] rounded-full left-1/2 transform -translate-x-1/2 -top-[75px]">

        <br>
        <br>
        <br><br><br>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 font-poppins">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('password') || $errors->has('new_password') || $errors->has('confirm_password'))
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
                @if ($errors->has('password'))
                    {{ $errors->first('password') }}
                @elseif ($errors->has('new_password'))
                    {{ $errors->first('new_password') }}
                @elseif ($errors->has('confirm_password'))
                    {{ $errors->first('confirm_password') }}
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Old Password -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Old password:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>
            <br>
            <!-- New Password -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="new_password"
                    name="new_password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="New password:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                <x-input-error :messages="$errors->get('new_password')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>
            <br>
            <!-- Confirm Password -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm password:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                <x-input-error :messages="$errors->get('new_password_confirmation')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <br>
            <!-- Boutons Back et Confirm -->
            <div class="flex justify-center mt-4 space-x-4">
                <!-- Bouton Back -->
                <a
                    href="{{ auth()->user()->teletravailleur ? route('teletravailleur.dashboard') : route('admin.dashboard') }}"
                    class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200 flex items-center justify-center"
                >
                    Back
                </a>
                <!-- Bouton Confirm -->
                <button
                    type="submit"
                    class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
                >
                    Confirm
                </button>
            </div>
        </form>


    </div>




@endsection
