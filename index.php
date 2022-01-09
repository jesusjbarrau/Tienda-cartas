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

//CONTROL DE ACCESO:
session_start();

if(!empty($_SESSION['usuario'])){
    header('Location: listado.php');
}

//FUNCIONES:
function filtrado($datos){
    $datos = trim($datos); // Elimina espacios antes y después de los datos
    $datos = stripslashes($datos); // Elimina backslashes \
    $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
    return $datos;
}

//VARIABLES:
$errores['email'] = ' ';
$errores['contraseña'] = ' ';
$errores['nombre'] = ' ';
$errores['apellidos'] = ' ';
$errores['fecha'] = ' ';
$errores['correo'] = ' ';
$error = false;




if(isset($_POST['submit'])){

    if(empty($_POST['email']) || (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))){
        $errores['email'] = 'Se necesita un email';
        $error = true;
    } else $email = filtrado($_POST['email']);

    if(empty($_POST['contraseña'])){
        $errores['contraseña'] = 'Se necesita una contraseña';
        $error = true;
    } else $contraseña = filtrado($_POST['contraseña']);

    if(!$error){
        $consulta = $dwes->query("SELECT id, contrasenia FROM usuario WHERE nombreUsuario='$email' AND estado='activo'");
        if(($tupla = $consulta->fetch(PDO::FETCH_OBJ)) != null) {
            if(password_verify($contraseña, $tupla->contrasenia)){
                $_SESSION['usuario'] = $email;
                header('Location: listado.php');
            } else {
                $errores['contraseña'] = 'La contraseña es incorrecta';
            }
        } else $errores['email'] = 'No existe este usuario';
        
    }
}

if(isset($_POST['registrar'])){

    if(empty($_POST['nombre'])){
        $errores['nombre'] = 'Se requiere de un nombre';
        $error = true;
    } else $nombre = filtrado($_POST['nombre']);

    if(empty($_POST['apellidos'])){
        $errores['apellidos'] = 'Se requiere de los apellidos';
        $error = true;
    } else $apellidos = filtrado($_POST['apellidos']);

    if(empty($_POST['fecha'])){
        $errores['fecha'] = "Se requiere de una fecha";
        $error = true;
    } else $fecha = filtrado($_POST['fecha']);

    if(empty($_POST['correo'])){
        $errores['correo'] = "Se requiere de un correo";
        $error = true;
    } else $email = filtrado($_POST['correo']);

    if(empty($_POST['contraseña'])){
        $errores['contraseña'] = 'Se requiere de una contraseña';
        $error = true;
    } else $contraseña = filtrado($_POST['contraseña']);

    if(!$error){

        $consulta = $dwes->query("SELECT id FROM usuario WHERE nombreUsuario='$email'");
        if(($tupla = $consulta->fetch(PDO::FETCH_OBJ)) != null){
            $errores['correo'] = 'Ya existe una cuenta con este correo';
        } else {
            $contraseña = password_hash($contraseña,PASSWORD_DEFAULT);
            $consulta = $dwes->exec("INSERT INTO usuario (nombre,apellidos,nombreUsuario,fecha,contrasenia)
            VALUES ('$nombre','$apellidos','$email','$fecha','$contraseña')");
            $_SESSION['usuario'] = $email;
            header('Location: listado.php');
        }

    }

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
<body id="cuerpoInicio">

    <header id="inicioCabeza">

        <h1>CardAttack</h1>

    </header>

<?php

if(isset($_POST['irRegistro']) || isset($_POST['registrar'])){

?>

    <form class="inicio" action="" method="post">

        <label>
            <h2>Panel de registro</h2>
        </label>

        <label>Nombre</label> <br>
        <input type="text" name="nombre"> <br>
        <span style="color:red"><?=$errores['nombre']?></span>
        <br>
        <label>Apellidos</label> <br>
        <input type="text" name="apellidos"> <br>
        <span style="color:red"><?=$errores['apellidos']?></span>
        <br>
        <label>Fecha nacimiento</label> <br>
        <input type="date" name="fecha"> <br>
        <span style="color:red"><?=$errores['fecha']?></span>
        <br>
        <label>Email</label> <br>
        <input type="email" name="correo"> <br>
        <span style="color:red"><?=$errores['correo']?></span>
        <br>
        <label>Contraseña</label> <br>
        <input type="password" name="contraseña"> <br>
        <span style="color:red"><?=$errores['contraseña']?></span>
        <br> <br>

        <input type="submit" name="registrar" class="btn btn-primary btn-block mb-4" value="Registrarse">
        <input type="submit" id="botonRegistro" name="irInicio" class="btn btn-secondary btn-block mb-4" value="¿Ya tienes una cuenta? Incia sesión">

    </form>

<?php

} else {

?>

    <form class="inicio" action="" method="post">

        <label>
            <h2>Inicio de sesión</h2>
        </label>

        <br>
        
        <label>Email</label> <br>
        <input type="email" name='email'> <br>
        <span  style="color:red"><?=$errores['email']?></span>
        <br>

        <label>Contraseña</label> <br>
        <input type="password" name='contraseña'> <br>
        <span style="color:red"><?=$errores['contraseña']?></span>
        <br>

        <br>
    
        <input type="submit" name="submit" class="btn btn-primary btn-block mb-4" value="Iniciar sesión">
        <input type="submit" id="botonRegistro" name="irRegistro" class="btn btn-secondary btn-block mb-4" value="Registrar nueva cuenta">

        

    </form>

<?php

}

?>

</body>
</html>