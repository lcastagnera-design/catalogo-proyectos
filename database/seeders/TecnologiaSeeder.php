<?php

namespace Database\Seeders;

use App\Models\Tecnologia;
use Illuminate\Database\Seeder;

class TecnologiaSeeder extends Seeder
{
    /**
     * Lista plana de tecnologías con su categoría, extraída del Excel.
     *
     * @var list<array{categoria: string, nombre: string}>
     */
    public const DATA = [
        ['categoria' => 'BackEnd', 'nombre' => 'NestJS'],
        ['categoria' => 'BackEnd', 'nombre' => 'FastAPI'],
        ['categoria' => 'BackEnd', 'nombre' => 'Laravel'],
        ['categoria' => 'BackEnd', 'nombre' => 'Django'],
        ['categoria' => 'BackEnd', 'nombre' => 'Livewire'],
        ['categoria' => 'BackEnd', 'nombre' => 'Express.js'],
        ['categoria' => 'BackEnd', 'nombre' => 'Fastify.js'],
        ['categoria' => 'BackEnd', 'nombre' => 'Spring Boot'],
        ['categoria' => 'BackEnd', 'nombre' => 'Kotlin'],
        ['categoria' => 'BackEnd', 'nombre' => '.NET'],
        ['categoria' => 'Frontend', 'nombre' => 'Angular'],
        ['categoria' => 'Frontend', 'nombre' => 'Nest.js'],
        ['categoria' => 'Frontend', 'nombre' => 'Ionic'],
        ['categoria' => 'Frontend', 'nombre' => 'Capacitor'],
        ['categoria' => 'Base de datos', 'nombre' => 'Oracle'],
        ['categoria' => 'Base de datos', 'nombre' => 'PostgreSQL'],
        ['categoria' => 'Base de datos', 'nombre' => 'MariaDB'],
        ['categoria' => 'Base de datos', 'nombre' => 'MongoDB'],
        ['categoria' => 'Base de datos', 'nombre' => 'Redis'],
    ];

    public function run(): void
    {
        foreach (self::DATA as $row) {
            Tecnologia::create($row);
        }
    }
}
