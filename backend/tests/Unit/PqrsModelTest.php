<?php

namespace Tests\Unit;

use App\Models\Pqrs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PqrsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pqrs_has_responder_relationship(): void
    {
        $user = User::factory()->create();
        
        $pqrs = Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test message',
            'estado' => 'resuelto',
            'respuesta' => 'Response',
            'respondido_at' => now(),
            'respondido_por' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $pqrs->responder);
        $this->assertEquals($user->id, $pqrs->responder->id);
    }

    public function test_new_scope_only_returns_new_pqrs(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'peticion',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'resuelto',
        ]);

        $new = Pqrs::new()->get();
        
        $this->assertCount(1, $new);
        $this->assertEquals('nuevo', $new->first()->estado);
    }

    public function test_in_process_scope_only_returns_in_process_pqrs(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'en_proceso',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'peticion',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        $inProcess = Pqrs::inProcess()->get();
        
        $this->assertCount(1, $inProcess);
        $this->assertEquals('en_proceso', $inProcess->first()->estado);
    }

    public function test_of_type_scope_filters_by_type(): void
    {
        Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        Pqrs::create([
            'folio' => 'PQRS-2026-000002',
            'tipo' => 'queja',
            'nombre' => 'User 2',
            'email' => 'user2@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        $peticiones = Pqrs::ofType('peticion')->get();
        
        $this->assertCount(1, $peticiones);
        $this->assertEquals('peticion', $peticiones->first()->tipo);
    }

    public function test_generate_folio_creates_unique_folio(): void
    {
        $folio = Pqrs::generateFolio();
        
        $this->assertStringStartsWith('PQRS-', $folio);
        $this->assertMatchesRegularExpression('/^PQRS-\d{4}-\d{6}$/', $folio);
    }

    public function test_folios_are_sequential(): void
    {
        $folio1 = Pqrs::generateFolio();
        
        Pqrs::create([
            'folio' => $folio1,
            'tipo' => 'peticion',
            'nombre' => 'User 1',
            'email' => 'user1@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Message',
            'estado' => 'nuevo',
        ]);

        $folio2 = Pqrs::generateFolio();
        
        // Extract numbers from folios
        preg_match('/PQRS-\d{4}-(\d{6})/', $folio1, $matches1);
        preg_match('/PQRS-\d{4}-(\d{6})/', $folio2, $matches2);
        
        $this->assertEquals((int)$matches1[1] + 1, (int)$matches2[1]);
    }

    public function test_respondido_at_is_cast_to_datetime(): void
    {
        $pqrs = Pqrs::create([
            'folio' => 'PQRS-2026-000001',
            'tipo' => 'peticion',
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'asunto' => 'Test',
            'mensaje' => 'Test message',
            'estado' => 'resuelto',
            'respondido_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $pqrs->respondido_at);
    }
}
