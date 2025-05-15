@extends('layouts.base-interface')

@section('content')
<div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75 overflow-y-auto scrollbar-hidden">
    <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-8 leading-tight tracking-wide font-poppins">
        UPDATE YOUR <br> PROFILE?
    </h1>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 font-poppins">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col items-center space-y-6">
        @csrf
        @method('PATCH')

        <!-- Champs communs pour tous les utilisateurs -->
        <div class="w-[745px]">

            <input type="text" id="nom" name="nom" value="{{ old('nom', auth()->user()->nom) }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder=" Last Name:">
            <x-input-error :messages="$errors->get('nom')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>

        <div class="w-[745px]">

            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="First Name:">
            <x-input-error :messages="$errors->get('prenom')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>

        <div class="w-[745px]">

            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                   class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="Email:">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>

        <!-- Champs spécifiques aux télétravailleurs -->
        @if(auth()->user()->teletravailleur)
            <div class="w-[745px]">

                <input type="text" id="cin" name="cin" value="{{ old('cin', auth()->user()->teletravailleur->cin ?? '') }}" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="CIN:">
                <x-input-error :messages="$errors->get('cin')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <div class="w-[745px]">

                <input type="text" id="telephone" name="telephone" value="{{ old('telephone', auth()->user()->teletravailleur->telephone ?? '') }}" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="Phone Number:">
                <x-input-error :messages="$errors->get('telephone')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <div class="w-[745px]">

                <input type="text" id="adresse" name="adresse" value="{{ old('adresse', auth()->user()->teletravailleur->adresse ?? '') }}" required
                       class="w-full h-[88px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                       placeholder="Address:">
                <x-input-error :messages="$errors->get('adresse')" class="mt-1 text-red-200 font-poppins text-sm" />
            </div>

            <div class="w-[745px] mb-4 flex justify-right">
                <div class="relative">
                    <input
                        type="file"
                        name="photoProfil"
                        id="photoProfil"
                        required
                        class="absolute inset-0 w-full h-[49px] opacity-0 cursor-pointer"
                    />
                    <div class="w-[290px] h-[49px] bg-[#2F9EB8] rounded-[57px] flex items-center justify-center text-black font-semibold font-poppins border-none focus:outline-none">
                        <span> + Drag your photo here</span>
                    </div>
                    @if(auth()->user()->photoProfil)
                        <img src="{{ Storage::url(auth()->user()->photoProfil) }}" alt="Photo de profil actuelle" class="mt-2 max-w-[100px]">
                    @endif
                    <x-input-error :messages="$errors->get('photoProfil')" class="mt-1 text-red-200 font-poppins text-sm" />
                </div>
            </div>
        @endif
            <br>
        <!-- Bouton Mettre à jour -->
        <button type="submit"
                class="h-[78px] w-[245px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200">
            Update
        </button>
    </form>


</div>
@endsection
