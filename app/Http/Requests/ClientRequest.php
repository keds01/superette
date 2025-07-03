<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'statut' => ['required', Rule::in(['actif', 'inactif'])],
            'type' => ['required', Rule::in(['particulier', 'entreprise'])],
        ];

        // Validation d'unicité du code (pour la création et la mise à jour)
        if ($this->route('client')) { // Si on est en train de mettre à jour un client
            $rules['code'] = ['required', 'string', 'max:20', Rule::unique('clients', 'code')->ignore($this->route('client'))];
        } else { // Si on est en train de créer un nouveau client
            $rules['code'] = ['required', 'string', 'max:20', Rule::unique('clients', 'code')];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Générer un code si il n'est pas fourni (uniquement à la création)
        if ($this->isMethod('POST') && !$this->has('code')) {
            $this->merge(['code' => $this->generateUniqueCode()]);
        }
    }

    /**
     * Generate a unique client code.
     *
     * @return string
     */
    protected function generateUniqueCode(): string
    {
        $prefix = 'CL';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -4)); // Add a random part

        $code = $prefix . $timestamp . $random;

        // Ensure the code is unique (optional but recommended)
        while (\App\Models\Client::where('code', $code)->exists()) {
            $timestamp = now()->format('YmdHis');
            $random = strtoupper(substr(uniqid(), -4));
            $code = $prefix . $timestamp . $random;
        }

        return $code;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'telephone.required' => 'Le téléphone est requis.',
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut sélectionné est invalide.',
            'type.required' => 'Le type de client est requis.',
            'type.in' => 'Le type de client sélectionné est invalide.',
            'code.required' => 'Le code client est requis.',
            'code.unique' => 'Ce code client existe déjà.',
            'code.max' => 'Le code client ne doit pas dépasser 20 caractères.',
        ];
    }

    /**
     * Get validated data from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Si on demande une clé spécifique, on ne modifie pas les données
        if ($key !== null) {
            return $validated;
        }
        
        // S'assurer que le code est inclus dans les données validées
        if ($this->method() === 'POST' && is_array($validated)) {
            $validated['code'] = $this->generateUniqueCode();
        }
        
        return $validated;
    }
}