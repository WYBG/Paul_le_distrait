<?php
// Démarre la session
session_start();

require_once 'flight/Flight.php';

// Fonction pour gérer la connexion à la base de données avec Singleton (une seule connexion utilisée)
function getDatabaseConnection() {
    static $db = null; // Connexion statique pour éviter de recréer une connexion à chaque appel
    if ($db === null) {
        try {
            $db = new PDO(
                "pgsql:host=localhost;port=5434;dbname=escape_game", // Connexion à une base PostgreSQL
                "postgres",
                "postgres"
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Lève des exceptions en cas d'erreurs SQL
        } catch (PDOException $e) { 
            // En production, éviter d'afficher le détail des erreurs pour des raisons de sécurité
            echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
        }
    }
    return $db; // Retourne l'objet PDO pour les requêtes
}

// Déclaration d'une route pour la page d'accueil
Flight::route('/', function () {
    // Rend le template "MY_Escape_Game" lorsqu'on accède à la racine du site
    Flight::render('MY_Escape_Game');
});

// Route API pour récupérer tous les objets de la base de données
Flight::route('/api', function () {
    $tableau = []; // Tableau pour stocker les objets
    $dbconnection = getDatabaseConnection(); 
    // Requête SQL pour récupérer les informations des objets
    $query =  'SELECT nom, objet_Type, indice, importance, img, taille, position_ancre, objet_debloque, objet_libere, minzoom, ST_AsGeoJson(geometrie) AS geom FROM objets';
    $objets = $dbconnection->prepare($query);
    $objets->execute(); 
    $objets = $objets->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($objets as $objet) {
        // Traitement des champs spécifiques pour convertir les données en format attendu
        $objet['taille'] = array_map('intval', explode(',', trim($objet['taille'], '{}')));
        $objet['position_ancre'] = array_map('intval', explode(',', trim($objet['position_ancre'], '{}')));
        $objet['geom'] = json_decode($objet['geom']); // Conversion JSON des données géométriques
        $tableau[] = $objet; // Ajout de l'objet au tableau
    }
    // Retourne le tableau d'objets au format JSON
    Flight::json(["tableau" => $tableau]);
});

// Route API pour récupérer uniquement les objets de départs
Flight::route('GET /api/objets', function () {
    $tableau = [];
    $dbconnection = getDatabaseConnection();
    // Requête SQL qui recupère les champs requis et la géométrie en JSON
    $query =  'SELECT nom, objet_Type, indice, importance, img, taille, position_ancre, objet_debloque, objet_libere, minzoom, ST_AsGeoJson(geometrie) AS geom FROM objets WHERE debut = :type';
    $objets = $dbconnection->prepare($query);
    $objets->execute([':type' => 'TRUE']); 
    $objets = $objets->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($objets as $objet) {
        // Traitement identique à celui de la route précédente
        $objet['taille'] = array_map('intval', explode(',', trim($objet['taille'], '{}')));
        $objet['position_ancre'] = array_map('intval', explode(',', trim($objet['position_ancre'], '{}')));
        $objet['geom'] = json_decode($objet['geom']);
        $tableau[] = $objet;
    }
    // Retourne les objets filtrés
    Flight::json(["tableau" => $tableau]);
});

// Route API pour récupérer un objet spécifique par son ID
Flight::route('GET /api/objets(/@id)', function ($id) {
    if ($id) { 
        $dbconnection = getDatabaseConnection();
        $query =  'SELECT nom, objet_Type, indice, importance, img, taille, position_ancre, objet_debloque, objet_libere, minzoom, ST_AsGeoJson(geometrie) AS geom FROM objets WHERE id = :id';
        $objet = $dbconnection->prepare($query);
        $objet->execute([':id' => $id]); 
        $objet = $objet->fetch(PDO::FETCH_ASSOC); 
        if ($objet) {
            // Traitement des données comme dans les autres routes
            $objet['taille'] = array_map('intval', explode(',', trim($objet['taille'], '{}')));
            $objet['position_ancre'] = array_map('intval', explode(',', trim($objet['position_ancre'], '{}')));
            $objet['geom'] = json_decode($objet['geom']);
            Flight::json(["objet" => [$objet]]);
        } else {
            Flight::json(["message" => "Objet non trouvé"], 404);
        }
    } else {
        Flight::json(["message" => "ID manquant"], 400);
    }
});

// Route pour gérer l'ajout d'un utilisateur (via formulaire POST)
Flight::route('POST /MY_Escape_Game', function () {
    // Ajout des infos de la session
    $_SESSION["user"] = $_POST["pseudo"] ?? ''; 
    $_SESSION["genre"] = $_POST["genre"] ?? ''; 
    $score = 0; // Initialise le score à zéro
    Flight::render('MY_Escape_Game', ["pseudo" => $_SESSION["user"], "genre" => $_SESSION["genre"], "score" => $score]);
});

// Route pour déconnecter l'utilisateur et sauvegarder ses données
Flight::route('/logout', function () {
    $dbconnection = getDatabaseConnection();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        // Vérifie la présence du score et des infos du joueurs
        if (isset($data["score"], $_SESSION["user"], $_SESSION["genre"])) {
            $score = intval($data["score"]);
            // Ajout du jour dans la basede données
            $NewJoueur =  'INSERT INTO joueurs (pseudo, genre, score) VALUES (:pseudo,:genre,:score)';
            $NewJoueur = $dbconnection->prepare($NewJoueur);
            $NewJoueur->execute([
                ':pseudo' => $_SESSION["user"],
                ':genre' => $_SESSION["genre"],
                ':score' => $score
            ]);
            // Récupération du top 10 des joueurs
            $top10 = 'SELECT genre, pseudo, score FROM joueurs ORDER BY score DESC LIMIT 10';
            $top10 = $dbconnection->prepare($top10);
            $top10->execute();
            $_SESSION["top10"] = $top10->fetchAll(PDO::FETCH_ASSOC);
        } else {
            Flight::json(["message" => "Score manquant"], 400);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //Déconnexion
        Flight::redirect('/');
    } else {
        Flight::json(["message" => "Méthode non autorisée"], 405);
    }
});

// Route pour réinitialiser la session
// et Retourne à la page d'accueil
Flight::route('/replay', function () {
    $_SESSION = []; // Vide la session
    Flight::redirect('/');
});

// Démarrage de l'application Flight
Flight::start();
?>