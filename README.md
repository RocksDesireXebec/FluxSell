# FluxSell
Un BackEnd API REST fonctionnel d'achat de produits et services en ligne conçu grâce au Framework Symfony et l'Api platform 

**Table de contenu :**
1. [Fonctionnalités du système](#fonctionnalités-du-système)
    1. Gestion des utlisateurs et sécurité
        1. Création de comptes utilisateur
        1. Activation de compte via vérification d'adresse e-mail
        1. Authentification via le système JWT (Json Web Token)
        1. Protection contre la vulnérabilité CSRF (Cross-Site Request Forgery)
        1. Cryptage des mots de passes
        1. Sécurité des accès aux ressources via parefeu, attribution des droits et vérification de compte
        1. Gestion d'envoie d'e-mails
        1. Réinitialisation de mot de passe oublié
    1. Gestion des achats client
        1. Achats de produits et services
        1. Paiements par carte bancaire via la plateforme STRIPE
        1. Stockages des commandes 
        1. Gestion du panier utilisateur
        1. Sauvegarde automatique du panier en base de données après fermeture normale ou accidentelle de session  

1. Installation
    1. Installation via Docker
    1. Installation via Wampserver

1. Manuel d'utilisation
    1. Création et activation d'un compte utlisateur
    1. Connexion au système
    1. Consulter les différentes catégories de produits
    1. Consulter un produit
    1. Commander un produit
    1. Regler une commande
    1. Gerer son panier

1. Tests unitaires et d'intégrations

1. Fonctionnalités en cours de developpement
    1. Inscription via les services tiers ( Gmail, Facebook, etc)
    1. Gestion de la livraison de produits via la géo-localisation
    1. Sécurité des inscriptions via le système de CAPTCHA
    1. Système d'envoie de notifications SMS
    1. Intégration du moyen de paiement en ligne PayPal
    1. Service client, blog, Service après vente
    1. Espace publicitaire

## Fonctionnalités du système ##

