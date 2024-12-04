<?php
require_once 'vehiculo.php';
require_once 'coche.php';
require_once 'moto.php';
require_once 'camion.php';
require_once 'bicicleta.php';
require_once 'vehiculoElectrico.php';
require_once 'tesla.php';
require_once 'concesionario.php';

// Crear instancias de los vehículos
$coche = new Coche("Toyota", "Corolla", "Blanco", 4);
$moto = new Moto("Yamaha", "MT-07", "Negro", 689);
$camion = new Camion("Volvo", "FH16", "Azul", 25.0);
$tesla = new Tesla("Tesla", "Model S", "Rojo", 85);
$bicicleta = new Bicicleta("Giant", "Escape 3", "Verde");

// Mostrar información de los vehículos
$concesionario = new Concesionario();
echo "<h2>Información del Coche</h2>";
$concesionario->mostrarVehiculo($coche);

echo "<h2>Información de la Moto</h2>";
$concesionario->mostrarVehiculo($moto);

echo "<h2>Información del Camión</h2>";
$concesionario->mostrarVehiculo($camion);

echo "<h2>Información del Tesla</h2>";
$concesionario->mostrarVehiculo($tesla);

echo "<h2>Información de la Bicicleta</h2>";
echo $bicicleta->obtenerInformacion();

// Probar métodos específicos
echo "<h3>Métodos Específicos</h3>";
$coche->mover();
echo "<br>";
$coche->detener();

echo "<br>";
$moto->mover();
echo "<br>";
$moto->detener();

echo "<br>";
$camion->mover();
echo "<br>";
$camion->detener();

echo "<br>";
$tesla->mover();
echo "<br>";
$tesla->detener();
echo "<br>";
echo $tesla->estadoBateria();
echo "<br>";
$tesla->cargarBateria();
echo "<br>";
echo $tesla->estadoBateria();

echo "<br>";
$bicicleta->pedalear();
?>
