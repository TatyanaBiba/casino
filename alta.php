<?php
session_start();
require_once("classes/Socis.php");  // Incluyo el fichero Socis.php de la carpeta classes

if(isset($_POST["alta"])){
    if(!empty($_POST["nick"]) && !empty($_POST["pass"]) && !empty($_POST["passr"])){
        if($_POST["pass"]==$_POST["passr"]){
            $nick=htmlspecialchars($_POST["nick"]);
            $pass=htmlspecialchars($_POST["pass"]);

            $elssocis=new Socis(); // Creo un nuevo objeto de tipo Socis ( __construct() conectará automáticamente con la BD )
            $socio=$elssocis->retornaSocio($nick);  // Llamo al método que devuelve un socio de la BD a partir de su id y lo guardo en $socio
    
            if($socio==false){
                $passc=password_hash($pass, PASSWORD_DEFAULT);
                $resultado=$elssocis->creaSocio($nick,$passc,1,50);
                if($resultado==true){
                    $_SESSION["nick"]=$nick;  // Guardo el nick del usuario en el campo ["nick"] del array $_SESSION

                    header("Location: ruleta.php");  // Redirijo al visitante a la página ruleta.php
                    exit;
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/alt.css"> 
	<link rel="icon" href="imagenes/favicon.ico" type="image/x-icon">
    <title>alta</title>
</head>
<body>  
    <main class="reg flex-container">
    	<form class="fregistr flex2" id="login" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    	<h2>REGISTRARSE</h2>
        <input class="user" id="user" type="text" name="nick" placeholder="nombre">
        <input class="password" id="password" type="password" name="pass" placeholder="password">   
        <input class="password" id="password" type="password" name="passr" placeholder="confirma password">
        <input class="saldo" type="number" name="saldo" placeholder="saldo" required>   
        <input type="submit" name="alta"  value="enviar"><br>
        <a href="login.php">LOGIN</a>
    </form>
    </main>
</body>
</html>