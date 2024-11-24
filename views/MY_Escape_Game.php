<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Encodage de caractères UTF-8 pour gérer les accents et caractères spéciaux -->
    <meta charset="UTF-8">
    <!-- Favicon du site -->
    <link rel="icon" href="images/icon.png">
    <!-- Titre de la page -->
    <title>Paul l'étourdie</title>
    <!-- Feuille de style CSS spécifique à l'application -->
    <link rel="stylesheet" href="assets/MY_Escape_Game.css">

    <!-- Inclusion des styles de la bibliothèque Leaflet (pour les cartes interactives) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin=""/>

    <!-- Inclusion du script JavaScript de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
    
    <!-- Meta viewport pour une adaptation sur les écrans mobiles-->
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="<?= isset($pseudo) ? 'jeu-active' : '' ?>"> 

<!-- Vérifie si la variable PHP $pseudo est définie ou non -->
<?php if (!isset($pseudo)) { ?>
<!-- Si $pseudo n'est pas défini, afficher l'écran d'accueil -->

<?php if (!isset($_SESSION["top10"]) ) { ?>
<div id="accueil">
    <div id="intro">
            <p class="titre">Bienvenue dans la journée d'aventure de Paul le distrait !!!</p>
            <img src="images/Accueil.png" alt="accueil">
            <p class="titre2">Votre Objectif : Aidez Paul le Distrait à Sauver sa Journée </p>
            <p class="presentation">
                Paul est un homme très occupé, mais aussi extrêmement tête en l'air. Aujourd’hui, il doit réaliser 
                une présentation importante lors d’une grande conférence dans le hall de l'Ecole Nationale des Sciences Géographiques (ENSG). Cependant, il est pris au dépourvu : 
                il a complètement oublié le mot de passe de son ordinateur, où se trouve sa présentation ! Pour 
                récupérer ce mot de passe, Paul doit retourner chez lui, mais rien ne se passe comme prévu…
            </p>
            
    </div>
        <!-- règles du jeu -->
    <div id="regles" class="regle">
        <p class="titre2 regles-titre">Règles du Jeu</p>
            <ul class="regles-list">
                <li>
                <p class="presentation">Pour terminer le jeu, vous devez récupérer la présentation dans l'ordinateur de Paul.</p>
                </li>
                <li>
                <p class="presentation">Si vous vous trompez de mot de passe, vous subirez des points de malus.</p>
                </li>
                <li>
                <p class="presentation">Vous remportez des points en fonction de l'importance des objets que vous récupérez.</p>
                </li>
                <li>
                <p class="presentation">Pour récupérer le contenu d'un objet bloqué, vous devez sélectionner l'objet qui le débloque dans votre inventaire puis cliquer sur lui. L'objet apparaitra quand vous fermerez la popup.</p>
                </li>
                <li>
                <p class="presentation">Vous obtenez des points bonus si vous terminez le jeu en moins de 15 minutes, mais des malus si vous dépassez ce délai.</p>
                </li>
                <li>
                <p class="presentation">Un gros malus sera appliqué à chaque utilisation du mode triche, mais la durée de son activation dépend de vous.</p>
                </li>
            </ul>
    </div>

    <p class="titre2">Tâche 1 : Retrouvez Paul dans le hall de l'ENSG.</p>
        
    <div id="profil">
        <!-- Formulaire pour enregistrer le pseudo et le genre -->
        <form action="/MY_Escape_Game" method="post">
            <p>Renseignez vos informations pour commencer l'aventure !</p>

            <p>Nom :</p>
            <input type="text" name="pseudo" required>
            
            <p>Genre :</p>
            <select name="genre">
                <option value="none_genre"></option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>

            <!-- Bouton pour soumettre les informations -->
            <input type="submit" name="envoi" value="Play">
        </form>
    </div>

</div>
    
<?php } else { ?>

    <!-- Section Hall of Fame si la session top10 existe -->
<div id ="HallOfFame" >

    <h2>Bienvenue dans le Hall of Fame !!!</h2>
    <p >J'espère que vous passez un bon moment</p>
    <p >Ici a été gravé les noms du TOP 10</p>
    <p >Ton nom a t-il été inscrit ? 👀</p>
    <table>
        <thead>
            <tr>
                <th>N</th>
                <th></th>
                <th>Nom</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $rank = 1;
        foreach ($_SESSION["top10"] as $joueur ){?>
            <tr><td><?= $rank?></td><td> <img src= "<?php echo "images/" .$joueur["genre"]  . ".png";?>" alt="genre"></td>
            <td> <?= $joueur["pseudo"]?></td>
            <td> <?= $joueur["score"]?> </td></tr><?php ;
        $rank++;}?>
        </tbody>
    </table>

        <!-- Bouton pour rejouer -->
    <form action="/replay" method="get">
    <button  class="rejouer">Rejouer</button>
    </form>
</div>

<?php }?>

<?php } else { ?>
<!-- Si $pseudo est défini, afficher l'interface du jeu -->
<div id="jeu">
    <div id="profil">
        <!-- Affichage de l'image de profil selon le genre sélectionné -->
        <?php if (isset($genre)) { ?>
        <img src="<?php echo "../images/" . $genre . ".png"; ?>" alt="" >
        <?php } ?>
        <!-- Affichage des informations de l'utilisateur -->
        <p>Nom : <?= isset($pseudo) ? $pseudo : 'Anonyme' ?></p>
        <p id="score">Score : {{this.score}}</p>
        <p>Temps  {{this.temps[0]}} : {{this.temps[1]}}</p>
        
        <!-- Mode triche activable via une case à cocher -->
        <p class="triche">Mode triche   
        <label class="switch">
            <input @change="mode_triche" type="checkbox" v-model="triche">
            <span class="slider round"></span>
        </label>
        </p>

        <!-- Bouton pour Abandonner -->
        <form action="/logout" method="get">
         <button @click="recuperation_score" class="button">Abandonner</button>
        </form>
    </div>
    
    <!-- Div pour afficher une carte interactive -->
    <div id="carte"></div>
    
    <!-- Div pour gérer l'inventaire -->
    <div id="inventaire">
        <ul>
            <!-- Utilisation de Vue.js pour lier dynamiquement les objets de l'inventaire -->
            <li v-for="objet in Inventaire" :key="objet.nom" @click="selection_objet(objet)" 
                class="objets_inventaitre" 
                :class="{ 'selected': selectedObjet && selectedObjet.nom === objet.nom }">
                <img :src="'../images/' + objet.img + '.png'" :alt="objet.nom"  />
            </li>
        </ul>
    </div>
</div>
<?php } ?>

<!-- Inclusion de Vue.js et du script principal de l'application -->
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="assets/MY_Escape_Game.js"></script>
</body>
</html>