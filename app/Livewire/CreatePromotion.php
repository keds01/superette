<?php

namespace App\Livewire;

use Livewire\Component;

class CreatePromotion extends Component
{
    public $currentStep = 1;

    public function render()
    {
        return view('livewire.create-promotion');
    }

    public function nextStep()
    {
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function submitForm()
    {
        // Logique de soumission finale (à implémenter plus tard)
        // Pour l'instant, on peut juste afficher un message ou rediriger
        session()->flash('message', 'Formulaire soumis!');
        // return redirect()->route('promotions.index');
    }
}
