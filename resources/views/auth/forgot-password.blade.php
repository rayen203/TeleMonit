<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label>Email :</label>
    <input type="email" name="email" required>
    <button type="submit">Envoyer le lien de réinitialisation</button>
</form>
@if (session('status'))
    <p>{{ session('status') }}</p>
@endif
