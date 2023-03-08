# cloner le projet
git clone url du projet

# installer les dependances
composer update

# Demarrer le projet,
php -S localhost:PORT -t public

#Config de la base de données

/src/Models/Db.php

<code>
<?php

namespace App\Models;

use \PDO;

class DB
{
    private $host = 'localhost';
    private $user = 'postgres';
    private $pass = '';
    private $dbname = 'dvdrental';

    public function connect()
    {
        $conn_str = "pgsql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}

</code
