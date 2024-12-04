<?php
require_once 'vehiculo.php';

class Coche extends Vehiculo {
    private int $numeroPuertas;

    public function __construct(string $marca, string $modelo, string $color, int $numeroPuertas) {
        parent::__construct($marca, $modelo, $color);
        $this->numeroPuertas = $numeroPuertas;
    }

    public function setNumeroPuertas(int $numeroPuertas): self {
        $this->numeroPuertas = $numeroPuertas;
        return $this;
    }

    public function setColor(string $color): self {
        $this->color = $color;
        return $this;
    }

    public function mover() {
        echo "El coche está en movimiento.";
    }

    public function detener() {
        echo "El coche se ha detenido.";
    }

    public function obtenerInformacion(): string {
        return parent::obtenerInformacion() . ", Número de Puertas: {$this->numeroPuertas}";
    }
}
?>
