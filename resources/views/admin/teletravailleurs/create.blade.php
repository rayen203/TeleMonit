@extends('layouts.base-interface')

@section('content')
<div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
    <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-8 leading-tight tracking-wide font-poppins">
        Create a remote <br>  worker?
    </h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 font-poppins">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teletravailleur.store') }}" class="flex flex-col items-center space-y-6">
        @csrf

        <!-- Champ Nom -->
        <div class="w-[745px]">

            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="Last Name:">
            @error('nom')
                <div class="mt-1 text-red-200 font-poppins text-sm">{{ $message }}</div>
            @enderror
        </div>

        <!-- Champ Prénom -->
        <div class="w-[745px]">

            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="First Name:">
            @error('prenom')
                <div class="mt-1 text-red-200 font-poppins text-sm">{{ $message }}</div>
            @enderror
        </div>

        <!-- Champ Email -->
        <div class="w-[745px]">

            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="Email:">
            @error('email')
                <div class="mt-1 text-red-200 font-poppins text-sm">{{ $message }}</div>
            @enderror
        </div>

        <br><br>

        <!-- Boutons -->
        <div class="flex justify-center mt-4 space-x-4">
            <!-- Bouton Back to Admin Dashboard -->
            <a
                href="{{ route('admin.dashboard') }}"
                class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200 flex items-center justify-center"
            >
                Back
            </a>

            <!-- Bouton Créer -->
            <button
                type="submit"
                class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
            >
                Create
            </button>


        </div>
            </form>


</div>
@endsection
