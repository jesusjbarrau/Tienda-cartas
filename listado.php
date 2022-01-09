<?php

//BASE DE DATOS:

try 
{
    $opc = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
    $dwes = new PDO('mysql:host=localhost;dbname=tienda', 'root', '',$opc);       
}
catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

//FUNCIONES:
function filtrado($datos){
    $datos = trim($datos); // Elimina espacios antes y después de los datos
    $datos = stripslashes($datos); // Elimina backslashes \
    $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
    return $datos;
}



//CONTROL DE ACCESO:
session_start();

if($_SESSION['usuario'] == null){
    header('Location: index.php');
} else {
    $usuario = $_SESSION['usuario'];
    $consulta = $dwes->query("SELECT nombre, apellidos, id, administrador FROM usuario WHERE nombreUsuario='$usuario'");
    if(($tupla = $consulta->fetch(PDO::FETCH_OBJ)) != null){
        $id = $tupla->id;
        $nombre = $tupla->nombre;
        $apellidos = $tupla->apellidos;
        $administrador = $tupla->administrador;
    }
    if($administrador === 'si'){
        header('Location: panelControl.php');
    }
}


//VARIABLES:
if(isset($_GET['comienzo'])){
    $comienzo = filtrado($_GET['comienzo']);
} else $comienzo = 0;

$num = 4;

//CONSULTA DE INICIO:
$total = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE estado != 'inactivo'");
$tupla = $total->fetch(PDO::FETCH_OBJ);
$numeroTotal = $tupla->numero;
$consulta = $dwes->query("SELECT id, nombre, precio, imagen, stock FROM carta WHERE estado != 'inactivo' ORDER BY id DESC LIMIT 4");

//CERRAR SESION:
if(isset($_POST['cerrar'])){
    session_destroy();
    header('Location: index.php');
}

//VER CARRITO:
if(isset($_POST['irCarrito'])){
    header('Location: carrito.php');
}

//AÑADIR AL CARRITO:

if(isset($_POST['añadir'])){

    $idProducto = filtrado($_POST['añadir']);

    if(isset($_POST['cantidadCarta'])){
        $cantidad = filtrado($_POST['cantidadCarta']);
    } else $cantidad = 1;

    //AÑADIMOS A LA CESTA EL ID Y LA CANTIDAD, EN ESTE CASO 1:
    $_SESSION['cesta'][$idProducto] = $cantidad;

    header('Location: carrito.php');

}

//VER INFO DE LA CARTA:
if(isset($_POST['infocarta'])){
    $codigoCarta = filtrado($_POST['infocarta']);
    $codigoCarta = intval($codigoCarta);
    $consulta01 = $dwes->query("SELECT * FROM carta WHERE id=$codigoCarta");
    $row = $consulta01->fetch(PDO::FETCH_OBJ);
}

//CONSULTAR POR NOMBRE:
if(isset($_GET['buscarNombre'])){

    $suma = null;

    if(isset($_GET['comienzo'])){
        $comienzo = filtrado($_GET['comienzo']);
    } else $comienzo = 0;

    if(empty($_GET['nombreCarta'])){
        $consulta03 = $dwes->query("SELECT * FROM carta WHERE estado != 'inactivo' LIMIT $comienzo,8");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE estado != 'inactivo'");
    } else {
        $nombreCarta = filtrado($_GET['nombreCarta']);
        $consulta03 = $dwes->query("SELECT * FROM carta WHERE nombre LIKE '%$nombreCarta%' AND estado != 'inactivo' LIMIT $comienzo,8");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE nombre LIKE '%$nombreCarta%' AND estado != 'inactivo'");
    }

    $numeroTotal = $suma->fetch(PDO::FETCH_OBJ)->numero;

}

//CONSULTAR POR FILTRO:
if(isset($_GET['buscarFiltro'])){

    if(isset($_GET['comienzo'])){
        $comienzo = filtrado($_GET['comienzo']);
    } else $comienzo = 0;

    if(isset($_GET['clan'])){
        $clan = filtrado($_GET['clan']);
    }

    if(isset($_GET['grado'])){
        $grado = filtrado($_GET['grado']);
    }

    if(isset($_GET['numero'])){
        $num = filtrado($_GET['numero']);
    }

    $suma = null;

    if($clan == "todos" && $grado == "todos") {
        $consulta02 = $dwes->query("SELECT * FROM carta WHERE estado != 'inactivo' LIMIT $comienzo,$num");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE estado != 'inactivo'");
    } else if($clan === "todos") {
        $consulta02 = $dwes->query("SELECT * FROM carta WHERE grado=$grado AND estado != 'inactivo' LIMIT $comienzo,$num");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE grado=$grado AND estado != 'inactivo'");
    } else if($grado === "todos") {
        $consulta02 = $dwes->query("SELECT * FROM carta WHERE clan='$clan' AND estado != 'inactivo' LIMIT $comienzo,$num");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE clan='$clan' AND estado != 'inactivo'");
    } else {
        $consulta02 = $dwes->query("SELECT * FROM carta WHERE clan='$clan' AND grado=$grado AND estado != 'inactivo' LIMIT $comienzo,$num");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE clan='$clan' AND grado=$grado AND estado != 'inactivo'");
    }

    $numeroTotal = $suma->fetch(PDO::FETCH_OBJ)->numero;
    
}

//IR A PANEL DE CONTROL:
if(isset($_POST['irPanel'])){
    header('Location: panelControl.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/style.css">
    <title>Listado</title>
</head>
<body>

    <header>

        <a style="text-style: none" href="listado.php"><h1>CardAttack</h1></a>

        <form id="buscarNombre" action="" method="get">

            <div class="input-group mb-3">

                <span class="input-group-text">Nombre</span>
                <input type="text" name="nombreCarta" class="form-control" aria-label="Username">

                <input id="botonBuscar" class="input-group-text btn btn-primary" type="submit" name="buscarNombre" value="Buscar">
                <input type="hidden" name="comienzo" value="0">
            
            </div>

        </form>

        <form id="menuCuenta" action="" method="post">
            <div id="menuUsuario" class="btn-group">
                <button class="btn btn-primary btn-sm" type="button" disabled>
                    <?=$nombre . " " . $apellidos?>
                </button>
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item" name="irCarrito">Carrito</button></li>
<?php 
                    if($administrador == 'si') {
?>
                    <li><button class="dropdown-item" name="irPanel">Panel de Control</button></li>
<?php
                    }
?>
                    <li><button class="dropdown-item" name="cerrar">Cerrar sesion</button></li>
                </ul>
            </div>
        </form>

    </header>

<?php
    if(empty($_GET) && empty($_POST)){
?>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img class="d-block w-100" src="./img/pos01.png" alt="First slide">
            </div>
            <div class="carousel-item">
            <img class="d-block w-100" src="./img/pos02.png" alt="Second slide">
            </div>
            <div class="carousel-item">
            <img class="d-block w-100" src="./img/pos03.png" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>

<?php
    }
?>

    <div id="container">

        <nav>
            <h2>Buscar por filtro</h2>

            <form action="" method="get">
                <label>Clan</label>
                <select type="submit" name="clan">
                    <option value="todos">Todos</option>
<?php

                    $consulta01 = $dwes->query("SELECT clan FROM carta GROUP BY clan");
                    while(($tupla = $consulta01->fetch(PDO::FETCH_OBJ)) != null){

?>

                        <option value="<?=$tupla->clan?>"><?=$tupla->clan?></option>

<?php

                    }

?>
                    
                </select>
                <br> <br>
                <label>Grado</label>
                <select type="submit" name="grado">
                    <option value="todos">Todos</option>
                    <option value="0">Grado 0</option>
                    <option value="1">Grado 1</option>
                    <option value="2">Grado 2</option>
                    <option value="3">Grado 3</option>
                    <option value="4">Grado 4</option>
                </select>
                <br>
                <br>
                <label>Buscar en:</label> <br>
                <select type="submit" name="numero">
                    <option value="4">4 en 4</option>
                    <option value="8">8 en 8</option>
                    <option value="12">12 en 12</option>
                </select>
                <br> <br>
                <button id="botonFiltro" type="submit" name="buscarFiltro" class="btn btn-primary">Buscar</button>
                <input type="hidden" name="comienzo" value="0">
            </form>
        </nav>

        <main>

<?php

    if(isset($_GET['buscarFiltro'])) {

        $tupla = $consulta02->fetch(PDO::FETCH_OBJ);

        if($tupla == null){
?>

            <article>
                <h2>No se han encontrado resultados</h2>
            </article>

<?php
        } else {
?>
            <form id="listado" action="listado.php" method="post">
                    <div class="row">
<?php

                    do{

?>
                    <div class="col-sm-3">
                        <div class="card">
                            <img class="card-img-top" src="./img/<?=$tupla->imagen?>.png" alt="Card image cap">
                            <div class="card-body">
                                <p class="card-title"><?=$tupla->nombre?></p>
                                <p class="precio">PVP: <?=$tupla->precio?>€</p>
                            </div>
                            <div class="btn-group" role="group" aria-label="Basic example">
<?php
                                if($tupla->stock == 0) {
?>
                                    <button type="submit" class="btn btn-secondary" name="añadir" value=<?=$tupla->id?> disabled>Agotado</button>
<?php
                                } else {
?>
                                    <button type="submit" class="btn btn-warning" name="añadir" value=<?=$tupla->id?>>Añadir</button>
<?php 
                                }
?>
                                <button type="submit" class="btn btn-primary" name="infocarta" value=<?=$tupla->id?>>Ver info</button>
                            </div>
                        </div>
                    </div>

<?php

                    }while(($tupla = $consulta02->fetch(PDO::FETCH_OBJ)) != null);

?>
                    </div>
            
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <?php
                            if($comienzo != 0) {
                                echo "<button class='btn btn-primary'>
                                <a href='listado.php?buscarFiltro=&clan=".$clan."&grado=".$grado."&numero=".$num."&comienzo=".($comienzo - $num)."'>Anterior</a>
                                </button>";
                            }

                            if((($comienzo + $num) < $numeroTotal)){
                                echo "<button class='btn btn-primary'>
                                <a href='listado.php?buscarFiltro=&clan=".$clan."&grado=".$grado."&numero=".$num."&comienzo=".($comienzo + $num)."'>Siguiente</a>
                                </button>";
                            } 
                        ?>
                    </div>

            </form>

<?php
        }

    } else if(isset($_POST['infocarta'])) {

?>

            <form id="infocarta" action="" method="post">
                <div>
                    <img src="./img/<?=$row->imagen?>.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5><?=$row->nombre?></h5>
                    <p>Grade: <?=$row->grado?></p>
                    <p>Clan: <?=$row->clan?></p>
                    <p>PVP: <?=$row->precio?> €</p>
                    <div class="input-group">
<?php
                if($row->stock == 0) {
?>
                    <button type="submit" class="btn btn-secondary" name="añadir" disabled>Agotado</button>
<?php
                } else {
?>
                    <select name="cantidadCarta" class="custom-select" id="inputGroupSelect04">
<?php
                    for ($cantidad=1; $cantidad <= $row->stock; $cantidad++) { 
?>
                        <option value="<?=$cantidad?>"><?=$cantidad?></option>
<?php
                    }
?>
                    </select>
                    <button type="submit" class="btn btn-warning" name="añadir" value=<?=$row->id?>>Añadir a carrito</button>
<?php 
                }
?>
                </div>
            </form>

<?php

    } else if(isset($_GET['buscarNombre'])){

        $tupla = $consulta03->fetch(PDO::FETCH_OBJ);

        if($tupla == null){

?>

            <article>
                <h2>No se han encontrado resultados</h2>
            </article>

<?php

        } else {
?>

                <form id="listado" action="" method="post">
                    <div class="row">
<?php

                    do{

?>
                    <div class="col-sm-3">
                        <div class="card">
                            <img class="card-img-top" src="./img/<?=$tupla->imagen?>.png" alt="Card image cap">
                            <div class="card-body">
                                <p class="card-title"><?=$tupla->nombre?></p>
                                <p class="precio">PVP: <?=$tupla->precio?>€</p>
                            </div>
                            <div class="btn-group" role="group" aria-label="Basic example">
<?php
                                if($tupla->stock == 0) {
?>
                                    <button type="submit" class="btn btn-secondary" name="añadir" value=<?=$tupla->id?> disabled>Agotado</button>
<?php
                                } else {
?>
                                    <button type="submit" class="btn btn-warning" name="añadir" value=<?=$tupla->id?>>Añadir</button>
<?php 
                                }
?>
                                <button type="submit" class="btn btn-primary" name="infocarta" value=<?=$tupla->id?>>Ver info</button>
                            </div>
                        </div>
                    </div>

<?php

                    }while(($tupla = $consulta03->fetch(PDO::FETCH_OBJ)) != null);

        }

?>
                    </div>
                </form>

                <div class="btn-group" role="group" aria-label="Basic example">
                <?php
                    if($comienzo != 0) {
                        echo "<button class='btn btn-primary'>
                        <a href='listado.php?buscarNombre=&comienzo=".($comienzo - 8)."'>Anterior</a>
                        </button>";
                    }

                    if((($comienzo + $num) < $numeroTotal)){
                        echo "<button class='btn btn-primary'>
                        <a href='listado.php?buscarNombre=&comienzo=".($comienzo + 8)."'>Siguiente</a>
                        </button>";
                    } 
                ?>
                </div>
                
            </div>

<?php

    } else {

?>
            <h2 id="lanzamiento">Últimos lanzamientos</h2>
            <form id="listado" action="" method="post">
                <div class="row">
<?php

                        while(($tupla = $consulta->fetch(PDO::FETCH_OBJ)) != null){

?>
                    <div class="col-sm-3">
                        <div class="card">
                            <img class="card-img-top" src="./img/<?=$tupla->imagen?>.png" alt="Card image cap">
                            <div class="card-body">
                                <p class="card-title"><?=$tupla->nombre?></p>
                                <p class="precio">PVP: <?=$tupla->precio?>€</p>
                            </div>
                            <div class="btn-group" role="group" aria-label="Basic example">
<?php
                                if($tupla->stock == 0) {
?>
                                    <button type="submit" class="btn btn-secondary" name="añadir" value=<?=$tupla->id?> disabled>Agotado</button>
<?php
                                } else {
?>
                                    <button type="submit" class="btn btn-warning" name="añadir" value=<?=$tupla->id?>>Añadir</button>
<?php 
                                }
?>
                                <button type="submit" class="btn btn-primary" name="infocarta" value=<?=$tupla->id?>>Ver info</button>
                            </div>
                        </div>
                    </div>

<?php

                        }

?>
                </div>
            </form>

<?php

    }

?>
            
        </main>

    </div>

    <footer>

    </footer>    

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"></script>
</body>
</html>