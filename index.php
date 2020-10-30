<?php
session_start();  // Hay que hacer session_start() siempre que se va a usar el array $_SESSION
require_once("classes/Socis.php");  // Incluyo el fichero Socis.php de la carpeta classes

if(isset($_POST["enviar"])){  // Si se ha pulsado el botón del formulario
    if( !empty($_POST["nick"]) && !empty($_POST["pass"]) ){  // Si el nick y la contraseña contienen algo

        $nick=htmlspecialchars($_POST["nick"]);  // Quito caracteres html que podria contener el nick que llega del formulario y lo guardo en $nick (por seguridad)
        $pass=htmlspecialchars($_POST["pass"]); // Quito caracteres html que podria contener el password que llega del formulario y lo guardo en $pass (por seguridad)

        $elssocis=new Socis(); // Creo un nuevo objeto de tipo Socis ( __construct() conectará automáticamente con la BD )
        $socio=$elssocis->retornaSocio($nick);  // Llamo al método que devuelve un socio de la BD a partir de su id y lo guardo en $socio

        if( isset($socio["pass"]) ){ // Si existe ese socio en la BD, existe el campo contraseña en $socio
            if(password_verify($pass, $socio["pass"])){  // Si la contraseña que hay en la BD para ese socio coincide con la que se ha recibido en el formulario
                $_SESSION["nick"]=$nick;  // Guardo el nick del usuario en el campo ["nick"] del array $_SESSION
                header("Location: ruleta.php");  // Redirijo al visitante a la página ruleta.php
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
		<meta charset="UTF-8">
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="css/style.css"> 
		<link rel="icon" href="imagenes/favicon.ico" type="image/x-icon">
	</head>
<body>
	<main class="flex-container">
	    	<figure class="h1 flex1">
    			<img src="imagenes/h1.png" alt="casino" style="width: 350px;">
    		</figure>
    		<h2>Elige nombre de jugador</h2>
    <form class="flogin flex2" id="login" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <input class="user" id="user" type="text" name="nick" placeholder="Nick de jugador" />
        <input  class="password" id="password" type="password" name="pass" placeholder="Clave de jugador" required/>  
        <input type="submit" name="enviar" value="enviar"><br>
        <a href="alta.php">REGISTRARSE</a>
    </form>
    </main>
</body>
</html>