<?php

namespace Database\Factories;

use App\Models\Proyecto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proyecto>
 */
class ProyectoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('es_AR');

        return [
            'nombre' => $faker->sentence(4, false),
            'descripcion' => $faker->paragraph(3),
            'organismo' => $faker->randomElement([
                'Ministerio de Modernización',
                'Ministerio de Salud',
                'Ministerio de Educación',
                'Ministerio de Ambiente',
                'Ministerio de Transporte',
                'Ministerio de Desarrollo Social',
                'Secretaría de Innovación',
                'Municipalidad General',
            ]),
            'estado' => $faker->randomElement(['activo', 'finalizado', 'pausado']),
            'categoria' => $faker->randomElement([
                'Infraestructura',
                'Tecnología',
                'Salud',
                'Educación',
                'Medio Ambiente',
                'Transporte',
                'Social',
                'Cultura',
            ]),
            'fecha_inicio' => $faker->optional(0.8)->dateTimeBetween('-2 years', '-3 months'),
            'fecha_fin' => null,
            'responsable' => $faker->name(),
            'presupuesto' => $faker->optional(0.7)->randomFloat(2, 500000, 50000000),
            'avance' => $faker->numberBetween(0, 100),
            'imagen_url' => $faker->optional(0.4)->imageUrl(640, 480, 'technics'),
        ];
    }

    public function activo(): static
    {
        return $this->state(fn () => ['estado' => 'activo']);
    }

    public function finalizado(): static
    {
        return $this->state(fn () => ['estado' => 'finalizado']);
    }

    public function pausado(): static
    {
        return $this->state(fn () => ['estado' => 'pausado']);
    }
}
