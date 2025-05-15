<x-guest-layout>
    <div class="min-h-screen bg-[#000A44] flex flex-col" style="height: 100vh !important; min-height: 100vh !important; overflow: hidden;">
        <!-- Image blue.png (cercle centré) -->
        <img src="{{ asset('images/blue.png') }}" alt="Blue Effect" class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-50 pointer-events-none" />

        <!-- Background Clock Image -->
        <img src="{{ asset('images/clock.png') }}" alt="Clock" class="absolute top-[19%] right-[10%] w-[630px] h-[630px] rounded-full opacity-100 pointer-events-none animate-clock" />

        <!-- Header -->
        <div class="flex justify-between items-center px-10 py-6 mt-4">
            <img src="{{ asset('images/telemonit-logo.png') }}" alt="Telemonit Logo" class="w-[197px] h-[43px]" />
            <!-- Icône settings avec menu déroulant -->
            <div class="settings-container relative">
                <img src="{{ asset('images/settings.png') }}" alt="Settings Icon" class="w-[47px] h-[45px]" />
                <img src="{{ asset('images/online.png') }}" alt="Online Status" class="absolute bottom-0 right-0 w-[14px] h-[13px]" />
                <!-- Menu déroulant -->
                <div class="dropdown-menu absolute bg-[#1E2A44] rounded-lg shadow-lg mt-2 right-0 w-48 hidden" id="dropdown-menu">
                    <a href="{{ route('password.update') }}" class="block px-4 py-2 text-white hover:bg-gray-700 rounded-t-lg">Change Password</a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:bg-gray-700 rounded-b-lg">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contenu spécifique à chaque page -->
        <div class="flex-1 flex justify-center items-center relative px-4">
            @yield('content')
        </div>



        <!-- Animation pour clock.png -->
        <style>
            @keyframes rotateClock {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            .animate-clock {
                animation: rotateClock 20s linear infinite;
                transform-origin: center center;
            }

            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: hidden;
            }

            .scrollbar-hidden::-webkit-scrollbar {
                display: none; /* Chrome, Safari, Edge */
            }

            .settings-container {
                position: relative;
            }

            .dropdown-menu {
                top: 100%;
                z-index: 50;
            }

            .dropdown-menu a, .dropdown-menu button {
                font-size: 14px;
                color: #E1E4E6;
            }

        </style>

        <!-- Script pour gérer l'affichage du menu -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const settingsContainer = document.querySelector('.settings-container');
                const dropdownMenu = document.getElementById('dropdown-menu');

                // Afficher le menu au survol
                settingsContainer.addEventListener('mouseenter', function () {
                    dropdownMenu.classList.remove('hidden');
                });

                // Gérer la disparition du menu lorsque la souris s'éloigne
                document.addEventListener('mousemove', function (e) {
                    const rect = settingsContainer.getBoundingClientRect();
                    const menuRect = dropdownMenu.getBoundingClientRect();

                    // Zone combinée (avatar + menu)
                    const combinedRect = {
                        top: Math.min(rect.top, menuRect.top),
                        bottom: Math.max(rect.bottom, menuRect.bottom),
                        left: Math.min(rect.left, menuRect.left),
                        right: Math.max(rect.right, menuRect.right),
                    };

                    // Ajouter une marge de 16px (≈ 1cm sur un écran standard)
                    const margin = 16;
                    const isOutside = e.clientX < combinedRect.left - margin ||
                                     e.clientX > combinedRect.right + margin ||
                                     e.clientY < combinedRect.top - margin ||
                                     e.clientY > combinedRect.bottom + margin;

                    if (isOutside) {
                        dropdownMenu.classList.add('hidden');
                    }
                });

                // Empêcher la propagation des clics dans le menu pour éviter qu'il ne se ferme
                dropdownMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });
        </script>
    </div>
</x-guest-layout>
