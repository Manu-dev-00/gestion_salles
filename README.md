### Gestion de Salles 🏢

Application web permettant de gérer les réservations de salles (ajout, modification, suppression, consultation).  
Ce projet est développé en **PHP/MySQL** avec une interface simple et intuitive.

---

### 🚀 Fonctionnalités
- Authentification des utilisateurs (connexion/inscription).
- Consultation des salles disponibles.
- Réservation d’une salle avec date et heure.
- Visualisation des réservations personnelles.
- Annulation ou modification d’une réservation.
- Interface administrateur pour gérer les salles et les utilisateurs.

---

### 📦 Installation

## Prérequis
- PHP >= 7.4
- Serveur web (Apache/Nginx)
- Base de données MySQL/MariaDB
- Composer (si dépendances externes)

## Étapes
1. Cloner le dépôt :
   ```bash
   git clone https://github.com/Manu-dev-00/gestion_salles.git

2. Placer le projet dans le dossier de votre serveur web (ex: htdocs ou www).

3. Importer la base de données :

Fichier SQL disponible dans /database/gestion_salles.sql.

Créer une base gestion_salles et importer le script.

4. Configurer la connexion à la base dans le fichier :

## /config/db.php
avec vos identifiants MySQL.

5. Lancer le serveur et accéder à :

http://localhost/gestion_salles

---

### 🖥️ Utilisation
- Créez un compte ou connectez-vous.

- Consultez les salles disponibles.

- Réservez une salle en choisissant date et heure.

- Gérez vos réservations via l’onglet Mes Réservations.

---

### 🤝 Contribution
Les contributions sont les bienvenues !
Pour proposer une amélioration :

1. Forkez le projet.

2. Créez une branche (git checkout -b feature/ma-fonctionnalite).

3. Faites vos modifications et commit (git commit -m 'Ajout nouvelle fonctionnalité').

4. Poussez la branche (git push origin feature/ma-fonctionnalite).

5. Ouvrez une Pull Request.

### 📄 Licence
Ce projet est distribué sous licence MIT.
Vous êtes libre de l’utiliser, le modifier et le partager.

### 👨‍💻 Auteur
Développé par Manu-dev-00  
Projet académique / personnel pour la gestion des réservations de salles.
