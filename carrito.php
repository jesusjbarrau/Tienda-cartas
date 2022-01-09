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
    $consulta = $dwes->query("SELECT nombre, apellidos, id FROM usuario WHERE nombreUsuario='$usuario'");
    if(($tupla = $consulta->fetch(PDO::FETCH_OBJ)) != null){
        $id = $tupla->id;
        $nombre = $tupla->nombre;
        $apellidos = $tupla->apellidos;
    }
    
}

//CERRAR SESION:
if(isset($_POST['cerrar'])){
    session_destroy();
    header('Location: index.php');
}

//CONSULTAR POR NOMBRE:
if(isset($_GET['buscarNombre'])){

    $suma = null;

    if(isset($_GET['comienzo'])){
        $comienzo = filtrado($_GET['comienzo']);
    } else $comienzo = 0;

    if(empty($_GET['nombreCarta'])){
        $consulta03 = $dwes->query("SELECT id, nombre, precio, imagen FROM carta LIMIT $comienzo,8");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta");
    } else {
        $nombreCarta = filtrado($_GET['nombreCarta']);
        $consulta03 = $dwes->query("SELECT id, nombre, precio, imagen FROM carta WHERE nombre LIKE '%$nombreCarta%' LIMIT $comienzo,8");
        $suma = $dwes->query("SELECT COUNT(id) AS numero FROM carta WHERE nombre LIKE '%$nombreCarta%'");
    }

    $numeroTotal = $suma->fetch(PDO::FETCH_OBJ)->numero;

}


//CESTA/CARRITO:
$cesta = [];
if(!empty($_SESSION['cesta'])){
    $cesta = $_SESSION['cesta'];
}


//CALCULAR PRECIO DEL CARRITO:
$precioTotal = 0;
foreach ($cesta as $id => $cantidad) {
    
    $consulta01 = $dwes->query("SELECT precio FROM carta WHERE id=$id");
    if(($tupla = $consulta01->fetch(PDO::FETCH_OBJ)) != null){
        $precioTotal = $precioTotal + ($tupla->precio * $cantidad);
    }

}

//BORRAR ARTICULO:

if(isset($_POST['borrar'])){
    $idProducto = filtrado($_POST['borrar']);
    unset($_SESSION['cesta'][$idProducto]);
    header('Location: carrito.php');
}

//MODIFICAR ARTICULO:

if(isset($_POST['modificar'])){
    $idProducto = intval(filtrado($_POST['modificar']));
    $consulta02 = $dwes->query("SELECT imagen, nombre, stock FROM carta WHERE id=$idProducto");
    if(($tupla = $consulta02->fetch(PDO::FETCH_OBJ)) != null){
        $imagen = $tupla->imagen;
        $nombreCarta = $tupla->nombre;
        $cantidad = $_SESSION['cesta'][$idProducto];
        $stock = $tupla->stock;
    }
}

//ACTUALIZAR ARTICULO:

if(isset($_POST['actualizar'])){
    $idProducto = filtrado($_POST['actualizar']);
    $nuevaCantidad = intval(filtrado($_POST['nuevaCantidad']));
    $_SESSION['cesta'][$idProducto] = $nuevaCantidad;
    header('Location: carrito.php');
}

//COMPRAR CESTA:

if(isset($_POST['comprarCesta'])){
    $cesta = $_SESSION['cesta'];
}

//CONFIRMAR COMPRA:

if(isset($_POST['confirmar'])){
    
    $cesta = $_SESSION['cesta'];
    
    foreach ($cesta as $id => $cantidad) {
        $borrado = $dwes->exec("UPDATE carta SET stock = (stock - $cantidad) WHERE id=$id");
    }

    $cesta = $_SESSION['cesta'] = [];

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

        <form id="buscarNombre" action="listado.php" method="get">

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
                    <li><button class="dropdown-item" name="cerrar">Cerrar sesion</button></li>
                </ul>
            </div>
        </form>

    </header>

    <div id="container">
    <main id="carrito">
<?php

    if(isset($_POST['modificar'])){

?>

        <h2>Modifique la cantidad</h2>
        <table>
            <tr>
                <th>Carta</th>
                <th>Cantidad</th>
            </tr>
            <form action="" method="post">
                <tr>
                    <td><img class="imgForm" src="./img/<?=$imagen?>.png" alt="img"></td>
                    <td>
                        <select name="nuevaCantidad">
<?php
                        for ($i=1; $i <= $stock; $i++) { 
?>
                            <option value="<?=$i?>"><?=$i?></option>
<?php
                        }
?>
                        </select>
                    </td>
                    <td><button type="submit" name="actualizar" value=<?=$idProducto?>>Actualizar</button></td>
                </tr>        
            </form>
        </table>
        

<?php

    } else if (isset($_POST['comprarCesta'])) {

?>

        <table>
                    <form action="" method="post">
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                        </tr>
<?php
                foreach ($cesta as $id => $cantidad) {
                    
                    $consulta = $dwes->query("SELECT id, imagen, nombre, precio FROM carta WHERE id=$id");
                    $tupla = $consulta->fetch(PDO::FETCH_OBJ);

?>

                        <tr>
                            <td><img src="./img/<?=$tupla->imagen?>.png" alt="Imagen carta" srcset=""></td>
                            <td><?=$tupla->nombre?></td>
                            <td><?=$tupla->precio?></td>
                            <td><?=$cantidad?></td>
                        </tr>

<?php
                }
?>
                    </form>
        </table>
        <div id="menuCompra">
            <h5>Precio Total: <?=$precioTotal?>€</h5>
            <form class="menuCompra" action="" method="post">
                <button type="submit" class="btn btn-primary" name="confirmar">Confirmar compra</button>
            </form>
        </div>


<?php

    } else if(isset($_POST['confirmar'])) {

?>

            <div id="errorCarrito">
                <h3>Compra realizada</h3>
                <button type="button" class="btn btn-primary">
                    <a href="listado.php">Seguir comprando</a>
                </button>
            </div>

<?php

    } else {

?>

            <h2>Su cesta de la compra</h2>
<?php
            if(empty($_SESSION['cesta'])){
?>
            <div id="errorCarrito">
                <h3>Su carrito esta actualmente vacio</h3>
            </div>
<?php
            } else {
?>
                <table>
                    <form action="" method="post">
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                        </tr>
<?php
                foreach ($cesta as $id => $cantidad) {
                    
                    $consulta = $dwes->query("SELECT id, imagen, nombre, precio FROM carta WHERE id=$id");
                    $tupla = $consulta->fetch(PDO::FETCH_OBJ);

?>

                        <tr>
                            <td><img src="./img/<?=$tupla->imagen?>.png" alt="Imagen carta" srcset=""></td>
                            <td><?=$tupla->nombre?></td>
                            <td><?=$tupla->precio?></td>
                            <td><?=$cantidad?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="submit" class="btn btn-secondary" value=<?=$tupla->id?> name="modificar">Modificar</button>
                                    <button type="submit" class="btn btn-secondary" value=<?=$tupla->id?> name="borrar">Borrar</button>
                                </div>
                            </td>
                        </tr>

<?php

                }
?>
                    </form>
                </table>
                <div id="menuCompra">
                    <h5>Precio Total: <?=$precioTotal?>€</h5>
                    <form class="menuCompra" action="" method="post">
                        <button type="submit" class="btn btn-primary" name="comprarCesta">Comprar</button>
                    </form>
                </div>
<?php
            }
?>
        </main>

<?php
    }
?>

    </div>

    <footer></footer>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"></script>
</body>
</html>