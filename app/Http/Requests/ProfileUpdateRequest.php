<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('utilisateurs')->ignore($this->user()->id)],

        ];

        // Si l'utilisateur est un télétravailleur, ajouter les règles pour la photo de profil
        if ($this->user()->teletravailleur) {
            $rules['photoProfil'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',
            'photoProfil.image' => 'Le fichier doit être une image (jpeg, png, jpg, gif).',
            'photoProfil.mimes' => 'Le format de l\'image n\'est pas valide (jpeg, png, jpg, gif uniquement).',
            'photoProfil.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo.',

        ];
    }
}
