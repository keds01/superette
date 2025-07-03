<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Superette;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CreateSuperette extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superette:create 
                            {--nom= : Nom de la superette}
                            {--code= : Code unique de la superette}
                            {--adresse= : Adresse de la superette}
                            {--telephone= : Numéro de téléphone}
                            {--email= : Email de contact}
                            {--description= : Description de la superette}
                            {--with-admin : Créer également un utilisateur administrateur}';

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
        $this->info('🏪 Création d\'une nouvelle superette');
        $this->line('----------------------------------------');

        // Récupérer ou demander les informations
        $nom = $this->option('nom') ?: $this->ask('Nom de la superette');
        
        $code = $this->option('code');
        if (!$code) {
            $code = $this->ask('Code unique (ex: SP002)');
            
            // Vérifier si le code existe déjà
            while (Superette::where('code', $code)->exists()) {
                $this->error('Ce code existe déjà.');
                $code = $this->ask('Veuillez choisir un autre code');
            }
        }
        
        $adresse = $this->option('adresse') ?: $this->ask('Adresse', '');
        $telephone = $this->option('telephone') ?: $this->ask('Téléphone', '');
        $email = $this->option('email') ?: $this->ask('Email', '');
        $description = $this->option('description') ?: $this->ask('Description (optionnelle)', '');
        
        // Validation des données
        $validator = Validator::make([
            'nom' => $nom,
            'code' => $code,
            'email' => $email,
        ], [
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:superettes,code',
            'email' => 'nullable|email|max:255',
        ]);
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }
        
        // Création de la superette
        try {
            $superette = Superette::create([
                'nom' => $nom,
                'code' => $code,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'email' => $email,
                'description' => $description,
                'actif' => true
            ]);
            
            $this->info("✅ Superette '{$nom}' créée avec succès! (ID: {$superette->id})");
            $this->info("Les utilisateurs seront redirigés vers la page de sélection de supérette après la création.");
            
            // Créer un utilisateur admin si demandé
            if ($this->option('with-admin') || $this->confirm('Voulez-vous créer un utilisateur administrateur pour cette superette?', false)) {
                $this->createAdmin($superette);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Erreur lors de la création de la superette: {$e->getMessage()}");
            return 1;
        }
    }
    
    /**
     * Créer un utilisateur administrateur pour la superette
     */
    protected function createAdmin(Superette $superette)
    {
        $this->info('');
        $this->info('👤 Création d\'un utilisateur administrateur');
        $this->line('----------------------------------------');
        
        $name = $this->ask('Nom de l\'utilisateur');
        $email = $this->ask('Email de l\'utilisateur');
        
        // Vérifier si l'email existe déjà
        while (User::where('email', $email)->exists()) {
            $this->error('Cet email existe déjà.');
            $email = $this->ask('Veuillez choisir un autre email');
        }
        
        $password = $this->secret('Mot de passe (laissez vide pour générer automatiquement)');
        
        if (empty($password)) {
            $password = Str::random(10);
            $this->line("Mot de passe généré: <fg=yellow>{$password}</>");
        }
        
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'superette_id' => $superette->id
            ]);
            
            // Attribuer le rôle d'administrateur
            if (class_exists('\Spatie\Permission\Models\Role')) {
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
                if ($adminRole) {
                    $user->assignRole($adminRole);
                    $this->info("✅ Rôle 'admin' attribué à l'utilisateur.");
                } else {
                    $this->warn("⚠️ Rôle 'admin' non trouvé. L'utilisateur a été créé sans rôle.");
                }
            }
            
            $this->info("✅ Utilisateur '{$name}' créé avec succès!");
            $this->line("   Email: <fg=yellow>{$email}</>");
            $this->line("   Mot de passe: <fg=yellow>{$password}</>");
            
        } catch (\Exception $e) {
            $this->error("Erreur lors de la création de l'utilisateur: {$e->getMessage()}");
        }
    }
} 