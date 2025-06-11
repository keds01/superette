# 🚀 Déploiement Laravel sur Railway.app

Ce guide explique comment déployer ton projet Laravel (superette) sur Railway.app, plateforme cloud simple et adaptée à PHP/MySQL.

---

## 1. Pré-requis
- Un compte Railway : https://railway.app/
- Un dépôt Git (GitHub, GitLab ou local)
- Clé APP_KEY Laravel (générée en local ou par Railway)

---

## 2. Préparation du projet

1. **Vérifie que ton `.env` local fonctionne** (connexion DB, mail, etc.)
2. **Ajoute/commite tout** (code, migrations, seeders, etc.)
3. **Supprime le fichier `netlify.toml`** (inutile sur Railway)

---

## 3. Création du projet Railway

1. Va sur https://railway.app/
2. Clique sur **"New Project"** > **"Deploy from GitHub repo"**
3. Sélectionne ton dépôt Laravel
4. Railway détecte automatiquement PHP (sinon, choisis "Other")

---

## 4. Ajout de la base de données

1. Dans l’onglet "Plugins" > **"Add Plugin"** > choisis **MySQL**
2. Railway crée la DB et te donne les variables d’environnement (host, user, password...)
3. Copie ces infos dans l’onglet "Variables" de Railway, adapte ton `.env` :
   - `DB_CONNECTION=mysql`
   - `DB_HOST=...`
   - `DB_PORT=3306`
   - `DB_DATABASE=...`
   - `DB_USERNAME=...`
   - `DB_PASSWORD=...`

---

## 5. Configuration des variables d’environnement

Dans Railway > "Variables" :
- **APP_KEY** : génère-la en local avec `php artisan key:generate --show`
- **APP_ENV** : `production`
- **APP_DEBUG** : `false`
- **LOG_CHANNEL** : `stack`
- **Autres** : adapte selon tes besoins (`MAIL_*`, etc.)

---

## 6. Configuration du build & start

Dans Railway (onglet "Deployments") :
- **Build command** : `composer install --no-dev --optimize-autoloader`
- **Start command** : `php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port $PORT`

> Railway expose automatiquement le port `$PORT` (ne pas mettre 8000/8080 en dur)

---

## 7. Déploiement

1. Clique sur "Deploy" (Railway va builder et lancer ton app)
2. Accède à l’URL publique fournie par Railway
3. Si tu vois Laravel, c’est OK !

---

## 8. Conseils post-déploiement
- Utilise un domaine personnalisé si besoin
- Active les backups MySQL dans Railway
- Pour les fichiers uploadés, utiliser un stockage externe (S3, etc.)
- Pour les jobs/queues, active le scheduler Railway ou utilise un worker dédié

---

## 9. Dépannage courant
- **Erreur 500** : vérifie `.env`, migrations, logs Railway
- **Pas de DB** : vérifie plugin MySQL, variables, migrations
- **Clé APP_KEY manquante** : génère-la et mets-la dans Railway

---

## 10. Ressources
- Docs Railway : https://docs.railway.app/
- Docs Laravel : https://laravel.com/docs/

---

**Déploiement 100% Laravel, sans hack, production-ready !**

Besoin d’un script d’automatisation, d’un Dockerfile ou d’une config CI/CD Railway ? Dis-le-moi !
