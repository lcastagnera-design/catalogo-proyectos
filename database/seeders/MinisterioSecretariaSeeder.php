<?php

namespace Database\Seeders;

use App\Models\MinisterioSecretaria;
use Illuminate\Database\Seeder;

class MinisterioSecretariaSeeder extends Seeder
{
    /**
     * Datos jerárquicos (nivel2, nivel3, nivel4) extraídos del Excel.
     *
     * @var list<array{nivel2: string, nivel3: string, nivel4: ?string}>
     */
    public const DATA = [
        ['nivel2' => 'Jefatura de Gobierno', 'nivel3' => '-', 'nivel4' => null],
        ['nivel2' => 'Vicejefatura de Gobierno', 'nivel3' => '-', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Jefatura de Gabinete de Ministros', 'nivel4' => 'Secretaría de Comunicación'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Jefatura de Gabinete de Ministros', 'nivel4' => 'Secretaría de Innovación y Transformación Digital'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Jefatura de Gabinete de Ministros', 'nivel4' => 'Secretaría de Asuntos Estratégicos'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Jefatura de Gabinete de Ministros', 'nivel4' => 'Secretaría de Gobierno y Vínculo Ciudadano'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Hacienda y Finanzas', 'nivel4' => 'Secretaría de Gestión Institucional y Vinculación Estratégica'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Salud', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Educación', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Desarrollo Humano y Hábitat', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Justicia y Seguridad', 'nivel4' => 'Secretaría de Seguridad'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Infraestructura', 'nivel4' => 'Secretaría de Transporte'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Infraestructura', 'nivel4' => 'Secretaría de Obras'],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Desarrollo Económico', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Espacio Público e Higiene Urbana', 'nivel4' => null],
        ['nivel2' => 'Ministerios', 'nivel3' => 'Ministerio de Cultura', 'nivel4' => null],
    ];

    public function run(): void
    {
        foreach (self::DATA as $row) {
            MinisterioSecretaria::create($row);
        }
    }
}
