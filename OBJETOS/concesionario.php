<?php
require_once 'vehiculo.php';

class Concesionario {
    public function mostrarVehiculo(Vehiculo $vehiculo) {
        echo $vehiculo->obtenerInformacion();
    }
}
?>
