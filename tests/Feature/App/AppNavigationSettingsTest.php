<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppNavigationSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppNavigationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithApp()
    {
        $user = User::factory()->create();
        $app = App::factory()->forUser($user)->create();
        $this->actingAs($user);

        return [$user, $app];
    }

    public function test_component_renders_for_authenticated_user()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('pull_to_refresh', $app->androidWebViewConfig->pull_to_refresh)
            ->assertSet('swipe_navigation', $app->androidWebViewConfig->swipe_navigation);
    }

    public function test_navigation_items_array_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('navigation_items', [])
            ->call('save')
            ->assertHasNoErrors('navigation_items');
    }

    public function test_navigation_items_cannot_exceed_max_limit()
    {
        [$user, $app] = $this->createUserWithApp();

        // Create 10 items to exceed the max limit of 9
        $items = array_fill(0, 10, [
            'label' => 'Item',
            'action_type' => 'internal',
            'value' => 'https://example.com',
            'icon' => 'home',
        ]);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('navigation_items', $items)
            ->call('save')
            ->assertHasErrors(['navigation_items' => 'max']);
    }

    public function test_app_bar_buttons_array_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('app_bar_buttons', [])
            ->call('save')
            ->assertHasNoErrors('app_bar_buttons');
    }

    public function test_app_bar_buttons_cannot_exceed_max_limit()
    {
        [$user, $app] = $this->createUserWithApp();

        $buttons = array_fill(0, 4, [
            'label' => 'Button',
            'action_type' => 'internal',
            'value' => 'https://example.com',
            'icon' => 'home',
        ]);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('app_bar_buttons', $buttons)
            ->call('save')
            ->assertHasErrors(['app_bar_buttons' => 'max']);
    }

    public function test_pull_to_refresh_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('pull_to_refresh', '')
            ->call('save')
            ->assertHasErrors(['pull_to_refresh' => 'required']);
    }

    public function test_pull_to_refresh_must_be_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('pull_to_refresh', 'not-a-boolean')
            ->call('save')
            ->assertHasErrors(['pull_to_refresh' => 'boolean']);
    }

    public function test_swipe_navigation_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('swipe_navigation', '')
            ->call('save')
            ->assertHasErrors(['swipe_navigation' => 'required']);
    }

    public function test_swipe_navigation_must_be_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('swipe_navigation', 'not-a-boolean')
            ->call('save')
            ->assertHasErrors(['swipe_navigation' => 'boolean']);
    }

    public function test_can_add_app_bar_button()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->call('addAppBarButton')
            ->assertCount('app_bar_buttons', 1);
    }

    public function test_cannot_add_more_than_3_app_bar_buttons()
    {
        [$user, $app] = $this->createUserWithApp();

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Add 3 buttons
        for ($i = 0; $i < 3; $i++) {
            $component->call('addAppBarButton');
        }

        $component->assertCount('app_bar_buttons', 3);

        // Try to add 4th button
        $component->call('addAppBarButton');
        $component->assertCount('app_bar_buttons', 3); // Should still be 3
    }

    public function test_can_remove_app_bar_button()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->call('addAppBarButton')
            ->call('addAppBarButton')
            ->assertCount('app_bar_buttons', 2)
            ->call('removeAppBarButton', 0)
            ->assertCount('app_bar_buttons', 1);
    }

    public function test_home_tab_cannot_be_deleted()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->call('removeNavigationItem', 0)
            ->assertDispatched('show-toast')
            ->assertCount('navigation_items', 1); // Home tab still exists
    }

    public function test_home_tab_cannot_be_reordered()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Add a regular tab
        $component->call('addNavigationItem');
        $component->set('navigation_items.1.label', 'Shop');
        $component->set('navigation_items.1.value', 'https://shop.com');

        // Try to move home tab down - should fail
        $component->call('moveDown', 0)
            ->assertDispatched('show-toast');

        // Home tab should still be at index 0
        $items = $component->get('navigation_items');
        $this->assertEquals('Home', $items[0]['label']);
        $this->assertTrue($items[0]['is_home']);
        $this->assertEquals('Shop', $items[1]['label']);

        // Try to move home tab up - should also fail
        $component->call('moveUp', 0)
            ->assertDispatched('show-toast');

        // Home tab should still be at index 0
        $items = $component->get('navigation_items');
        $this->assertEquals('Home', $items[0]['label']);
        $this->assertTrue($items[0]['is_home']);
    }

    public function test_home_tab_label_can_be_edited()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('navigation_items.0.label', 'Main')
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertEquals('Main', $app->androidWebViewConfig->navigation_items[0]['label']);
        $this->assertTrue($app->androidWebViewConfig->navigation_items[0]['is_home']);
    }

    public function test_home_tab_icon_can_be_changed()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('navigation_items.0.icon', 'shopping-cart')
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertEquals('shopping-cart', $app->androidWebViewConfig->navigation_items[0]['icon']);
        $this->assertTrue($app->androidWebViewConfig->navigation_items[0]['is_home']);
    }

    public function test_can_add_up_to_8_regular_tabs_plus_home_tab()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Add 8 regular tabs
        for ($i = 0; $i < 8; $i++) {
            $component->call('addNavigationItem');
        }

        // Should have 9 total items (1 home + 8 regular)
        $component->assertCount('navigation_items', 9);

        // Trying to add 9th regular tab should fail
        $component->call('addNavigationItem');
        $component->assertCount('navigation_items', 9);
    }

    public function test_home_tab_not_created_for_other_layouts()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer', 'navigation_items' => []]);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Home tab should not be created for non-tabs layouts
        $this->assertCount(0, $component->get('navigation_items'));
    }

    public function test_app_bar_button_icon_selection_persists_correctly()
    {
        [$user, $app] = $this->createUserWithApp();

        // Add an app bar button with a specific icon
        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->call('addAppBarButton')
            ->set('app_bar_buttons.0.label', 'Search')
            ->set('app_bar_buttons.0.action_type', 'internal')
            ->set('app_bar_buttons.0.value', 'https://search.com')
            ->set('app_bar_buttons.0.icon', 'settings')
            ->call('save')
            ->assertHasNoErrors();

        // Verify the icon was saved correctly
        $app->refresh();
        $this->assertCount(1, $app->androidWebViewConfig->app_bar_buttons);
        $this->assertEquals('settings', $app->androidWebViewConfig->app_bar_buttons[0]['icon']);
    }

    public function test_drawer_home_item_appears_by_default()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Home item should be created automatically for drawer layout
        $drawerItems = $component->get('drawer_items');
        $this->assertCount(1, $drawerItems);
        $this->assertEquals('Home', $drawerItems[0]['label']);
        $this->assertTrue($drawerItems[0]['is_home']);
        $this->assertEquals($app->androidWebViewConfig->url, $drawerItems[0]['value']);
    }

    public function test_drawer_home_item_cannot_be_deleted()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->call('removeDrawerItem', 0)
            ->assertDispatched('show-toast')
            ->assertCount('drawer_items', 1); // Home item still exists
    }

    public function test_drawer_home_item_cannot_be_reordered()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Add a regular item
        $component->call('addDrawerItem');
        $component->set('drawer_items.1.label', 'About');
        $component->set('drawer_items.1.value', 'https://about.com');

        // Try to move home item down - should fail
        $component->call('moveDrawerItemDown', 0)
            ->assertDispatched('show-toast');

        // Home item should still be at index 0
        $items = $component->get('drawer_items');
        $this->assertEquals('Home', $items[0]['label']);
        $this->assertTrue($items[0]['is_home']);
        $this->assertEquals('About', $items[1]['label']);

        // Try to move home item up - should also fail
        $component->call('moveDrawerItemUp', 0)
            ->assertDispatched('show-toast');

        // Home item should still be at index 0
        $items = $component->get('drawer_items');
        $this->assertEquals('Home', $items[0]['label']);
        $this->assertTrue($items[0]['is_home']);
    }

    public function test_drawer_home_item_label_can_be_edited()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('drawer_items.0.label', 'Main')
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertEquals('Main', $app->androidWebViewConfig->drawer_items[0]['label']);
        $this->assertTrue($app->androidWebViewConfig->drawer_items[0]['is_home']);
    }

    public function test_drawer_home_item_icon_can_be_changed()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        Livewire::test(AppNavigationSettings::class, ['app' => $app])
            ->set('drawer_items.0.icon', 'shopping-cart')
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertEquals('shopping-cart', $app->androidWebViewConfig->drawer_items[0]['icon']);
        $this->assertTrue($app->androidWebViewConfig->drawer_items[0]['is_home']);
    }

    public function test_can_add_up_to_9_additional_drawer_items()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Add 9 regular items
        for ($i = 0; $i < 9; $i++) {
            $component->call('addDrawerItem');
        }

        // Should have 10 total items (1 home + 9 regular)
        $component->assertCount('drawer_items', 10);

        // Trying to add 10th regular item should fail (not add)
        $component->call('addDrawerItem');
        $component->assertCount('drawer_items', 10); // Should still be 10
    }

    public function test_drawer_home_item_stays_at_top_after_save_when_out_of_order()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update([
            'layout_template' => 'app_bar_drawer',
            // Simulate a corrupted state where home item is not at index 0
            'drawer_items' => [
                ['label' => 'About', 'action_type' => 'internal', 'value' => 'https://about.com', 'icon' => 'info'],
                ['label' => 'Home', 'action_type' => 'internal', 'value' => $app->url, 'icon' => 'home', 'is_home' => true],
            ],
        ]);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Home item should be moved to index 0 by ensureHomeItem() on mount
        $drawerItems = $component->get('drawer_items');
        $this->assertTrue($drawerItems[0]['is_home'] ?? false);
        $this->assertEquals('Home', $drawerItems[0]['label']);
        $this->assertEquals('About', $drawerItems[1]['label']);
    }

    public function test_navigation_home_tab_stays_at_top_after_save_when_out_of_order()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update([
            'layout_template' => 'app_bar_tabs',
            // Simulate a corrupted state where home tab is not at index 0
            'navigation_items' => [
                ['label' => 'Shop', 'action_type' => 'internal', 'value' => 'https://shop.com', 'icon' => 'shopping-cart'],
                ['label' => 'Home', 'action_type' => 'internal', 'value' => $app->url, 'icon' => 'home', 'is_home' => true],
            ],
        ]);

        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Home tab should be moved to index 0 by ensureHomeItem() on mount
        $navigationItems = $component->get('navigation_items');
        $this->assertTrue($navigationItems[0]['is_home'] ?? false);
        $this->assertEquals('Home', $navigationItems[0]['label']);
        $this->assertEquals('Shop', $navigationItems[1]['label']);
    }

    public function test_full_save_cycle_preserves_home_at_top_for_navigation()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_tabs']);

        // Step 1: Mount component (creates home tab at index 0)
        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Step 2: Add a regular item (should go after home)
        $component->call('addNavigationItem');
        $component->set('navigation_items.1.label', 'Shop');
        $component->set('navigation_items.1.value', 'https://shop.com');

        // Step 3: Save
        $component->call('save');

        // Step 4: Verify database has correct order
        $app->refresh();
        $dbItems = $app->androidWebViewConfig->navigation_items;
        $this->assertTrue($dbItems[0]['is_home'] ?? false, 'Home should be at index 0 in database');
        $this->assertEquals('Shop', $dbItems[1]['label'] ?? '', 'Shop should be at index 1 in database');

        // Step 5: Simulate page refresh by creating new component instance
        $newComponent = Livewire::test(AppNavigationSettings::class, ['app' => $app->fresh()]);
        $reloadedItems = $newComponent->get('navigation_items');

        $this->assertTrue($reloadedItems[0]['is_home'] ?? false, 'Home should be at index 0 after reload');
        $this->assertEquals('Shop', $reloadedItems[1]['label'] ?? '', 'Shop should be at index 1 after reload');
    }

    public function test_full_save_cycle_preserves_home_at_top_for_drawer()
    {
        [$user, $app] = $this->createUserWithApp();
        $app->androidWebViewConfig->update(['layout_template' => 'app_bar_drawer']);

        // Step 1: Mount component (creates home item at index 0)
        $component = Livewire::test(AppNavigationSettings::class, ['app' => $app]);

        // Step 2: Add a regular item (should go after home)
        $component->call('addDrawerItem');
        $component->set('drawer_items.1.label', 'About');
        $component->set('drawer_items.1.value', 'https://about.com');

        // Step 3: Save
        $component->call('save');

        // Step 4: Verify database has correct order
        $app->refresh();
        $dbItems = $app->androidWebViewConfig->drawer_items;
        $this->assertTrue($dbItems[0]['is_home'] ?? false, 'Home should be at index 0 in database');
        $this->assertEquals('About', $dbItems[1]['label'] ?? '', 'About should be at index 1 in database');

        // Step 5: Simulate page refresh by creating new component instance
        $newComponent = Livewire::test(AppNavigationSettings::class, ['app' => $app->fresh()]);
        $reloadedItems = $newComponent->get('drawer_items');

        $this->assertTrue($reloadedItems[0]['is_home'] ?? false, 'Home should be at index 0 after reload');
        $this->assertEquals('About', $reloadedItems[1]['label'] ?? '', 'About should be at index 1 after reload');
    }
}
