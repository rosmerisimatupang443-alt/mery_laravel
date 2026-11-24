<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_and_registration_forms(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke Catatan');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat Akun');
    }

    public function test_guest_is_redirected_to_login_from_catatan(): void
    {
        $this->get(route('catatan.index'))
            ->assertRedirect(route('login'));

        $this->post(route('catatan.store'), [
            'judul' => 'Catatan rahasia',
            'isi' => 'Tidak boleh disimpan oleh tamu',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('catatan', 0);
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('kata-sandi-rahasia'),
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'user@example.com',
                'password' => 'kata-sandi-rahasia',
            ])
            ->assertRedirect(route('catatan.index'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_do_not_log_user_in(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('kata-sandi-rahasia'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'user@example.com',
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'kata-sandi-rahasia',
            'password_confirmation' => 'kata-sandi-rahasia',
        ])->assertRedirect(route('catatan.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
        ]);

        $user = User::where('email', 'baru@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('kata-sandi-rahasia', $user->password));
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
