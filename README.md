# Hackers Poulette Contact Form

This project is an implementation of a secure contact form for the Hackers Poulette website. The form allows users to submit contact requests by providing their personal information and a message.

## View

![view.png](view.png)

## Features

- Server-side validation of form data.
- Protection against spam using the honeypot technique.
- Saves form data in a MySQL database.
- Sends an e-mail confirming receipt (using SwiftMailer).
- Use of the rakit/validation library for data validation.
- Client-side validation with JavaScript for a better user experience.

## Prerequisites

- PHP 5.6 or higher
- MySQL
- Composer (to install dependencies)

## Installation

1. Clonez ce dépôt dans votre répertoire local :

`git clone https://github.com/votre-utilisateur/hackers-poulette.git`

2. Accédez au répertoire du projet :

`cd hackers-poulette`

3. Installez les dépendances en exécutant la commande suivante :

`composer install`

4. Configurez les informations de connexion à la base de données dans le fichier `functions.php`.

## Utilisation

1. Importez le fichier SQL `database.sql` dans votre base de données MySQL pour créer la table `contact_forms`.

2. Démarrez un serveur web local (par exemple, avec Apache) et assurez-vous que votre site est accessible.

3. Accédez à la page du formulaire dans votre navigateur (par exemple, http://localhost/hackers-poulette/index.php).

4. Remplissez le formulaire avec les informations requises et soumettez-le.

5. Si le formulaire est valide, les données seront enregistrées dans la base de données et vous recevrez un e-mail de confirmation de réception.

## Contribuer

Les contributions sont les bienvenues ! Si vous souhaitez améliorer ce projet, veuillez suivre les étapes suivantes :

1. Fork du dépôt.
2. Créez une branche pour vos modifications.
3. Faites les modifications et commit.
4. Push vos modifications sur votre fork.
5. Ouvrez une pull request vers ce dépôt.
