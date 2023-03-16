<?php
use App\Models\Db;
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require_once __DIR__ . '/../vendor/autoload.php';
$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
   $response->getBody()->write('Hello World!');
   return $response;
});


/***************************************************************************************************************
 * GESTION DES CLIENTS
 * 
 ***************************************************************************************************************/

// LISTE DES CLIENTS
$app->get('/client/list', function (Request $request, Response $response) {
    $sql = "SELECT * FROM client";
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($customers));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });
   
// LA LISTE DES CLIENTS PAR TRAJETS ORDONNE PAR TRAJET.DATE


// $app->get('/client/listpartrajet/{trajet}', function (Request $request, Response $response) {
   
//    // Ensuite il faut recuperer le parametre
//    $nomtrajet = $request->getAttribute('trajet');
   
//    // ensuite tu passes le nom du trajet a la requête 
//     $sql = "SELECT client.id_client, client.nom, client.prenom, client.datnais, client.adresse, client.telephone, client.email, client.adresse,
//     trajet.id_trajet,trajet.date,trajet.typ_voyage
//     FROM client
//     JOIN ticket ON client.id_client=ticket.id_client
//     JOIN trajet ON ticket.id_trajet=trajet.id_trajet
//     WHERE trajet.typ_voyage=$nomtrajet
//     ORDER BY trajet.date;";

//   try {
//     $db = new Db();
//     $conn = $db->connect();
//     $stmt = $conn->query($sql);
//     $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
//     $db = null;
   
//     $response->getBody()->write(json_encode($customers));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(200);
//   } catch (PDOException $e) {
//     $error = array(
//       "message" => $e->getMessage()
//     );
 
//     $response->getBody()->write(json_encode($error));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(500);
//   }
//  });
 


// AJOUTER UN NOUVEAU CLIENT
$app->post('/client/add', function (Request $request, Response $response, array $args) {
   
    //$data = $request->getParsedBody(); 
    $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le client
    
    $nom = $data["nom"]; // permet de récuperer le nom
    $prenom = $data["prenom"];  // permet de récuperer le prenom
    $datnais = $data["datnais"];  // permet de récuperer la date de naissance
    $adresse = $data["adresse"];   // permet de récuperer l'adresse
    $email = $data["email"];  // permet de récuperer l'email
    $telephone = $data["telephone"];  // permet de récuperer le téléphone

    // requete pour inserer les éléments du formulaire dans la base de donnée 
   
    $sql = "INSERT INTO client(nom, prenom, datnais, email, telephone, adresse)  VALUES (:nom, :prenom, :datnais, :email, :telephone, :adresse)";
   
    try {
      $db = new Db();
      $conn = $db->connect();
     
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':nom', $nom);
      $stmt->bindParam(':prenom', $prenom);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':telephone', $telephone);
      $stmt->bindParam(':datnais', $datnais);
      $stmt->bindParam(':adresse', $adresse);
      
      $result = $stmt->execute();
   
      $msg = [
        "message" => "Enregistrement reussi",
        "status" => 200 
      ];

      $db = null;
      $response->getBody()->write(json_encode($msg));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });


// MODIFIER UN CLIENT
$app->put('/client/update/{id_client}',
function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_client');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];
$email = $data["email"];
$telephone = $data["telephone"];
$adresse = $data["adresse"];
$prenom = $data["prenom"];
$datnais = $data["datnais"];


$sql = "UPDATE client SET
         nom = :nom,
         email = :email,
         telephone = :telephone,
         adresse = :adresse,
         prenom = :prenom,
         datnais = :datnais
WHERE id_client = $id";

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $stmt->bindParam(':email', $email);
 $stmt->bindParam(':telephone', $telephone);
 $stmt->bindParam(':adresse', $adresse);
 $stmt->bindParam(':prenom', $prenom);
 $stmt->bindParam(':datnais', $datnais);

 $result = $stmt->execute();

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UN CLIENT 
$app->delete('/client/delete/{id_client}', function (Request $request, Response $response, array $args) {
  $id = $args["id_client"];
  $sql = "DELETE FROM client WHERE id_client = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API CLIENT ******************************************* */


/**************************************************************************************************************
 * GESTION DES GESTIONNAIRES
 * 
 **************************************************************************************************************/

 // LISTES DES GESTIONNAIRES 
 $app->get('/gestionnaire/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM gestionnaire";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

 // AJOUTER UN GESTIONNAIRE
 $app->post('/gestionnaire/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le gestionnaire
  
  $nom = $data["nom"]; // permet de récuperer le nom
  $prenom = $data["prenom"];  // permet de récuperer le prenom
  $datnais = $data["datnais"];  // permet de récuperer la date de naissance
  $adresse = $data["adresse"];   // permet de récuperer l'adresse
  $email = $data["email"];  // permet de récuperer l'email
  $telephone = $data["telephone"];  // permet de récuperer le téléphone

  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO gestionnaire(nom, prenom, datnais, email, adresse, telephone)  VALUES (:nom, :prenom, :datnais, :email, :telephone, :adresse)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':datnais', $datnais);
    $stmt->bindParam(':adresse', $adresse);
    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // MODIFIER UN GESTIONNAIRE
 $app->put('/gestionnaire/update/{id_gest}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_gest');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];
$email = $data["email"];
$telephone = $data["telephone"];
$adresse = $data["adresse"];
$prenom = $data["prenom"];
$datnais = $data["datnais"];


$sql = "UPDATE gestionnaire SET
         nom = :nom,
         email = :email,
         telephone = :telephone,
         adresse = :adresse,
         prenom = :prenom,
         datnais = :datnais
WHERE id_gest = $id";

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $stmt->bindParam(':email', $email);
 $stmt->bindParam(':telephone', $telephone);
 $stmt->bindParam(':adresse', $adresse);
 $stmt->bindParam(':prenom', $prenom);
 $stmt->bindParam(':datnais', $datnais);

 $result = $stmt->execute();

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UN GESTIONNAIRE 
$app->delete('/gestionnaire/delete/{id_gest}', function (Request $request, Response $response, array $args) {
  $id = $args["id_gest"];
  $sql = "DELETE FROM gestionnaire WHERE id_gest = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      
    ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API GESTIONNAIRE ******************************************* */



/*************************************************************************************************************
 * GESTION DES USERS
 * 
 **************************************************************************************************************/

 // LISTES DES USERS
 $app->get('/users/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM users";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

 // AJOUTER UN USERS
 $app->post('/users/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le users
  
  $nom = $data["nom"]; // permet de récuperer le nom
  $prenom = $data["prenom"];  // permet de récuperer le prenom
  $datnais = $data["datnais"];  // permet de récuperer la date de naissance
  $adresse = $data["adresse"];   // permet de récuperer l'adresse
  $email = $data["email"];  // permet de récuperer l'email
  $telephone = $data["telephone"];  // permet de récuperer le téléphone

  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO users(nom, prenom, datnais, email, adresse, telephone)  VALUES (:nom, :prenom, :datnais, :email, :telephone, :adresse)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':datnais', $datnais);
    $stmt->bindParam(':adresse', $adresse);
    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // MODIFIER UN USERS
 $app->put('/users/update/{id_user}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_user');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];
$prenom = $data["prenom"];
$email = $data["email"];
$telephone = $data["telephone"];
$adresse = $data["adresse"];
$datnais = $data["datnais"];


$sql = "UPDATE users SET
         nom = :nom,
         email = :email,
         telephone = :telephone,
         adresse = :adresse,
         prenom = :prenom,
         datnais = :datnais
WHERE id_user = $id";

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $stmt->bindParam(':email', $email);
 $stmt->bindParam(':telephone', $telephone);
 $stmt->bindParam(':adresse', $adresse);
 $stmt->bindParam(':prenom', $prenom);
 $stmt->bindParam(':datnais', $datnais);

 $result = $stmt->execute();

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UN USERS 
$app->delete('/users/delete/{id_user}', function (Request $request, Response $response, array $args) {
  $id = $args["id_user"];
  $sql = "DELETE FROM users WHERE id_user = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API USERS ******************************************* */


/***************************************************************************************************************
* GESTION DES VILLES
* 
***************************************************************************************************************/

 // LISTES DES VILLES
 $app->get('/ville/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM ville";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

 // AJOUTER UNE VILLE
 $app->post('/ville/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la ville
  
  $nom = $data["nom"]; // permet de récuperer le nom
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO ville(nom)  VALUES (:nom)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // MODIFIER UNE VILLE
 $app->put('/ville/update/{id_ville}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_ville');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];

$sql = "UPDATE ville SET
         nom = :nom
WHERE id_ville = $id";

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $result = $stmt->execute();

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UNE VILLE 
$app->delete('/ville/delete/{id_ville}', function (Request $request, Response $response, array $args) {
  $id = $args["id_ville"];
  $sql = "DELETE FROM ville WHERE id_ville = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API VILLE ******************************************* */



/***************************************************************************************
*GESTION DES COMPAGNIES
***************************************************************************************/

 // LISTES DES COMPAGNIES
 $app->get('/compagnie/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM compagnie";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 



 //LA LISTE DES COMPAGNIES PAR VILLES
 $app->get('/compagnie/listparville/{ville}', function (Request $request, Response $response) {
    
    $nomville = $request->getAttribute('ville');
    
    // il faut passer le nom de ville dans la clause where
  $sql = "SELECT compagnie.id_compagny,compagnie.nom,compagnie.adresse,compagnie.telephone,compagnie.email,ville.id_ville,ville.nom
          FROM compagnie
          JOIN gare ON compagnie.id_compagny = gare.id_compagny
          JOIN ville ON gare.id_ville=ville.id_ville
          WHERE ville.nom=$nomville";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 


 // AJOUTER UNE COMPAGNIE
 $app->post('/compagnie/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la compagnie
  
  $nom = $data["nom"]; // permet de récuperer le nom
  $email = $data["email"];
  $adresse = $data["adresse"];
  $telephone = $data["telephone"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO compagnie(nom,email, adresse, telephone)  VALUES (:nom,:email,:adresse,:telephone)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':telephone', $telephone);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // MODIFIER UNE COMPAGNIE
 $app->put('/compagnie/update/{id_compagny}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_compagny');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];
$email = $data["email"];
$adresse = $data["adresse"];
$telephone = $data["telephone"];

$sql = "UPDATE compagnie SET
         nom = :nom,
         email = :email,
         adresse = :adresse,
         telephone = :telephone
WHERE id_compagny = $id";
 
try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $stmt->bindParam(':email', $email);
 $stmt->bindParam(':telephone', $telephone);
 $stmt->bindParam(':adresse', $adresse);
 $result = $stmt->execute();

 

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UNE COMPAGNIE 
$app->delete('/compagnie/delete/{id_compagny}', function (Request $request, Response $response, array $args) {
  $id = $args["id_compagny"];
  $sql = "DELETE FROM compagnie WHERE id_compagny = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API COMPAGNIE ******************************************* */




/***************************************************************************************
*GESTION DES GARES
*
***************************************************************************************/
// LISTES DES GARES
$app->get('/gare/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM gare";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

// LA LISTE DES GARES PAR VILLE
 $app->get('/gare/listparville/{ville}', function (Request $request, Response $response) {
  
    $nomville = $request->getAttribute('ville');
    
  $sql = "SELECT gare.id_gare,gare.nom,ville.id_ville,ville.nom
          FROM gare,ville
          WHERE ville.id_ville=$nomville";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


 // AJOUTER UNE GARE
 $app->post('/gare/add', function (Request $request, Response $response, array $args) {
   
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la ville
  
  $nom = $data["nom"]; // permet de récuperer le nom
  $id_compagny = $data["id_compagny"];
  $id_ville = $data["id_ville"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO gare(nom,id_compagny,id_ville)  VALUES(:nom,:id_compagny,:id_ville)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':id_compagny', $id_compagny);
    $stmt->bindParam(':id_ville', $id_ville);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


// MODIFIER UNE GARE
$app->put('/gare/update/{id_gare}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_gare');
$data = json_decode($request->getBody()->getContents(), true);
$nom = $data["nom"];
$id_compagny = $data["id_compagny"];
$nom = $data["nom"];
$id_ville = $data["id_ville"];

$sql = "UPDATE gare SET
         nom=:nom,
         id_compagny=:id_compagny,
         id_ville=:id_ville
WHERE id_gare=$id";
 

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':nom', $nom);
 $stmt->bindParam(':id_compagny', $id_compagny);
 $stmt->bindParam(':id_ville', $id_ville);
//  var_dump($email); die;
 $result = $stmt->execute();

 
 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});



// SUPPRIMER UNE GARE
$app->delete('/gare/delete/{id_gare}', function (Request $request, Response $response, array $args) {
  $id = $args["id_gare"];
  $sql = "DELETE FROM gare WHERE id_gare = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
//**************************** FIN API GARES ******************************************* */




/*************************************************************************************************************
*  GESTION DES CARS
************************************************************************************************************** */

// LISTES DES CARS 
$app->get('/car/list', function (Request $request, Response $response) {
   $sql = "SELECT * FROM car ";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 


//  LISTE DES CARS QUI ONT EFFECTUE AU MOINS UN TRAJET POUR CHAQUE COMPAGNIE
 $app->get('/car/listcarparcompagnie/{idcompagnie}', function (Request $request, Response $response) {
    
       $idcompagnie = $request->getAttribute('idcompagnie');
    
  $sql = "SELECT car.num_car,car.typ_car,compagnie.nom
          FROM car 
          JOIN place ON car.num_car = place.num_car
          JOIN trajet ON place.id_trajet = trajet.id_trajet
          JOIN compagnie ON compagnie.id_compagny = car.id_compagny
          WHERE trajet.id_trajet > 1 AND compagnie.id_compagny = $idcompagnie";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // AJOUTER UNE CAR
 $app->post('/car/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la car
  
  $id_compagny = $data["id_compagny"];
  $typ_car = $data["typ_car"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO car(id_compagny, typ_car)  VALUES (:id_compagny,:typ_car)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_compagny', $id_compagny);
    $stmt->bindParam(':typ_car', $typ_car);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


// MODIFIER UN CAR
$app->put('/car/update/{num_car}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('num_car');
$data = json_decode($request->getBody()->getContents(), true);
$typ_car = $data["typ_car"];
$id_compagny = $data["id_compagny"];

$sql = "UPDATE car SET
         typ_car=:typ_car,
         id_compagny=:id_compagny
WHERE num_car = $id";
 
try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':typ_car', $typ_car);
 $stmt->bindParam(':id_compagny', $id_compagny);

 $result = $stmt->execute();


 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;

 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});


// SUPPRIMER UN CAR
$app->delete('/car/delete/{num_car}', function (Request $request, Response $response, array $args) {
  $id = $args["num_car"];
  $sql = "DELETE FROM car WHERE num_car = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

/******************************************** FIN API CAR ******************************************/ 


/****************************************************************************************************************
 * GESTION DES PLACES
 * 
 ***************************************************************************************************************/ 
 

// LISTES DES PLACES
$app->get('/place/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM place";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 


//  LA LISTE DES PLACES DISPONIBLES POUR UN TRAJET PAR COMPAGNIE
 $app->get('/place/listplacepartrajet/{idcompagnie}', function (Request $request, Response $response) {
    
    $idcompagnie = $request->getAttribute('idcompagnie');
    
  $sql = "SELECT DISTINCT place.num_place,place.ranger,place.nbre_place,trajet.id_trajet
          FROM place
          JOIN trajet ON place.id_trajet=trajet.id_trajet
          JOIN gare On trajet.id_gare=gare.id_gare
          JOIN compagnie ON gare.id_compagny=compagnie.id_compagny
          WHERE trajet.typ_voyage='allerretour' 
          AND compagnie.id_compagny = $idcompagnie";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

 // AJOUTER UNE PLACE
 $app->post('/place/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la car
  
  $nbre_place = $data["nbre_place"];
  $id_trajet = $data["id_trajet"];
  $ranger = $data["ranger"];
  $num_car = $data["num_car"];

  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO place(ranger,nbre_place,id_trajet,num_car)  VALUES (:ranger,:nbre_place,:id_trajet,:num_car)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ranger', $ranger);
    $stmt->bindParam(':nbre_place', $nbre_place);
    $stmt->bindParam(':id_trajet', $id_trajet);
    $stmt->bindParam(':num_car', $num_car);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


// MODIFIER UNE PLACE
$app->put('/place/update/{num_place}',
  function (Request $request, Response $response) 
{
$id = $request->getAttribute('num_place');
$data = json_decode($request->getBody()->getContents(), true);
$ranger = $data["ranger"];
$nbre_place = $data["nbre_place"];
$id_trajet = $data["id_trajet"];
$num_car = $data["num_car"];


$sql = "UPDATE place SET
         ranger = :ranger,
         nbre_place = :nbre_place,
         id_trajet = :id_trajet,
         num_car = :num_car
WHERE num_place = $id";
 

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':ranger', $ranger);
 $stmt->bindParam(':nbre_place', $nbre_place);
 $stmt->bindParam(':id_trajet', $id_trajet);
 $stmt->bindParam(':num_car', $typ_car);
 $result = $stmt->execute();

 

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});

// SUPPRIMER UN CAR
$app->delete('/place/delete/{num_place}', function (Request $request, Response $response, array $args) {
  $id = $args["num_place"];
  $sql = "DELETE FROM place WHERE num_place = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
 /*****************************************FIN API PLACES**************************************/ 



 /************************************************************************************************************ 
  * GESTION DES TICKCETS
 *************************************************************************************************************/
// LISTES DES TICKETS
$app->get('/ticket/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM ticket";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

 // AJOUTER UN TICKET
 $app->post('/ticket/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le client
  
  $prix_total = $data["prix_total"];
  $id_client = $data["id_client"];
  $id_trajet = $data["id_trajet"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO ticket(prix_total,id_client,id_trajet)  VALUES (:prix_total,:id_client,:id_trajet)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':prix_total', $prix_total);
    $stmt->bindParam(':id_client', $id_client);
    $stmt->bindParam(':id_trajet', $id_trajet);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


// MODIFIER UN TICKET
$app->put('/ticket/update/{id_ticket}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_ticket');
$data = json_decode($request->getBody()->getContents(), true);
$prix_total = $data["prix_total"];
$id_client = $data["id_client"];
$id_trajet = $data["id_trajet"];


$sql = "UPDATE ticket SET
         prix_total = :prix_total,
         id_client = :id_client,
         id_trajet = :id_trajet
WHERE id_ticket = $id";
 

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':prix_total', $prix_total);
 $stmt->bindParam(':id_client', $id_client);
 $stmt->bindParam(':id_trajet', $id_trajet);
 $result = $stmt->execute();

 

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});

// SUPPRIMER UN TICKET
$app->delete('/ticket/delete/{id_ticket}', function (Request $request, Response $response, array $args) {
  $id = $args["id_ticket"];
  $sql = "DELETE FROM ticket WHERE id_ticket = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });
 
 /*********************************************FIN API TICKETS************************************/ 
  

 
/*************************************************************************************************************
 * GESTION DES TRAJETS
 ************************************************************************************************************/

 // LISTES DES TRAJETS
$app->get('/trajet/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM trajet";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 }); 

//  LISTE DES TRAJETS PAR COMPAGNIE ET ORDONNEE PAR ORDRE D HEURE DE DEPART
// $app->post('/trajet/recherchetrajet', function (Request $request, Response $response) {
//   $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le client
  
//   $idvilledepart = $data["idvilledepart"];
//   $idvillearrive = $data["idvillearrive"];
//   $idcompagnie = $data["idcompagnie"];
//   $depart = $data["depart"];
//   $typ_voyage = $data["typ_voyage"];

  
//   // requete pour inserer les éléments du formulaire dans la base de donnée 
 
//   $sql = "SELECT * FROM trajet 
//          WHERE depart=
          
//    VALUES (:typ_voyage,:prix,:date,:duree,:depart,:id_gare,:heuredepart,:heurearrive,:destination)";
 
//   try {
//     $db = new Db();
//     $conn = $db->connect();
   
//     $stmt = $conn->prepare($sql);
//     $stmt->bindParam(':typ_voyage', $typ_voyage);
//     $stmt->bindParam(':prix', $prix);
//     $stmt->bindParam(':date', $date);
//     $stmt->bindParam(':duree', $duree);
//     $stmt->bindParam(':depart', $depart);
//     $stmt->bindParam(':id_gare', $id_gare);
//     $stmt->bindParam(':heurearrive', $heurearrive);
//     $stmt->bindParam(':heuredepart', $heuredepart);
//     $stmt->bindParam(':destination', $destination);

//     $result = $stmt->execute();
 
//     $msg = [
//       "message" => "Enregistrement reussi",
//       "status" => 200 
//     ];

//     $db = null;
//     $response->getBody()->write(json_encode($msg));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(200);
//   } catch (PDOException $e) {
//     $error = array(
//       "message" => $e->getMessage()
//     );
 
//     $response->getBody()->write(json_encode($error));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(500);
//   }
//  });










$app->get('/trajet/listparcompagnie/{idcompagnie}', function (Request $request, Response $response) {
   
   $idcompagnie = $request->getAttribute('idcompagnie');
   
  $sql = "SELECT trajet.id_trajet,trajet.prix,trajet.date,trajet.depart,trajet.typ_voyage,trajet.duree,
                 trajet.destination,trajet.heuredepart,trajet.heurearrive,trajet.destination,
                 trajet.id_gare,compagnie.nom,ville.nom
          FROM trajet 
          join gare ON trajet.id_gare = gare.id_gare
          join ville ON gare.id_ville = ville.id_ville
          join compagnie ON gare.id_compagny = compagnie.id_compagny
          WHERE compagnie.id_compagny = $idcompagnie
          ORDER BY trajet.heuredepart";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 // AJOUTER UN TRAJET
 $app->post('/trajet/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par le client
  
  $typ_voyage = $data["typ_voyage"];
  $prix = $data["prix"];
  $date = $data["date"];
  $duree = $data["duree"];
  $depart = $data["depart"];
  $id_gare = $data["id_gare"];
  $heuredepart = $data["heuredepart"];
  $heurearrive = $data["heurearrive"];
  $destination = $data["destination"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO trajet(typ_voyage,prix,date,duree,depart,id_gare,heuredepart,heurearrive,destination)  VALUES (:typ_voyage,:prix,:date,:duree,:depart,:id_gare,:heuredepart,:heurearrive,:destination)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':typ_voyage', $typ_voyage);
    $stmt->bindParam(':prix', $prix);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':duree', $duree);
    $stmt->bindParam(':depart', $depart);
    $stmt->bindParam(':id_gare', $id_gare);
    $stmt->bindParam(':heurearrive', $heurearrive);
    $stmt->bindParam(':heuredepart', $heuredepart);
    $stmt->bindParam(':destination', $destination);

    $result = $stmt->execute();
 
    $msg = [
      "message" => "Enregistrement reussi",
      "status" => 200 
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });


// MODIFIER UN TRAJET
$app->put('/trajet/update/{id_trajet}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_trajet');
$data = json_decode($request->getBody()->getContents(), true);
$typ_voyage = $data["typ_voyage"];
$prix = $data["prix"];
$date = $data["date"];
$depart = $data["depart"];
$duree = $data["duree"];
$id_gare = $data["id_gare"];
$heuredepart = $data["heuredepart"];
$heurearrive = $data["heurearrive"];
$destination = $data["destination"];


$sql = "UPDATE trajet SET
         prix=:prix,
         id_gare=:id_gare,
         duree =:duree,
         depart=:depart,
         typ_voyage=:typ_voyage,
         date=:date,
         heuredepart=:heuredepart,
         heurearrive=:heurearrive,
         destination=:destination
WHERE id_trajet = $id";
 

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':typ_voyage', $typ_voyage);
 $stmt->bindParam(':id_gare', $id_gare);
 $stmt->bindParam(':depart', $depart);
 $stmt->bindParam(':duree', $duree);
 $stmt->bindParam(':date', $date);
 $stmt->bindParam(':prix', $prix);
 $stmt->bindParam(':heurearrive', $heurearrive);
 $stmt->bindParam(':heuredepart', $heuredepart);
 $stmt->bindParam(':destination', $destination);
 $result = $stmt->execute();

 

 $msg = [
  "message" => "modification reussi",
  "status" => "200"
 ];

 $db = null;
echo "Update successful! ";
 $response->getBody()->write(json_encode($msg));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(200);
} catch (PDOException $e) {
 $error = array(
   "message" => $e->getMessage()
 );

 $response->getBody()->write(json_encode($error));
 return $response
   ->withHeader('content-type', 'application/json')
   ->withStatus(500);
}
});

// SUPPRIMER UN TRAJET
$app->delete('/trajet/delete/{id_trajet}', function (Request $request, Response $response, array $args) {
  $id = $args["id_trajet"];
  $sql = "DELETE FROM trajet WHERE id_trajet = $id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
 
    $msg=[
      "message" => "suppression effectué avec succès",
      "status" => "200"
    ];

    $db = null;
    $response->getBody()->write(json_encode($msg));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
 });

 /***************************************************** FIN API TRAJETS *******************************************/
$app->run();
