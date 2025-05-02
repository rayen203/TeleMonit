<x-guest-layout>
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: linear-gradient(180deg, #000943 0%, #1E3A8A 100%);">
        <!-- Header with Logo and Dark Mode Toggle -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 40px;">
            <div style="display: flex; align-items: center;">
                <img src="{{ asset('images/telemonit-logo.png') }}" alt="Telemonit Logo" style="height: 24px;" />
            </div>
            <div>
                <label style="display: flex; align-items: center; position: relative;">
                    <span style="color: #FFFFFF; margin-right: 10px;">🌙</span>
                    <input type="checkbox" style="appearance: none; width: 40px; height: 20px; background: #D9D9D9; border-radius: 20px; position: relative; cursor: pointer;" />
                    <span style="position: absolute; width: 16px; height: 16px; background: #FFFFFF; border-radius: 50%; top: 2px; left: 2px; transition: left 0.3s;"></span>
                </label>
            </div>
        </div>

        <!-- Main Content (Card) -->
        <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
            <div style="position: relative; background: rgba(217, 217, 217, 0.15); border-radius: 40px; padding: 40px; width: 400px; text-align: center; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(8.7px); border: 2px solid #666666;">
                <!-- Clock Background (Using Image) -->
                <img src="{{ asset('images/clock.png') }}" alt="Clock Background" style="position: absolute; top: -50px; right: -100px; width: 500px; height: 500px; opacity: 0.8;" />

                <!-- Heading -->
                <h1 style="color: #E3EDEF; font-size: 40px; font-weight: 600; margin-bottom: 40px; line-height: 1.2; letter-spacing: 4px; font-family: 'Poppins', sans-serif; text-align: center;">
                    UPGRADE YOUR<br>PRODUCTIVITY?
                </h1>

                <!-- Session Status -->
                <x-auth-session-status style="margin-bottom: 16px; color: #E3EDEF; font-family: 'Poppins', sans-serif;" :status="session('status')" />

                @if (session('success'))
                    <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; font-family: 'Poppins', sans-serif;">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->has('error'))
                    <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; font-family: 'Poppins', sans-serif;">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div style="width: 300px; margin: 0 auto;">
                        <label for="email" style="display: block; color: #E3EDEF; font-size: 16px; font-family: 'Poppins', sans-serif; font-weight: 600; text-align: left; margin-bottom: 8px; line-height: 24px; letter-spacing: 0px;">Email:</label>
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            :value="old('email') ?? session('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Email:"
                            style="width: 100%; height: 50px; background: #D9D9D9; border-radius: 57px; border: none; padding: 15px 20px; font-size: 16px; color: #7D7D7D; font-weight: 600; font-family: 'Poppins', sans-serif;"
                        />
                        <x-input-error :messages="$errors->get('email')" style="margin-top: 8px; color: #f8d7da; font-family: 'Poppins', sans-serif;" />
                    </div>

                    <!-- Password -->
                    <div style="width: 300px; margin: 16px auto 0;">
                        <label for="password" style="display: block; color: #E3EDEF; font-size: 16px; font-family: 'Poppins', sans-serif; font-weight: 600; text-align: left; margin-bottom: 8px; line-height: 24px; letter-spacing: 0px;">Password:</label>
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Password:"
                            style="width: 100%; height: 50px; background: #D9D9D9; border-radius: 57px; border: none; padding: 15px 20px; font-size: 16px; color: #7D7D7D; font-weight: 600; font-family: 'Poppins', sans-serif;"
                        />
                        <x-input-error :messages="$errors->get('password')" style="margin-top: 8px; color: #f8d7da; font-family: 'Poppins', sans-serif;" />
                    </div>

                    <!-- Remember Me -->
                    <div style="text-align: left; margin-top: 12px;">
                        <label for="remember_me" style="display: inline-flex; align-items: center;">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #D1D5DB; accent-color: #4F46E5;"
                            >
                            <span style="margin-left: 8px; font-size: 14px; color: #E3EDEF; font-family: 'Poppins', sans-serif; font-weight: 600;">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <!-- Forgot Password and Log In Button -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 12px;">
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                style="text-decoration: underline; font-size: 14px; color: #E3EDEF; font-family: 'Poppins', sans-serif; font-weight: 600; text-align: right;"
                            >
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <button
                            type="submit"
                            style="width: 150px; height: 50px; background: #D9E0E0; border-radius: 57px; border: none; font-size: 16px; font-weight: 600; color: #000943; font-family: 'Poppins', sans-serif;"
                        >
                            LOG IN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
