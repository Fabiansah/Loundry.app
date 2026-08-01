<?php

namespace Tests\Feature;

use App\Models\BukuKas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClosingKasirTest extends TestCase
{
    use RefreshDatabase;

    public function test_closing_process_redirects_to_print_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        BukuKas::create([
            'user_id' => $user->id,
            'modal_awal' => 100000,
            'tanggal' => now()->toDateString(),
            'status' => 'buka',
        ]);

        $response = $this->actingAs($user)->post('/tutup-kasir', [
            'uang_fisik_laci' => 120000,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('/tutup-kasir/print/', $response->headers->get('Location'));
    }
}
