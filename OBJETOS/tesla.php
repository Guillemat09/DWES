<?php
require_once 'vehiculo.php';
require_once 'vehiculoElectrico.php';

class Tesla extends Vehiculo implements VehiculoElectrico {
    private int $nivelBateria;

    public function __construct(string $marca, string $modelo, string $color, int $nivelBateria = 100) {
        parent::__construct($marca, $modelo, $color);
        $this->nivelBateria = $nivelBateria;
    }

    public function cargarBateria() {
        $this->nivelBateria = 100;
        echo "La batería está completamente cargada.";
    }
     function estadoBateria(): string {
        return "Nivel de batería: {$this->nivelBateria}%";
    }

    public function mover() {
        echo "El Tesla está en movimiento.";
    }

    public function detener() {
        echo "El Tesla se ha detenido.";
    }

    public function obtenerInformacion(): string {
        return parent::obtenerInformacion() . ", Nivel de Batería: {$this->nivelBateria}%";
    }
}
?>
