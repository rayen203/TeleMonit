@extends('layouts.base-interface')

@section('content')
    <div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
        <img src="{{ asset('images/avatar1.png') }}" alt="User Avatar" class="absolute w-[190px] h-[183px] rounded-full left-1/2 transform -translate-x-1/2 -top-[75px]">

        <br>
        <br>
        <br><br><br>



        @if (session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('teletravailleur.complete.store', $token) }}">
            @csrf

            <!-- CIN -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="cin"
                    name="cin"
                    type="text"
                    value="{{ old('cin') }}"
                    required
                    placeholder="CIN:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                @error('cin')
                    <span class="mt-1 text-red-200 font-poppins text-sm block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone number -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="telephone"
                    name="telephone"
                    type="text"
                    value="{{ old('telephone') }}"
                    required
                    placeholder="Phone number:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                @error('telephone')
                    <span class="mt-1 text-red-200 font-poppins text-sm block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-4 text-center">
                <x-text-input
                    style="border-radius: 57px;"
                    id="adresse"
                    name="adresse"
                    type="text"
                    value="{{ old('adresse') }}"
                    required
                    placeholder="Address:"
                    class="w-[745px] h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                />
                @error('adresse')
                    <span class="mt-1 text-red-200 font-poppins text-sm block">{{ $message }}</span>
                @enderror
            </div>

            <br>
            <!-- Bouton Next -->
            <div class="flex justify-center mt-4">
                <button
                    type="submit"
                    class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
                >
                    Next →
                </button>
            </div>
        </form>
        <br><br><br><br><br>
        <!-- 3 points sous la carte -->
        <div class="flex justify-center mt-6 space-x-2">
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full opacity-50"></div>
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full "></div>
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full opacity-50"></div>
        </div>
    </div>
@endsection
