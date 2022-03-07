# Retrait Selon Agenda

Ce module permet de proposer à vos clients un retrait sur place à une date et un lieu qu'ils peuvent choisir lors de la
finalisation de leur commande.

Les dates et lieux proposés aux clients sont des événements qui sont extraits d'un calendrier Google. Il vous suffit donc
de créer un événement dans ce calendrier pour qu'il devienne un lieu de livraison dans votre boutique.

Le module utilise les informations suivantes des événements du calendrier :
- La date de l’événement
- La localisation (optionnelle)
- Le titre de l’événement
- La description (optionnelle)

# Installation et Configuration

Installez le module avec composer pour installer l'ensemble des dépendances :

```
composer require thelia/retrait-selon-agenda-module
```

Installez et activez le module sur votre boutique, puis allez à la page de configuration pour indiquer les informations
nécessaires au fonctionnement du module :

- **Adresse URL privée de l'agenda**: il s'agit d'une URL qui permet d'accéder au calendrier Google où vous allez placer
les dates et lieux de livraison. Voici la marche à suivre pour obtenir cette URL :
    1. Sur un ordinateur, ouvrez Google Agenda.
    2. En haut à droite, cliquez sur Paramètres Paramètres > Paramètres.
    3. Ouvrez l'onglet Agendas.
    4. Cliquez sur le nom de l'agenda que vous souhaitez utiliser.
    5. Dans la section Adresse URL privée, cliquez sur ICAL.
    6. Copiez le lien ICAL qui s'affiche dans la fenêtre. 
    7. Collez le lien dans la configuration du module.

- **Durée de vie du cache des données de l'agenda, en minutes**: Pour éviter de trop fréquentes requêtes à Google, les
 informations extraites du calendrier sont mises en cache pour une durée configurable. Indiquez ici cette durée en minutes.
  30 minutes semble une durée raisonnable.
  
- **Nombre maximum d’événements à présenter**: Pour limiter le nombre de dates de retrait possibles proposées à vos
clients.

Une fois la configuration terminée, n'oubliez pas d'affecter une zone de livraison au module (la zone France, sans doute)

# Intégration en front-office

Aucune intégration n'est nécessaire, le module utilise les hooks pour insérer les informations de livraison aux endroits 
nécessaires :

- Sur la page de récapitulation de commande
- Dans l'onglet "Livraison et Facturation" des commandes dans le back-office
- Dans le mail de confirmation de commande
- Dans le mail de notification que vous recevez lorsque la commande est passée.
- Dans les factures et bons de livraison PDF
