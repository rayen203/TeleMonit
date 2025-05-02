<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ request()->route('token') }}">

    <label>Email :</label>
    <input type="email" name="email" value="{{ request()->email }}" required>

    <label>Nouveau mot de passe :</label>
    <input type="password" name="password" required>

    <label>Confirmer le mot de passe :</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Réinitialiser le mot de passe</button>

</form>
