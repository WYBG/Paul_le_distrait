# Paul l'étourdie

## 📖 Présentation

**Paul l'étourdie** est un jeu cartographique interactif où les joueurs aident **Paul**, un personnage distrait, à récupérer le mot de passe de son ordinateur. Cela lui permettra de présenter son projet lors d'un forum Géo organisé par l'ENSG. À travers une série d'énigmes, le joueur explore différents lieux, récupère des objets, et débloque des indices pour avancer dans l'histoire.

---

## 🛠️ Consignes d'installation

Voici les composants nécessaires à l'installation et leur version respective :

| Composant        | Version      |
|-------------------|--------------|
| OpenJDK11U-jre   | 11.0.25_8    |
| Geoserver        | 2.26.0       |
| XAMPP            | 3.3.0        |
| PostgreSQL       | 16           |


Après avoir installer OpenJDK11U-jre comme environnement JAVA, vous pourrez installer Geoserver. Il faudra fournir le chemin du JRE installé :

![Texte alternatif](Captures/JRE_geoserver.PNG "Texte au survol")

Nous avons choisi de l'installer en lancement manuel, il faudra donc lancer geoserver et s'authentifier pour visualiser la carte de chaleur :

![Texte alternatif](Captures/Geoserver_manuel.PNG "Texte au survol")

Après son installation, récupérez le **dossier MY_escape_game**  à la racine du git qui est notre espace de travail et mettez le dans le dossier workspaces de votre geoserver :

![Texte alternatif](Captures/Ajout_workspace.PNG "Texte au survol")

Vous aurez aussi besoin d'un Système de gestion de base de données ici pgAdmin. Vous pourrez le télécharger sur le site d'EDB avec PostgreSQL :

![Texte alternatif](Captures/PGSQL1.PNG "Texte au survol")

Nous avons intaller notre PostgreSQL sur le port **5434** et télécharger pgAdmin comme une de ses composantes :

![Texte alternatif](Captures/PGSQL2.PNG "Texte au survol")

Nos données étant des données géoréférecées, il sera également important de télécharger PostGIS grâce à Stack builder qui se lancera après son installation parmi les composants :

<p align="center">
  <img src="Captures/Complement_pgsql.png" alt="Image 1" width="45%" style="margin-right:20px;">
  <img src="Captures/Postgis.PNG" alt="Image 2" width="45%"style="margin-right:20px;">
</p>

Après l'installation de pgAdmin, PostgreSQL et PostGIS, vous pourrez créer la base de données nécessaire au jeu :
- Créer une base de données que vous appelerez escape_game.
- Ouvrez ensuite une query tool 
- Il faudra créer une extention PostGIS à votre votre base de données :

![Texte alternatif](Captures/BDD1.PNG "Texte au survol")

- Vous pourrez ensuite récupérer le fichier **BDD_Diallo_Gagre.sql** à la racine du git et l'exécuter dans la query tool pour créer les tables de la base de donnée :
  
![Texte alternatif](Captures/BDD2.PNG "Texte au survol")

 En cas d'utilisation de XAMPP pour le lancement de serveur Apache, il faudra décommenter les extensions pdo_pgsql et pgsql du fichier php.ini en elevant les ";" :
 
![Texte alternatif](Captures/Xampp_ini.PNG "Texte au survol")

Ainsi que changer le chemin du fichier htdocs pour celui de votre **dossier de travail** où votre fichier index.php se trouve dans le fichier httpd.conf :

<p align="center">
  <img src="Captures/Xampp1.PNG" alt="Image 1" width="45%" style="margin-right:20px;">
  <img src="Captures/Xampp2.PNG" alt="Image 2" width="45%"style="margin-right:20px;">
</p>

## 🧩 Solutions des énigmes

Le tableau ci-dessous récapitule les objets à trouver et/ou à récupérer, leur ordre, et leur localisation géographique.

| Objet          | Ordre | Lieu                     | Position (Long, Lat)  |
|----------------|-------|--------------------------|------------------------|
| Clé de voiture | 1     | Aéroport d'Orly, proche du logo avion | 2.3670, 48.7278 |
| Badge Maison   | 2     | Intermarché près de la forêt régionale des Vallières, Thorigny-sur-Marne | 2.7168, 48.8960 |
| Carnet         | 3     | Hôtel Disney, Chessy     | 2.7883, 48.8704        |
| Présentation   | 4     | Hall ENSG                | 2.5878, 48.8410        |

Ces objets sont majoritairement bloqués par d'autres objets.

### Objets bloqués et conditions de déblocage

| Objet           | Bloqué ? | Objet bloquant       |
|------------------|----------|----------------------|
| Clé de voiture  | Non      | -                    |
| Badge Maison    | Oui      | Voiture              |
| Carnet          | Oui      | Maison               |
| Présentation    | Oui      | Ordinateur           |

---

## 📜 Étapes chronologiques

Le tableau suivant décrit les actions nécessaires pour terminer le jeu.

| Étape | Lieu        | Action                         | Résultat                |
|-------|-------------|--------------------------------|-------------------------|
| 1     | ENSG        | Clic sur ordinateur           | Indice mot de passe     |
| 2     | Maison      | Clic sur Maison               | Indice badge            |
| 3     | Garage      | Clic sur Voiture              | Indice clé de voiture   |
| 4     | Aéroport    | Clic sur clé                  | Clé dans inventaire     |
| 5     | Garage      | Clic sur clé puis sur voiture | Apparition badge        |
| 6     | Garage      | Clic sur badge                | Badge dans inventaire   |
| 7     | Maison      | Clic sur Badge puis sur Maison| Apparition carnet       |
| 8     | ENSG        | Clic sur ordinateur et saisie mot de passe | Apparition Présentation |
| 9     | ENSG        | Clic sur Présentation         | Tableau des 10 meilleurs joueurs |

⚠️ L’utilisation du bouton **triche** entraîne une pénalité sur le score.

---

## 🚀 Lancement

1. Assurez-vous que tous les composants nécessaires sont installés et configurés correctement.
2. Lancez geoserver et authentifiez vous.
3. Lancez XAMPP et démarrez le serveur Apache.
4. Ouvrez votre navigateur et tapez **localhost** dans la barre URL.
5. Suivez les étapes du jeu pour résoudre les énigmes.
6. Profitez de l’expérience et aidez Paul à réussir sa présentation !

---

## 🎯 Objectif

Terminez le jeu en un minimum de temps et de clics pour figurer dans le tableau des **10 meilleurs joueurs** !

Bon jeu ! 🎮
