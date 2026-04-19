# Travail pratique : Créer une application de sondage (Laravel + Vue.js)

## Introduction

Ce projet vous invite à créer une application complète mêlant backend Laravel et frontend Vue.js,
sous la forme d'un système de sondage multi-plateforme.

L'objectif est de concevoir une interface permettant de créer, configurer, consulter et utiliser des
sondages à travers une API JSON consommée par le frontend.

Les modèles Eloquent de base sont fournis. Le travail portera principalement sur :

- le frontend Vue.js
- les endpoints backend JSON nécessaires au fonctionnement du frontend

Le système de sondage attendu repose notamment sur les fonctionnalités suivantes :

- un sondage appartient à une personne authentifiée
- un sondage contient une question et plusieurs options
- un sondage est par défaut en mode brouillon ; l'utilisateur pourra lancer le sondage directement à
  sa création, ou a posteriori
- un sondage peut autoriser un ou plusieurs choix
- un sondage peut autoriser ou non le changement de vote
- les résultats peuvent être publics ou privés
- un sondage peut avoir une durée ; la date de début et la date de fin seront calculées au moment où
  le mode brouillon est désactivé, à partir du temps actuel et de cette durée
- les votes sont associés à une personne authentifiée et à une option

## Objectifs pédagogiques

À l'issue de ce travail pratique, les étudiants devraient être capables de :

- concevoir et développer une application web complète de type SPA
- implémenter et consommer une API JSON versionnée
- exploiter des modèles relationnels déjà fournis pour construire des fonctionnalités cohérentes
- créer un frontend réactif pour gérer un tableau de bord, un éditeur et des vues de
  consultation
- interagir avec une API (`GET`, `POST`, `PUT`/`PATCH`, `DELETE`) et afficher
  dynamiquement les contenus

## Consignes générales

Vous développerez une application web en deux parties :

- Backend Laravel : responsable de l'exposition des endpoints JSON utilisés par le frontend
- Frontend Vue.js : responsable de l'affichage et des interactions autour des sondages, utilisable
  sur navigateur et mobile, avec une approche mobile first

Les modèles sont déjà fournis. Vous devez construire autour de ceux-ci les fonctionnalités utiles au
frontend.

Fonctionnalités attendues :

- afficher la liste des sondages de la personne connectée
- permettre la création et l'édition d'un sondage, avec modification limitée au titre une fois le
  sondage lancé
- gérer les options d'un sondage
- gérer le mode brouillon
- gérer le choix simple ou multiple
- gérer l'autorisation ou non du changement de vote
- gérer la visibilité publique ou non des résultats
- gérer les dates ou la durée de disponibilité
- afficher un sondage accessible via un token
- permettre de voter
- afficher les résultats via polling avec un graphique visualisant en direct leur évolution

La structure exacte de l'interface est libre, à condition que l'application reste claire,
fonctionnelle et cohérente.

## Évaluation

Chaque partie du projet sera évaluée selon plusieurs catégories. L'évaluation portera sur :

- la qualité du frontend
- le bon fonctionnement des endpoints JSON nécessaires à ce frontend
- la capacité à expliquer, défendre et adapter son code à l'oral

Conditions particulières :

- toute triche avérée entraîne la note de `1` et aucune possibilité de remédiation ne sera proposée
- l'oral a un poids important dans l'évaluation afin de contrebalancer l'usage des IA et de vérifier
  la maîtrise réelle du travail rendu

Note maximale : `(nombre de points obtenus / nombre de points maximum) x 5 + 1`

## Critères frontend et endpoints JSON

### Critères rendu

| # | Critère |
| --- | --- |
| 1 | Affichage d'un dashboard des sondages de la personne connectée
| 2 | Création ou édition d'un sondage depuis le frontend
| 3 | Gestion des options du sondage (ajout, modification, suppression)
| 4 | Gestion des paramètres du sondage (brouillon, choix multiples, changement de vote, résultats publics, dates)
| 5 | Affichage d'un sondage accessible via un token
| 6 | Soumission d'un vote valide depuis le frontend
| 7 | Affichage conditionnel correct selon l'état du sondage et les droits d'accès
| 8 | Consommation correcte des endpoints JSON par le frontend
| 9 | Gestion correcte des erreurs utilisateur côté frontend
| 10 | Interface lisible, claire, responsive et agréable à utiliser
| 11 | Affichage en temps réel, via polling, des résultats
| 12 | Le projet est fonctionnel de bout en bout
| 13 | Code lisible, structuré, `README` clair et utilisation correcte du contrôle de version
| 14 | Bon usage des composants Vue, des composables et d'une architecture cohérente du code
| 15 | Nommage, lisibilité et organisation générale du frontend (et routes API backend) soignés


## Critères présentation

Les informations ci-dessous sont à titre indicatif et peuvent être adaptées.

| # | Critère |
| --- | --- |
| 1 | Les informations sont claires et bien présentées
| 2 | Les réponses aux questions sont pertinentes
| 3 | La capacité à modifier le code en direct selon une demande est satisfaisante
| 4 | La compréhension théorique de Vue.js, des échanges frontend/backend et de l'architecture fullstack est bonne
| 5 | La personne démontre qu'elle maîtrise réellement le code présenté, y compris si des outils d'IA ont été utilisés

## Contraintes techniques

- Backend Laravel >= 12.x
- Frontend Vue.js >= 3.4
- Base de données relationnelle (`SQLite`, `MySQL` ou `PostgreSQL`)
- Projet disponible sur GitHub
- Une documentation minimale (`README.md`) doit permettre de tester facilement l'application
- Les modèles et migrations sont fournis, mais les endpoints JSON nécessaires au frontend doivent
  être implémentés
- L'usage de l'IA est autorisé, mais le code rendu doit être compris, maîtrisé et défendable à l'oral
- Les critères liés à l'architecture, au découpage du code, au nommage et à la lisibilité auront une
  importance particulière
- L'usage d'outils d'IA ne dispense pas d'un regard critique : un code trop verbeux, mal structuré ou
  peu cohérent sera pénalisé

## Conseils

- Ne cherchez pas à faire complexe : commencez simple, itérez ensuite.
- Travaillez de manière incrémentale et validez chaque étape.
- Testez tôt et souvent.
- Une fonctionnalité simple mais fiable vaut mieux qu'une fonctionnalité ambitieuse inachevée.
- Structurez clairement les données échangées entre votre frontend et votre API JSON.

## Livrables et rendu

Vous devez fournir :

- l'URL du dépôt GitHub
- un fichier `README.md` clair pour expliquer l'installation et les choix techniques
- Il est possible de mettre à jour le dépôt entre le jour du rendu et l'examen
- Seul le code présent avant l'échéance sera évalué pour le rendu
- Le code ajouté ou modifié après l'échéance ne sera pas évalué pour la note de rendu, mais pourra
  éventuellement aider lors de la présentation orale

Rendu final : au plus tard le dimanche 17 mai 2026 à 23:59:59 UTC (date du commit).

La présentation orale aura lieu lors de la période des examens et sera probablement d'une durée de 20 minutes par étudiant.