<x-guest-layout>
    <div class="min-h-screen bg-[#000A44] flex flex-col" style="height: 100vh !important; min-height: 100vh !important; overflow: hidden;">

        <img src="{{ asset('images/blue.png') }}" alt="Blue Effect" class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-50 pointer-events-none" />


        <img src="{{ asset('images/clock.png') }}" alt="Clock" class="absolute top-[19%] right-[10%] w-[630px] h-[630px] rounded-full opacity-100 pointer-events-none animate-clock" />


        <div class="flex justify-between items-center px-10 py-6 mt-4">
            <img src="{{ asset('images/telemonit-logo.png') }}" alt="Telemonit Logo" class="w-[197px] h-[43px]" />

            <div class="settings-container relative">
                <img src="{{ asset('images/settings.png') }}" alt="Settings Icon" class="w-[47px] h-[45px]" />
                <img src="{{ asset('images/online.png') }}" alt="Online Status" class="absolute bottom-0 right-0 w-[14px] h-[13px]" />

                <div class="dropdown-menu absolute bg-[#1E2A44] rounded-lg shadow-lg mt-2 right-0 w-48 hidden" id="dropdown-menu">
                    <a href="{{ route('password.update') }}" class="block px-4 py-2 text-white hover:bg-gray-700 rounded-t-lg">Change Password</a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:bg-gray-700 rounded-b-lg">Logout</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="flex-1 flex justify-center items-center relative px-4">
            @yield('content')
        </div>




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
                display: none;
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


        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const settingsContainer = document.querySelector('.settings-container');
                const dropdownMenu = document.getElementById('dropdown-menu');


                settingsContainer.addEventListener('mouseenter', function () {
                    dropdownMenu.classList.remove('hidden');
                });


                document.addEventListener('mousemove', function (e) {
                    const rect = settingsContainer.getBoundingClientRect();
                    const menuRect = dropdownMenu.getBoundingClientRect();


                    const combinedRect = {
                        top: Math.min(rect.top, menuRect.top),
                        bottom: Math.max(rect.bottom, menuRect.bottom),
                        left: Math.min(rect.left, menuRect.left),
                        right: Math.max(rect.right, menuRect.right),
                    };


                    const margin = 16;
                    const isOutside = e.clientX < combinedRect.left - margin ||
                                     e.clientX > combinedRect.right + margin ||
                                     e.clientY < combinedRect.top - margin ||
                                     e.clientY > combinedRect.bottom + margin;

                    if (isOutside) {
                        dropdownMenu.classList.add('hidden');
                    }
                });


                dropdownMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });
        </script>
    </div>
</x-guest-layout>
