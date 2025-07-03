<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Superette;
use Illuminate\Support\Str;
use Exception;

class CreateSuperetteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superette 
                            {nom : Nom de la superette}
                            {--code= : Code unique de la superette (généré automatiquement si non fourni)}
                            {--adresse= : Adresse physique de la superette}
                            {--telephone= : Numéro de téléphone de la superette}
                            {--email= : Adresse email de la superette}
                            {--description= : Description de la superette}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer une nouvelle superette dans le système';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nom = $this->argument('nom');
        $code = $this->option('code') ?? $this->generateUniqueCode($nom);
        
        try {
            $superette = Superette::create([
                'nom' => strtoupper($nom),
                'code' => strtoupper($code),
                'adresse' => $this->option('adresse'),
                'telephone' => $this->option('telephone'),
                'email' => $this->option('email'),
                'description' => $this->option('description'),
                'actif' => true,
            ]);
            
            $this->info("✓ La superette \"{$superette->nom}\" a été créée avec succès!");
            $this->table(
                ['ID', 'Nom', 'Code', 'Adresse', 'Téléphone', 'Email'],
                [[$superette->id, $superette->nom, $superette->code, $superette->adresse ?? '-', $superette->telephone ?? '-', $superette->email ?? '-']]
            );
        } catch (Exception $e) {
            $this->error("Erreur lors de la création de la superette: {$e->getMessage()}");
        }
    }

    /**
     * Génère un code unique pour la superette basé sur son nom
     * 
     * @param string $nom
     * @return string
     */
    protected function generateUniqueCode(string $nom): string
    {
        // Créer un code à partir des 3 premiers caractères du nom + un nombre aléatoire à 3 chiffres
        $prefix = Str::upper(Str::substr(Str::slug($nom, ''), 0, 3));
        $code = $prefix . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Vérifier si le code existe déjà, si oui, en générer un autre
        while (Superette::where('code', $code)->exists()) {
            $code = $prefix . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
