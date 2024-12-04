<?php
require_once 'vehiculo.php';

class Camion extends Vehiculo {
    private float $capacidadCarga;

    public function __construct(string $marca, string $modelo, string $color, float $capacidadCarga) {
        parent::__construct($marca, $modelo, $color);
        $this->capacidadCarga = $capacidadCarga;
    }

    public function getCapacidadCarga(): float {
        return $this->capacidadCarga;
    }

    public function setCapacidadCarga(float $capacidadCarga): void {
        $this->capacidadCarga = $capacidadCarga;
    }

    public function mover() {
        echo "El camión está en movimiento.";
    }

    public function detener() {
        echo "El camión se ha detenido.";
    }

    public function obtenerInformacion(): string {
        return parent::obtenerInformacion() . ", Capacidad de Carga: {$this->capacidadCarga} toneladas";
    }
}
?>
