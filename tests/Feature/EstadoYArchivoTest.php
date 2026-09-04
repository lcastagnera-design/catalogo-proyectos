<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadoYArchivoTest extends TestCase
{
    use RefreshDatabase;

    private function crearProyecto(string $estado = 'planificacion'): Proyecto
    {
        return Proyecto::create([
            'nombre_proyecto' => 'Proyecto Prueba '.uniqid(),
            'nombre_proyecto_marca' => 'Marca '.uniqid(),
            'estado' => $estado,
        ]);
    }

    public function test_destroy_archiva_en_vez_de_eliminar(): void
    {
        $proyecto = $this->crearProyecto('ejecucion');
        $pid = $proyecto->id;

        $this->withSession(['logged_in' => true])
            ->delete(route('proyectos.destroy', $proyecto))
            ->assertRedirect(route('proyectos.index'));

        $this->assertDatabaseHas('proyectos', ['id' => $pid, 'estado' => Proyecto::ESTADO_ARCHIVADO]);
        $this->assertNull(Proyecto::NoArchivados()->find($pid));
        $this->assertNotNull(Proyecto::IncluyendoArchivados(true)->find($pid));
    }

    public function test_cambiar_estado_desde_listado(): void
    {
        $proyecto = $this->crearProyecto('planificacion');

        $this->withSession(['logged_in' => true])
            ->post(route('proyectos.estado', $proyecto), ['estado' => 'finalizado'])
            ->assertRedirect();

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'estado' => 'finalizado']);
    }

    public function test_cambiar_estado_rechaza_valor_invalido(): void
    {
        $proyecto = $this->crearProyecto('planificacion');

        $this->withSession(['logged_in' => true])
            ->post(route('proyectos.estado', $proyecto), ['estado' => 'no-existe'])
            ->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'estado' => 'planificacion']);
    }
}
