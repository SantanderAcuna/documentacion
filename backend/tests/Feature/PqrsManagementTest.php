<?php

namespace Tests\Feature;

use App\Models\Pqrs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PqrsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $pqrsAttendant;
    protected User $citizen;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
        
        $this->pqrsAttendant = User::factory()->create();
        $this->pqrsAttendant->assignRole('atencion-pqrs');
        
        $this->citizen = User::factory()->create();
        $this->citizen->assignRole('ciudadano');
    }

    public function test_anyone_can_create_pqrs(): void
    {
        $response = $this->postJson('/api/v1/pqrs', [
            'tipo' => 'peticion',
            'nombre' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'telefono' => '3001234567',
            'documento' => '12345678',
            'asunto' => 'Solicitud de información',
            'mensaje' => 'Requiero información sobre los trámites disponibles.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'pqrs' => ['id', 'folio', 'tipo', 'estado'],
                'folio',
            ]);

        $this->assertDatabaseHas('pqrs', [
            'email' => 'juan@example.com',
            'estado' => 'nuevo',
        ]);
    }

    public function test_pqrs_folio_is_automatically_generated(): void
    {
        $response = $this->postJson('/api/v1/pqrs', [
            'tipo' => 'queja',
            'nombre' => 'María García',
            'email' => 'maria@example.com',
            'asunto' => 'Queja sobre servicio',
            'mensaje' => 'El servicio fue deficiente.',
        ]);

        $response->assertStatus(201);
        
        $folio = $response->json('folio');
        $this->assertStringStartsWith('PQRS-', $folio);
        $this->assertMatchesRegularExpression('/^PQRS-\d{4}-\d{6}$/', $folio);
    }

    public function test_anyone_can_track_pqrs_by_folio(): void
    {
        $pqrs = Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test message',
            'estado' => 'nuevo',
        ]);

        $response = $this->getJson('/api/v1/pqrs/PQRS-2026-000001');

        $response->assertStatus(200)
            ->assertJson([
                'folio' => 'PQRS-2026-000001',
                'estado' => 'nuevo',
            ]);
    }

    public function test_pqrs_attendant_can_list_all_pqrs(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Request',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'queja',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Complaint',
            'mensaje' => 'Message',
            'estado' => 'en_proceso',
        ]);

        $response = $this->actingAs($this->pqrsAttendant, 'sanctum')
            ->getJson('/api/v1/pqrs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'folio', 'tipo', 'estado'],
                ],
            ]);
    }

    public function test_citizen_cannot_list_all_pqrs(): void
    {
        $response = $this->actingAs($this->citizen, 'sanctum')
            ->getJson('/api/v1/pqrs');

        $response->assertStatus(403);
    }

    public function test_can_filter_pqrs_by_type(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Request',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'queja',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Complaint',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        $response = $this->actingAs($this->pqrsAttendant, 'sanctum')
            ->getJson('/api/v1/pqrs?tipo=queja');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('queja', $data[0]['tipo']);
    }

    public function test_can_filter_pqrs_by_status(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Request',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'peticion',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Another Request',
            'mensaje' => 'Message',
            'estado' => 'resuelto',
        ]);

        $response = $this->actingAs($this->pqrsAttendant, 'sanctum')
            ->getJson('/api/v1/pqrs?estado=resuelto');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('resuelto', $data[0]['estado']);
    }

    public function test_pqrs_attendant_can_update_status(): void
    {
        $pqrs = Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test message',
            'estado' => 'nuevo',
        ]);

        $response = $this->actingAs($this->pqrsAttendant, 'sanctum')
            ->putJson("/api/v1/pqrs/{$pqrs->id}", [
                'estado' => 'en_proceso',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'estado' => 'en_proceso',
            ]);
    }

    public function test_pqrs_attendant_can_respond_to_pqrs(): void
    {
        $pqrs = Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test message',
            'estado' => 'nuevo',
        ]);

        $response = $this->actingAs($this->pqrsAttendant, 'sanctum')
            ->postJson("/api/v1/pqrs/{$pqrs->id}/respond", [
                'respuesta' => 'Estimado usuario, su solicitud ha sido atendida.',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'pqrs' => ['respuesta', 'respondido_at', 'respondido_por'],
            ]);

        $pqrs->refresh();
        $this->assertEquals('resuelto', $pqrs->estado);
        $this->assertEquals($this->pqrsAttendant->id, $pqrs->respondido_por);
        $this->assertNotNull($pqrs->respondido_at);
    }

    public function test_pqrs_validation_requires_all_fields(): void
    {
        $response = $this->postJson('/api/v1/pqrs', [
            'tipo' => 'invalid-type',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'email', 'asunto', 'mensaje']);
    }

    public function test_pqrs_tipo_must_be_valid(): void
    {
        $response = $this->postJson('/api/v1/pqrs', [
            'tipo' => 'invalid',
            'nombre' => 'Test',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo']);
    }
}
