# 🚀 Prototype API Symfony

## 🐳 Infrastructure Docker (PostgreSQL)

Pour initialiser l'environnement de développement de ce prototype, il est nécessaire de configurer correctement vos variables d'environnement locales afin que Docker et Symfony soient parfaitement synchronisés.

### 📋 Prérequis (À faire)

**1. Créer le fichier d'environnement local**
Créez un fichier `.env.local` à la racine du projet. Ce fichier est ignoré par Git pour des raisons de sécurité.

**2. Générer les clés JWT**
Générez la paire de clés asymétriques pour l'authentification. La commande va vous demander une *passphrase* ou la générer automatiquement :
```bash
php bin/console lexik:jwt:generate-keypair
```

**3. Configurer les variables**
Copiez le bloc suivant dans votre fichier `.env.local` et complétez les valeurs :

```env
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase_generee_ici
###< lexik/jwt-authentication-bundle ###

# --- Configuration Docker ---
POSTGRES_DB=future
POSTGRES_USER=app
POSTGRES_PASSWORD=votre_mot_de_passe_robuste

# --- Configuration Doctrine ---
# Doctrine utilise l'interpolation pour récupérer les identifiants Docker
DATABASE_URL="postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@127.0.0.1:5432/${POSTGRES_DB}?serverVersion=16&charset=utf8"
```

### 🚀 Lancement de l'infrastructure

Une fois la configuration terminée, démarrez les conteneurs en forçant Docker Compose à lire votre fichier local pour l'initialisation de PostgreSQL :

```bash
docker compose --env-file .env.local up -d
```
