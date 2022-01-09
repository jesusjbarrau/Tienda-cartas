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

session_start();

//CONTROL DE ACCESO:
if($_SESSION['usuario'] == null){
    header('Location: index.php');
} else {
    $nombreUsuarioActual = $_SESSION['usuario'];
    $consulta03 = $dwes->query("SELECT id, administrador FROM usuario WHERE nombreUsuario='$nombreUsuarioActual'");
    $tupla = $consulta03->fetch(PDO::FETCH_OBJ);
    $usuarioAdmin = $tupla->administrador;
    if($usuarioAdmin === 'no'){
        header('Location: listado.php');
    }
}

//FUNCIONES:
function filtrado($datos){
    $datos = trim($datos); // Elimina espacios antes y después de los datos
    $datos = stripslashes($datos); // Elimina backslashes \
    $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
    return $datos;
}

//VARIABLES:
$errores['error'] = '';
$error = false;

//AÑADIR ARTICULO:
if(isset($_POST['añadirUsuario'])){

    if(empty($_POST['nombreUsu'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $nombreUsu = filtrado($_POST['nombreUsu']);

    if(empty($_POST['apellidosUsu'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $apellidosUsu = filtrado($_POST['apellidosUsu']);

    if(empty($_POST['emailUsu'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $emailUsu = filtrado($_POST['emailUsu']);

    if(empty($_POST['fechaNac'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $fechaNac = filtrado($_POST['fechaNac']);

    if(empty($_POST['admin'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $admin = filtrado($_POST['admin']);

    if(empty($_POST['password'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else $password = password_hash(filtrado($_POST['password']), PASSWORD_DEFAULT);


    if(!$error) {
        $actu = $dwes->exec("INSERT INTO usuario (nombre,apellidos,nombreUsuario,fecha,administrador,contrasenia)
        VALUES ('$nombreUsu','$apellidosUsu','$emailUsu','$fechaNac','$admin','$password')");
    }

}

//CONSULTA DE ENTRADA USUARIOS:
if(isset($_POST['usuarios']) || isset($_POST['añadirUsuario'])){
    $consulta02 = $dwes->query("SELECT * FROM usuario WHERE nombreUsuario !='$nombreUsuarioActual'");
}

//MODIFICAR USUARIO:
if(isset($_POST['modificarUsuario'])) {
    $idUsuario = filtrado($_POST['modificarUsuario']);
    $consulta02 = $dwes->query("SELECT * FROM usuario WHERE id=$idUsuario");
    $tupla = $consulta02->fetch(PDO::FETCH_OBJ);
}

if(isset($_POST['actualizarUsuario'])) {
    $idUsuario = filtrado($_POST['actualizarUsuario']);
    $nombreNew = filtrado($_POST['nombreNew']);
    $dateNew = filtrado($_POST['dateNew']);
    $admin = filtrado($_POST['administrador']);
    $estado = filtrado($_POST['estado']);
    $actu = $dwes->exec("UPDATE usuario SET nombre='$nombreNew', fecha='$dateNew', administrador='$admin', estado='$estado' WHERE id=$idUsuario");
}

//AÑADIR ARTICULO:
if(isset($_POST['añadirArticulo'])){

    $error = false;

    if(empty($_POST['nombreCarta'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $nombreCarta = filtrado($_POST['nombreCarta']);
    }

    if(empty($_POST['clanCarta'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $clanCarta = filtrado($_POST['clanCarta']);
    }

    if(empty($_POST['grado'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $grado = intval(filtrado($_POST['grado']));
    }

    if(empty($_POST['nombreImagen'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $nombreImagen = filtrado($_POST['nombreImagen']);
    }

    if(empty($_POST['precio'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $precio = floatval(filtrado($_POST['precio']));
    }

    if(empty($_POST['stockCarta'])){
        $errores['error'] = "Rellene todo el formulario.";
        $error = true;
    } else {
        $stock = intval(filtrado($_POST['stockCarta']));
    }


    if(!$error){
        $actu = $dwes->exec("INSERT INTO carta (nombre,imagen,precio,stock,grado,clan) 
        VALUES ('$nombreCarta','$nombreImagen',$precio,$stock,$grado,'$clanCarta')");
    }

}

//CONSULTA DE ENTRADA ARTICULOS:
if(isset($_POST['articulos']) || isset($_POST['añadirArticulo'])){

    $consulta02 = $dwes->query("SELECT * FROM carta");

}


//MODIFICAR ARTICULO:
if(isset($_POST['modificarProducto'])){
    $idProducto = intval(filtrado($_POST['modificarProducto']));
    $consulta02 = $dwes->query("SELECT * FROM carta WHERE id=$idProducto");
    $tupla = $consulta02->fetch(PDO::FETCH_OBJ);
}

if(isset($_POST['actualizarArticulo'])){

    $idNew = intval(filtrado($_POST['actualizarArticulo']));
    $nombreNew = filtrado($_POST['nombreNew']);
    $gradoNew = intval(filtrado($_POST['gradoNew']));
    $clanNew = filtrado($_POST['clanNew']);
    $precioNew = floatval(filtrado($_POST['precioNew']));
    $stockNew = intval(filtrado($_POST['stockNew']));
    $estado = filtrado($_POST['estado']);

    $actu = $dwes->exec("UPDATE carta SET nombre='$nombreNew', grado=$gradoNew, clan='$clanNew', precio=$precioNew, stock=$stockNew, estado='$estado' 
    WHERE id=$idNew");

}

//CERRAR SESION:

if(isset($_POST['cerrar'])){
    session_destroy();
    header('Location: index.php');
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
    <title>Tienda Web</title>
</head>
<body id="panelControl">

    <header id="control">

        <h1>CardAttack | Panel de control</h1>

        <form class="menuCompra" action="" method="post">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="submit" class="btn btn-secondary" name="usuarios">Usuarios</button>
                <button type="submit" class="btn btn-secondary" name="articulos">Articulos</button>
                <button type="submit" name="cerrar" class="btn btn-dark" name="cerrar">Cerrar Sesión</button>
            </div>
        </form>

    </header>

<?php
    if(isset($_POST['articulos']) || isset($_POST['añadirArticulo'])){
?>

    <nav id='navegacion'>
        <form action="" method="post">
            <table>
                <tr>
                    <td><label>Nombre</label></td>
                    <td><input type="text" name="nombreCarta"></td>
                    <td><label>Clan</label></td>
                    <td><input type="text" name="clanCarta"></td>
                    <td><label>Grado</label></td>
                    <td>
                        <select name="grado">
                            <option value="0">Grado 0</option>
                            <option value="1">Grado 1</option>
                            <option value="2">Grado 2</option>
                            <option value="3">Grado 3</option>
                            <option value="4">Grado 4</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Nombre imagen</label></td>
                    <td><input type="text" name="nombreImagen"></td>
                    <td><label>Precio</label></td>
                    <td><input type="text" name="precio"></td>
                    <td><label>Stock</label></td>
                    <td><input type="text" name="stockCarta"></td>
                </tr>
                <tr><td><button type="submit" class="btn btn-primary" name="añadirArticulo">Añadir articulo</button></td></tr>
<?php 
                if($error) {
?>
                <tr><span style="color:red"><?=$errores['error']?></span></tr>
<?php
                }
?>
            </table>
        </form>
    </nav>

    <main id="listadoUsuarios">

        <h2>Listado de articulos</h2>
        <?php
                $tupla = $consulta02->fetch(PDO::FETCH_OBJ);
                if($tupla == null) {
?>
                    <div id="errorCarrito">
                        <h3>No hay resultado de busqueda</h3>
                    </div>
<?php
                } else {

?>
        <form class="menuCompra" action="" method="post">
            <table>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Clan</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                </tr>
<?php
                    do{
?>
                <tr>
                    <td><img src="./img/<?=$tupla->imagen?>.png" alt="Imagen carta" srcset=""></td>
                    <td><?=$tupla->nombre?></td>
                    <td><?=$tupla->grado?></td>
                    <td><?=$tupla->clan?></td>
                    <td><?=$tupla->precio?></td>
                    <td><?=$tupla->stock?></td>
                    <td><?=$tupla->estado?></td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="submit" class="btn btn-secondary" value=<?=$tupla->id?> name="modificarProducto">Modificar</button>
                        </div>
                    </td>
                </tr>
<?php
                } while(($tupla = $consulta02->fetch(PDO::FETCH_OBJ)) != null);

            }
?>
            </table>
        </form>
    </main>

<?php
    } 

    if(isset($_POST['modificarProducto'])) {

?>

        <main id="listadoUsuarios" class="menuModif">
            <img src="./img/<?=$tupla->imagen?>.png" alt="<?=$tupla->imagen?>">
            <form action="" method="post">
                <label>Nombre</label> <br>
                <input type="text" name="nombreNew" value='<?=$tupla->nombre?>'> <br>
                <label>Grado</label> <br>
                <input type="number" name="gradoNew" value='<?=$tupla->grado?>'> <br>
                <label>Clan</label> <br>
                <input type="text" name="clanNew" value='<?=$tupla->clan?>'> <br>
                <label>Precio</label> <br>
                <input type="text" name="precioNew" value='<?=$tupla->precio?>'> <br>
                <label>Stock</label> <br>
                <input type="number" name="stockNew" value='<?=$tupla->stock?>'> <br>
                <label>Estado</label> <br>
                <select name="estado">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select> <br> <br>
                <button type="submit" class="btn btn-primary" name="actualizarArticulo" value='<?=$tupla->id?>'>Actualizar</button>
            </form>

        </main>

<?php
    }

    if(isset($_POST['actualizarArticulo'])) {

?>
        <main id="listadoUsuarios">
            <div id="errorCarrito">
                <h3>Articulo actualizado</h3>
            </div>
        </main>
<?php
    }

    if(isset($_POST['usuarios']) || isset($_POST['añadirUsuario'])){
?>

    <nav id='navegacion'>
        <form action="" method="post">
            <table>
                <tr>
                    <td><label>Nombre</label></td>
                    <td><input type="text" name="nombreUsu"></td>
                    <td><label>Apellidos</label></td>
                    <td><input type="text" name="apellidosUsu"></td>
                    <td><label>Email</label></td>
                    <td><input type="text" name="emailUsu"></td>
                </tr>
                <tr>
                    <td><label>Fecha nacimientos</label></td>
                    <td><input type="date" name="fechaNac"></td>
                    <td><label>Administrador</label></td>
                    <td>
                        <select name="admin">
                            <option value="no">No</option>
                            <option value="si">Si</option>
                        </select>
                    </td>
                    <td><label>Contraseña</label></td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr><td><button type="submit" class="btn btn-primary" name="añadirUsuario">Añadir usuario</button></td></tr>
<?php 
                if($error) {
?>
                <tr><span style="color:red"><?=$errores['error']?></span></tr>
<?php
                }
?>
            </table>
        </form>
    </nav>

    <main id="listadoUsuarios">

        <h2>Listado de usuarios</h2>
        <?php
                $tupla = $consulta02->fetch(PDO::FETCH_OBJ);
                if($tupla == null) {
?>
                    <div id="errorCarrito">
                        <h3>No hay resultado de busqueda</h3>
                    </div>
<?php
                } else {

?>
        <form class="menuCompra" action="" method="post">
            <table>
                <tr>
                    <th>id</th>
                    <th>Nombre usuario</th>
                    <th>Nombre</th>
                    <th>Fecha nacimiento</th>
                    <th>Administrador</th>
                    <th>Estado</th>
                </tr>
<?php
                    do{
?>
                <tr>
                    <td><?=$tupla->id?></td>
                    <td><?=$tupla->nombreUsuario?></td>
                    <td><?=$tupla->nombre?></td>
                    <td><?=$tupla->fecha?></td>
                    <td><?=$tupla->administrador?></td>
                    <td><?=$tupla->estado?></td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="submit" class="btn btn-secondary" value=<?=$tupla->id?> name="modificarUsuario">Modificar</button>
                        </div>
                    </td>
                </tr>
<?php
                } while(($tupla = $consulta02->fetch(PDO::FETCH_OBJ)) != null);

            }
?>
            </table>
        </form>
    </main>

<?php
    }
    
    if(isset($_POST['modificarUsuario'])) {
?>
                        
    <main id="listadoUsuarios" class="menuModif">
        <form action="" method="post">
            <label>Nombre</label> <br>
            <input type="text" name="nombreNew" value='<?=$tupla->nombre?>'> <br>
            <label>Fecha nacimientos</label> <br>
            <input type="date" name="dateNew" value='<?=$tupla->fecha?>'> <br>
            <label>Administrador</label> <br>
            <select name="administrador">
                <option value="no">No</option>
                <option value="si">Si</option>
            </select> <br>
            <label>Estado</label> <br>
            <select name="estado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select> <br> <br>
            <button type="submit" class="btn btn-primary" name="actualizarUsuario" value='<?=$tupla->id?>'>Actualizar</button>
        </form>
    </main>
                        
<?php
    }

    if(isset($_POST['actualizarUsuario'])) {
?>
        <main id="listadoUsuarios">
            <div id="errorCarrito">
                <h3>Usuario actualizado</h3>
            </div>
        </main>
<?php
    }
?>
    

    <footer></footer>  
</body>
</html>