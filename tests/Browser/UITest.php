<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class UITest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_role_based_colors_hr()
    {
        $user = User::factory()->create(['role' => 'hr']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/hr/dashboard')
                    ->assertHasCss('--main-color', '#FF8C42'); // Orange for HR
        });
    }

    public function test_role_based_colors_it_manager()
    {
        $user = User::factory()->create(['role' => 'it_manager']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/it-manager/dashboard')
                    ->assertHasCss('--main-color', '#7B68EE'); // Purple for IT Manager
        });
    }

    public function test_role_based_colors_technician()
    {
        $user = User::factory()->create(['role' => 'technician']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/technician/dashboard')
                    ->assertHasCss('--main-color', '#20B2AA'); // Teal for Technician
        });
    }

    public function test_notification_dropdown()
    {
        $user = User::factory()->create(['role' => 'hr']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/hr/dashboard')
                    ->assertPresent('#notificationBell')
                    ->click('#notificationBell')
                    ->assertPresent('#notificationMenu')
                    ->assertSee('Notifications');
        });
    }

    public function test_logout()
    {
        $user = User::factory()->create(['role' => 'hr']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/hr/dashboard')
                    ->press('Déconnexion')
                    ->assertPathIs('/login');
        });
    }

    public function test_unauthorized_access_redirect()
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $this->browse(function (Browser $browser) use ($hr) {
            $browser->loginAs($hr)
                    ->visit('/it-manager/dashboard') // HR trying IT Manager route
                    ->assertPathIsNot('/it-manager/dashboard'); // Should redirect
        });
    }
}