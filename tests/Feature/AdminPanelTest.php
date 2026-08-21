<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true, 'is_verified' => true]);
    }

    private function shopOwner(): User
    {
        return User::factory()->create(['role' => 'shop_owner', 'is_active' => true, 'is_verified' => true]);
    }

    public function test_admin_dashboard_renders(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Platform overview');
    }

    public function test_admin_users_list_renders(): void
    {
        User::factory()->create(['role' => 'manufacturer']);

        $this->actingAs($this->admin())
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_admin_user_show_renders(): void
    {
        $user = User::factory()->create(['role' => 'manufacturer']);

        $this->actingAs($this->admin())
            ->get(route('admin.users.show', $user->id))
            ->assertOk();
    }

    public function test_admin_reports_renders(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.reports'))
            ->assertOk();
    }

    public function test_admin_can_suspend_user(): void
    {
        $user = User::factory()->create(['role' => 'shop_owner', 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.users.toggle-active', $user->id))
            ->assertRedirect();

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_shop_owner_cannot_access_admin(): void
    {
        $this->actingAs($this->shopOwner())
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('shop.dashboard'));
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => 'shop_owner',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_cannot_be_suspended(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-active', $admin->id))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }
}
