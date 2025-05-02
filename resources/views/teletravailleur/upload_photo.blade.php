@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('teletravailleur.upload.photo', $token) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="photoProfil">Photo de Profil</label>
        <input type="file" name="photoProfil" id="photoProfil" class="form-control" required>
        @error('photoProfil')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Next</button>
</form>
