<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El widget de chat debe ser alcanzable por invitados: se monta en la página
 * pública de bienvenida y en el layout de invitado (login/registro), no solo en
 * el layout autenticado.
 */
class ChatWidgetVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_renders_on_public_welcome_page_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        // Marcadores del partial del widget (burbuja flotante + inicializador Alpine).
        $response->assertSee('mentea-chat-widget', false);
        $response->assertSee('menteaChat()', false);
        // El widget necesita el token CSRF para postear.
        $response->assertSee('name="csrf-token"', false);
    }

    public function test_widget_renders_on_guest_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('mentea-chat-widget', false);
        $response->assertSee('menteaChat()', false);
    }
}
