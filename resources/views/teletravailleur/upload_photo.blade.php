@extends('layouts.base-interface')

@section('content')

    <div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[500px] text-center transform scale-75">

        <!-- Avatars devant la carte -->
        <a href="#" onclick="selectAvatar('avatar1.png')" class="absolute w-[120px] h-[115px] rounded-full left-[300px] -top-[-120px] z-20">
            <img src="{{ asset('images/avatar1.png') }}" alt="Avatar Homme">
        </a>
        <a href="#" onclick="selectAvatar('avatar2.png')" class="absolute w-[120px] h-[115px] rounded-full right-[300px] -top-[-120px] z-20">
            <img src="{{ asset('images/avatar2.png') }}" alt="Avatar Femme">
        </a>







        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <br><br><br><br><br><br><br><br>


        <form method="POST" action="{{ route('teletravailleur.upload.photo', $token) }}" enctype="multipart/form-data">
            @csrf

            <!-- Zone de téléchargement interactive -->
            <div class="mb-4 flex justify-center  ">
                <div class="relative">

                    <input
                        type="file"
                        name="photoProfil"
                        id="photoProfil"
                        required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    />
                    <div style="font-weight: bold;"  class="text-[#E3EEF0] text-sm font-inter mt-1 w-[83px] h-[43px] flex items-center justify-center mx-auto">- OR -</div>

                    <div class="w-[290px] h-[49px] bg-[#2F9EB8] rounded-[57px] flex items-center justify-center text-black font-semibold font-poppins border-none focus:outline-none">
                        <span> + Drag your photo here</span>
                    </div>
                    @error('photoProfil')
                        <span class="mt-1 text-red-200 font-poppins text-sm block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <br>
            <!-- Bouton Next -->
            <div class="flex justify-end mt-4">
                <button
                    type="submit"
                    class="h-[55px] w-[205px] rounded-[57px] bg-[#2F9EB8] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200"
                >
                    Done →
                </button>
            </div>
        </form>
        <br><br><br><br><br>
        <!-- 3 points sous la carte -->
        <div class="flex justify-center mt-6 space-x-2">
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full opacity-50"></div>
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full opacity-50"></div>
            <div class="w-4 h-4 bg-[#D9D9D9] rounded-full "></div>
        </div>
    </div>

    <script>
        function selectAvatar(avatar) {
            // Créer un formulaire caché pour soumettre la sélection
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('teletravailleur.upload.photo', $token) }}";
            form.style.display = 'none';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = "{{ csrf_token() }}";

            const avatarInput = document.createElement('input');
            avatarInput.type = 'hidden';
            avatarInput.name = 'avatar';
            avatarInput.value = avatar;

            form.appendChild(csrfInput);
            form.appendChild(avatarInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>

@endsection
