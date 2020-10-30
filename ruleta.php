<?php
session_start();  // Hay que hacer session_start() siempre que se va a usar el array $_SESSION
require_once("classes/Socis.php");  // Incluyo el fichero Socis.php de la carpeta classes

if(isset($_POST["logout"])){ // Si se ha pulsado el botón logout
    $_SESSION=[];  // Vacio el array $_SESSION (borra todo lo que haya dentro)
    session_destroy();  // Cierro la sesión (borra del servidor todas los ficheros relacionados con la sesión)
}
if(!isset($_SESSION["nick"])){  // Si no existe el campo nick en $_SESSION, significa que la sesión no está iniciada
    header("Location: index.php");  // Redirige a login.php
    exit;
}

$elssocis=new Socis(); // Creo un nuevo objeto de tipo Socis ( __construct() conectará automáticamente con la BD )
$socio=$elssocis->retornaSocio($_SESSION["nick"]);  // Llamo al método que devuelve un socio de la BD a partir de su id y lo guardo en $socio

if(isset($_POST["tirar"]) && $socio["saldo"]>0){  // Si se ha pulsado el botón tirar y el usuario tiene saldo
    $tiro=rand(0,36);  // Genero un numero aleatorio entre 0 y 36

    if( !empty($_POST["tipo"]) && !empty($_POST["apuesta"])){  

        $tipo=htmlspecialchars($_POST["tipo"]);
        $apuesta=htmlspecialchars($_POST["apuesta"]);
        $newsaldo=$socio["saldo"];

        if($tipo=="par"){  // Si en el formulario ha elegido par
            if( ($tiro%2==0) && ($tiro!=0) ){  // Si en la ruleta ha salido un numero divisible por 2 (excepto el 0)
                $newsaldo=$socio["saldo"]+$apuesta;  // Añade el dinero apostado al saldo que tenia antes
            }else{
                $newsaldo=$socio["saldo"]-$apuesta;  // Resta el dinero apostado apuesta del saldo que tenia antes
            }
        }elseif($tipo=="impar"){ // Si en el formulario ha elegido impar
            if( ($tiro%2==0) && ($tiro!=0) ){
                $newsaldo=$socio["saldo"]-$apuesta;
            }else{
                $newsaldo=$socio["saldo"]+$apuesta;
            }
        }elseif($tipo=="rojo"){ // Si en el formulario ha elegido rojo 
            if( in_array($tiro, [1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36]) ){  // Si en la ruleta ha salido un numero que es de los rojos 
                $newsaldo=$socio["saldo"]+$apuesta;
            }else{
                $newsaldo=$socio["saldo"]-$apuesta;
            }
        }elseif($tipo=="negro"){  // Si en el formulario ha elegido negro
            if( in_array($tiro, [2,4,6,8,10,11,13,15,17,20,22,24,26,28,29,31,33,35]) ){  // Si en la ruleta ha salido un numero que es de los negros 
                $newsaldo=$socio["saldo"]+$apuesta;
            }else{
                $newsaldo=$socio["saldo"]-$apuesta;
            }
        }

        if($newsaldo>$socio["saldo"]){  // Si ha ganado dinero, en el HTML imprimiré GANAS. si no, PIERDES
            $ganapierde="GANAS";
        }else{
            $ganapierde="PIERDES";
        }
        
        $socio["saldo"]=$newsaldo;  // Actualiza el saldo del usuario en la variable que se imprimirá en el html
        $elssocis->updateSaldo($_SESSION["nick"], $newsaldo);  // Actualiza el saldo del usuario en la BD
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego</title>
	<link rel="stylesheet" type="text/css" href="css/jueg.css"> 
	<link rel="icon" href="imagenes/favicon.ico" type="image/x-icon">
</head>
<body>
<main class="flex-container">
		<div class="flex1"><a href="/"><img alt="casino" src="imagenes/h1.png" height="35px;"></a></div>
			<div class="ul flex-container">
			<div class="li flex3"><img alt="nick" src="imagenes/nick.png" height="40px;"><?php echo "{$socio["nick"]}";?></div>
			<div class="li flex3"><img alt="saldo" src="imagenes/saldo.png" height="40px;"><?php echo "{$socio["saldo"]}€";?></div>
			<div class="li flex3"><img alt="privilegio" src="imagenes/privilegio.png" height="40px;"><?php echo "{$socio["privilegio"]}";?></div>
			<div class="li flex3"><img alt="rang" src="imagenes/rang.png" height="40px;"><?php echo "{$socio["rang"]}";?></div>
			</div> 
    	<form class="lout flex1" action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">
            <input class="logout" name="logout" type="submit" name="logout" value="logout">
        </form>
        
         <div class="juego flex-container">
            	<div class="jugo flex1">
            	<figure>
        		<img src="imagenes/castabl.png" alt="casino">
        		</figure>
            	</div>
         </div>
            
            
    <form class="fr flex-container" action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">    
        <div class="flex1">
        	<div class="cont" style="text-align: center; font-size: 1.8em;">
            	<h3>  
				<?php if(isset($tiro)){ // Si existe la variable $tiro, la imprimo 
                    // Si el numero que ha salido en la ruleta es rojo, pongo el texto del parrafo de color rojo
                    if(in_array($tiro, [1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36])){  
                        echo '<p style="color:red">';
                    }else{ echo "<p>"; }
                    echo $tiro."</p>";
                } ?>
                </h3>
        	</div>
        </div>
        <span>
                <?php if(isset($ganapierde)){ // Si existe la variable $ganapierde, la imprimo 
                    echo "<p>".$ganapierde."</p>"; 
                } ?>
        </span>
        <div class="sel flex1">
        <select name="tipo">
            <option value="par">PAR</option>
            <option value="impar">IMPAR</option>
            <option value="rojo">ROJO</option>
            <option value="negro">NEGRO</option>
        </select>
        </div>
        <div class="sel flex1">
        <select name="apuesta">
            <option value="5">5€</option>
            <option value="10">10€</option>
        </select>
        </div>

        <input type="submit" name="tirar" value="tirar">
    </form>


</main>
</body>
</html>