<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Fonctionnalité Multi-Boutiques (Superettes)

## Présentation

Le système supporte désormais la gestion de plusieurs boutiques centralisée dans une seule instance de l'application. Cette architecture multi-tenant permet :

- À un Super Admin de gérer plusieurs superettes (boutiques) de manière isolée
- De cloisonner complètement les données entre les différentes superettes
- D'assigner des employés à une superette spécifique

## Architecture

L'implémentation utilise une approche de **multi-tenancy à base de données unique** :

- Une table `superettes` centrale contient les informations de chaque boutique
- Une colonne `superette_id` dans les tables principales établit la relation et le cloisonnement
- Des global scopes Eloquent automatisent la filtration des données par superette

## Rôles et accès

Le système distingue deux types principaux d'utilisateurs :

1. **Super Admin**
   - N'est rattaché à aucune superette par défaut
   - Peut naviguer entre les superettes via un sélecteur dans la barre de navigation
   - Peut créer/modifier/supprimer des superettes
   - Peut assigner des utilisateurs à des superettes

2. **Utilisateurs standards** (employés, caissiers, etc.)
   - Obligatoirement rattachés à une superette spécifique
   - Ne peuvent voir que les données de leur propre superette
   - Ne peuvent pas changer de contexte

## Navigation et interface

- Les Super Admins sont redirigés vers une page de sélection de superette s'ils n'en ont pas sélectionné une
- Un badge dans l'interface indique la superette active pour les Super Admins
- Les utilisateurs standards n'ont pas besoin de sélectionner une superette, leur contexte est automatiquement fixé

## Commandes

Pour créer une nouvelle superette via la ligne de commande :

```bash
php artisan make:superette "Nom de la superette" [options]
```

Options disponibles :
- `--code` : Code unique (généré automatiquement si non fourni)
- `--adresse` : Adresse physique
- `--telephone` : Numéro de téléphone
- `--email` : Email de contact
- `--description` : Description détaillée

## Migrations et modèles

Pour ajouter une nouvelle entité au système multi-boutiques, suivez ces étapes :

1. Ajoutez un champ `superette_id` à la table via une migration
2. Ajoutez le trait `HasSuperette` au modèle Eloquent correspondant
3. Ajoutez `superette_id` à la liste des champs fillables du modèle

Exemple :
```php
// Dans le modèle
use App\Traits\HasSuperette;

class MonModele extends Model
{
    use HasSuperette;
    
    protected $fillable = [
        'superette_id',
        // autres champs...
    ];
}
```

Grâce au global scope automatiquement appliqué, toutes les requêtes sur ce modèle seront filtrées par la superette active du contexte utilisateur.
