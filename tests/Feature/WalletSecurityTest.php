<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Wallet $wallet;
    private Wallet $otherUserWallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // Create wallets for each user
        $this->wallet = Wallet::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserWallet = Wallet::factory()->create(['user_id' => $this->otherUser->id]);
    }

    /**
     * Test 1: Wallet memiliki UUID yang ter-generate otomatis
     */
    public function test_wallet_has_uuid(): void
    {
        $this->assertNotNull($this->wallet->uuid);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($this->wallet->uuid));
    }

    /**
     * Test 2: Setiap wallet punya UUID yang unique
     */
    public function test_wallet_uuid_is_unique(): void
    {
        $uuid1 = $this->wallet->uuid;
        $uuid2 = $this->otherUserWallet->uuid;

        $this->assertNotEquals($uuid1, $uuid2);
    }

    /**
     * Test 3: Route binding menggunakan UUID, bukan ID integer
     */
    public function test_route_key_name_uses_uuid(): void
    {
        $this->assertEquals('uuid', $this->wallet->getRouteKeyName());
    }

    /**
     * Test 4: Route menggunakan UUID di URL (bukan ID integer)
     */
    public function test_wallet_route_generates_uuid_url(): void
    {
        $url = route('wallets.show', $this->wallet);

        // URL harusnya berisi UUID, bukan integer ID
        $this->assertStringContainsString($this->wallet->uuid, $url);
        $this->assertStringNotContainsString("/{$this->wallet->id}", $url);
    }

    /**
     * Test 5: Authorized user bisa akses wallet mereka dengan UUID
     */
    public function test_authorized_user_can_view_own_wallet(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('wallets.show', $this->wallet));

        $response->assertSuccessful();
        $response->assertSee($this->wallet->name);
    }

    /**
     * Test 6: User tidak bisa akses wallet user lain (IDOR Protection)
     * PENTING: Ini adalah test untuk IDOR vulnerability
     */
    public function test_unauthorized_user_cannot_view_other_wallet(): void
    {
        // User mencoba akses wallet orang lain
        $response = $this->actingAs($this->user)
            ->get(route('wallets.show', $this->otherUserWallet));

        // Harusnya 403 Forbidden (authorized ke route, tapi tidak authorized ke resource)
        $response->assertForbidden();
    }

    /**
     * Test 7: User tidak authenticated tidak bisa akses wallet (ke login page)
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('wallets.show', $this->wallet));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test 8: Random UUID yang tidak ada akan return 404
     */
    public function test_invalid_uuid_returns_404(): void
    {
        $randomUuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $response = $this->actingAs($this->user)
            ->get("/wallets/{$randomUuid}");

        $response->assertNotFound();
    }

    /**
     * Test 9: UUID tidak bisa ditebak (random dan long)
     */
    public function test_uuid_is_not_guessable(): void
    {
        $uuid = $this->wallet->uuid;

        // UUID format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (36 chars)
        $this->assertEquals(36, strlen($uuid));

        // UUID harus valid v4 format
        $this->assertTrue(\Illuminate\Support\Str::isUuid($uuid));
    }

    /**
     * Test 10: Wallet yang baru dibuat otomatis punya UUID
     */
    public function test_new_wallet_auto_generates_uuid(): void
    {
        $newWallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'uuid' => null, // Tidak set, harusnya auto-generate
        ]);

        $this->assertNotNull($newWallet->uuid);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($newWallet->uuid));
    }

    /**
     * Test 11: Edit wallet - UUID tetap sama (tidak berubah)
     */
    public function test_wallet_uuid_remains_same_after_update(): void
    {
        $originalUuid = $this->wallet->uuid;

        $this->wallet->update(['name' => 'Updated Name']);

        $this->wallet->refresh();
        $this->assertEquals($originalUuid, $this->wallet->uuid);
    }

    /**
     * Test 12: Delete wallet - hanya owner bisa
     */
    public function test_only_owner_can_delete_wallet(): void
    {
        // Owner bisa delete
        $response = $this->actingAs($this->user)
            ->delete(route('wallets.destroy', $this->wallet));

        $response->assertRedirect();
        $this->assertDatabaseMissing('wallets', ['id' => $this->wallet->id]);
    }

    /**
     * Test 13: Non-owner tidak bisa delete wallet orang lain
     */
    public function test_non_owner_cannot_delete_other_wallet(): void
    {
        $response = $this->actingAs($this->user)
            ->delete(route('wallets.destroy', $this->otherUserWallet));

        $response->assertForbidden();
        $this->assertDatabaseHas('wallets', ['id' => $this->otherUserWallet->id]);
    }

    /**
     * Test 14: Edit wallet - hanya owner bisa
     */
    public function test_only_owner_can_edit_wallet(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('wallets.edit', $this->wallet));

        $response->assertSuccessful();
    }

    /**
     * Test 15: Non-owner tidak bisa edit wallet orang lain
     */
    public function test_non_owner_cannot_edit_other_wallet(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('wallets.edit', $this->otherUserWallet));

        $response->assertForbidden();
    }
}
