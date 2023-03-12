<?php
use App\Models\Db;
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
$name = $data["nom"];
$email = $data["email"];
$telephone = $data["telephone"];
$adresse = $data["adresse"];
$prenom = $data["prenom"];
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

 // AJOUTER UNE GARE
 $app->post('/gare/add', function (Request $request, Response $response, array $args) {
   
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la ville
  
  $nom = $data["nom"]; // permet de récuperer le nom
  $id_compagny = $data["id_compagny"];
  $id_ville = $data["id_ville"];
  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO gare(nom,id_compagny, id_ville)  VALUES (:nom,:id_compagny,:id_ville)";
 
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
$email = $data["id_ville"];
$adresse = $data["id_compagny"];



$sql = "UPDATE gare SET
         nom = :nom,
         id_compagny = :id_compagny,
         id_ville = :id_ville
WHERE id_gare = $id";
 

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
//**************************** GESTION DES GARES ******************************************* */




/*************************************************************************************************************
*  GESTION DES CARS
************************************************************************************************************** */

// LISTES DES CARS
$app->get('/car/list', function (Request $request, Response $response) {
  $sql = "SELECT * FROM car";
 
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
  $id_car = $data["typ_car"];
  
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
$nom = $data["id_compagny"];
$email = $data["typ_car"];



$sql = "UPDATE compagnie SET
         typ_compagny = :typ_compagny,
         typ_car = :typ_car,
WHERE id_car = $id";
 

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

/******************************************** FIN  DES CARS ****************************************************/


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

 // AJOUTER UNE PLACE
 $app->post('/place/add', function (Request $request, Response $response, array $args) {
   
  //$data = $request->getParsedBody(); 
  $data = json_decode($request->getBody()->getContents(), true); // Permet de récuperer le contenu envoye par la car
  
  $nbre_place = $data["nbre_place"];
  $id_trajet = $data["id_trajet"];
  $ranger = $data["ranger"];
  $ranger = $data["num_car"];

  
  // requete pour inserer les éléments du formulaire dans la base de donnée 
 
  $sql = "INSERT INTO place(ranger,nbre_place,id_trajet,num_car)  VALUES (:ranger,:nbre_place,:nbre_trajet,:num_car)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ranger', $ranger);
    $stmt->bindParam(':nbre_place', $nbre_place);
    $stmt->bindParam(':num_car', $num_car);
    $stmt->bindParam(':trajet', $id_trajet);

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
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('num_place');
$data = json_decode($request->getBody()->getContents(), true);
$ranger = $data["ranger"];
$nbre_place = $data["nbre_place"];
$id_trajet = $data["id_trajet"];
$typ_car = $data["typ_car"];


$sql = "UPDATE place SET
         ranger = :ranger,
         nbre_place = :nbre_place,
         id_trajet = :id_trajet,
         typ_car = :typ_car
WHERE id_car = $id";
 

try {
 $db = new Db();
 $conn = $db->connect();

 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':ranger', $ranger);
 $stmt->bindParam(':nbre_place', $nbre_place);
 $stmt->bindParam(':id_trajet', $id_trajet);
 $stmt->bindParam(':typ_car', $typ_car);
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
$app->delete('/place/delete/{id_car}', function (Request $request, Response $response, array $args) {
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
 /*****************************************FIN PLACES ********************************************************/




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
$app->put('/ticket/update/{num_place}',
  function (Request $request, Response $response, array $args) 
{
$id = $request->getAttribute('id_ticket');
$data = json_decode($request->getBody()->getContents(), true);
$ranger = $data["prix_total"];
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
 
 /*********************************************FIN TICKETS ***************************************************/
  

 
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

 /***************************************************** FIN TRAJETS *******************************************/
$app->run();
//$app->get('/gare/listparville/{nomville}', function (Request $request, Response $response, array $args)
