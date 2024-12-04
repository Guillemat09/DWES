<?php
abstract class Vehiculo {
    protected string $marca;
    protected string $modelo;
    protected string $color;

    public function __construct(string $marca, string $modelo, string $color = "Negro") {
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->color = $color;
    }

    abstract public function mover();
    abstract public function detener();

    public function obtenerInformacion(): string {
        return "Marca: {$this->marca}, Modelo: {$this->modelo}, Color: {$this->color}";
    }

    public function __toString() {
        return $this->obtenerInformacion();
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
}
?>
