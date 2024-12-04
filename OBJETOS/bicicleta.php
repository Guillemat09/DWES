<?php
final class Bicicleta {
    private string $marca;
    private string $modelo;
    private string $color;

    public function __construct(string $marca, string $modelo, string $color = "Negro") {
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->color = $color;
    }

    public function pedalear() {
        echo "La bicicleta está en movimiento.";
    }

    public function obtenerInformacion(): string {
        return "Marca: {$this->marca}, Modelo: {$this->modelo}, Color: {$this->color}";
    }
}
?>
