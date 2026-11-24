<?php

namespace Tests\Feature;

use App\Models\Catatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanTest extends TestCase
{
    use RefreshDatabase;

    public function test_catatan_can_be_viewed_edited_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());

        $catatan = Catatan::create([
            'judul' => 'Catatan lama',
            'isi' => 'Isi catatan lama',
        ]);

        $this->get(route('catatan.index'))
            ->assertOk()
            ->assertSee(route('catatan.show', $catatan))
            ->assertSee(route('catatan.edit', $catatan))
            ->assertSee(route('catatan.destroy', $catatan));

        $this->get(route('catatan.show', $catatan))
            ->assertOk()
            ->assertSee('Catatan lama')
            ->assertSee('Isi catatan lama');

        $this->get(route('catatan.edit', $catatan))
            ->assertOk()
            ->assertSee('value="Catatan lama"', false)
            ->assertSee('Isi catatan lama');

        $this->put(route('catatan.update', $catatan), [
            'judul' => 'Catatan baru',
            'isi' => 'Isi catatan baru',
        ])->assertRedirect(route('catatan.show', $catatan));

        $this->assertDatabaseHas('catatan', [
            'id' => $catatan->id,
            'judul' => 'Catatan baru',
            'isi' => 'Isi catatan baru',
        ]);

        $this->delete(route('catatan.destroy', $catatan))
            ->assertRedirect(route('catatan.index'));

        $this->assertDatabaseMissing('catatan', ['id' => $catatan->id]);
    }

    public function test_catatan_requires_a_title_and_content(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/catatan', [
            'judul' => '',
            'isi' => '',
        ]);

        $response->assertSessionHasErrors(['judul', 'isi']);
        $this->assertDatabaseCount('catatan', 0);
    }
}
