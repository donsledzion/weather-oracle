<?php

namespace Tests\Unit\Models;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'email',
            'user_id',
            'token',
            'first_snapshot_enabled',
            'daily_summary_enabled',
            'final_summary_enabled',
        ];

        $preference = new NotificationPreference();

        $this->assertEquals($fillable, $preference->getFillable());
    }

    /** @test */
    public function it_casts_boolean_fields()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'test-token',
            'first_snapshot_enabled' => 1,
            'daily_summary_enabled' => 0,
            'final_summary_enabled' => 1,
        ]);

        $this->assertIsBool($preference->first_snapshot_enabled);
        $this->assertIsBool($preference->daily_summary_enabled);
        $this->assertIsBool($preference->final_summary_enabled);

        $this->assertTrue($preference->first_snapshot_enabled);
        $this->assertFalse($preference->daily_summary_enabled);
        $this->assertTrue($preference->final_summary_enabled);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $preference = NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
        ]);

        $this->assertInstanceOf(User::class, $preference->user);
        $this->assertEquals($user->id, $preference->user->id);
    }

    /** @test */
    public function get_for_email_creates_new_preference_with_token()
    {
        $email = 'newuser@example.com';

        $this->assertDatabaseMissing('notification_preferences', ['email' => $email]);

        $preference = NotificationPreference::getForEmail($email);

        $this->assertDatabaseHas('notification_preferences', ['email' => $email]);
        $this->assertEquals($email, $preference->email);
        $this->assertNotEmpty($preference->token);
        $this->assertEquals(64, strlen($preference->token));
    }

    /** @test */
    public function get_for_email_returns_existing_preference()
    {
        $email = 'existing@example.com';
        $existingToken = 'existing-token-123';

        $existing = NotificationPreference::create([
            'email' => $email,
            'token' => $existingToken,
        ]);

        $preference = NotificationPreference::getForEmail($email);

        $this->assertEquals($existing->id, $preference->id);
        $this->assertEquals($existingToken, $preference->token);

        // Should still be only one record
        $this->assertEquals(1, NotificationPreference::where('email', $email)->count());
    }

    /** @test */
    public function get_for_user_creates_new_preference_with_token()
    {
        $user = User::factory()->create();

        $this->assertDatabaseMissing('notification_preferences', ['user_id' => $user->id]);

        $preference = NotificationPreference::getForUser($user->id);

        $this->assertDatabaseHas('notification_preferences', ['user_id' => $user->id]);
        $this->assertEquals($user->id, $preference->user_id);
        $this->assertNotEmpty($preference->token);
        $this->assertEquals(64, strlen($preference->token));
    }

    /** @test */
    public function get_for_user_returns_existing_preference()
    {
        $user = User::factory()->create();
        $existingToken = 'existing-user-token';

        $existing = NotificationPreference::create([
            'user_id' => $user->id,
            'token' => $existingToken,
        ]);

        $preference = NotificationPreference::getForUser($user->id);

        $this->assertEquals($existing->id, $preference->id);
        $this->assertEquals($existingToken, $preference->token);

        // Should still be only one record
        $this->assertEquals(1, NotificationPreference::where('user_id', $user->id)->count());
    }

    /** @test */
    public function get_by_token_finds_preference()
    {
        $token = 'unique-token-123';

        $preference = NotificationPreference::create([
            'email' => 'token@example.com',
            'token' => $token,
        ]);

        $found = NotificationPreference::getByToken($token);

        $this->assertNotNull($found);
        $this->assertEquals($preference->id, $found->id);
        $this->assertEquals($token, $found->token);
    }

    /** @test */
    public function get_by_token_returns_null_when_not_found()
    {
        $found = NotificationPreference::getByToken('non-existent-token');

        $this->assertNull($found);
    }

    /** @test */
    public function has_any_enabled_returns_true_when_first_snapshot_enabled()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'token',
            'first_snapshot_enabled' => true,
            'daily_summary_enabled' => false,
            'final_summary_enabled' => false,
        ]);

        $this->assertTrue($preference->hasAnyEnabled());
    }

    /** @test */
    public function has_any_enabled_returns_true_when_daily_summary_enabled()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'token',
            'first_snapshot_enabled' => false,
            'daily_summary_enabled' => true,
            'final_summary_enabled' => false,
        ]);

        $this->assertTrue($preference->hasAnyEnabled());
    }

    /** @test */
    public function has_any_enabled_returns_true_when_final_summary_enabled()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'token',
            'first_snapshot_enabled' => false,
            'daily_summary_enabled' => false,
            'final_summary_enabled' => true,
        ]);

        $this->assertTrue($preference->hasAnyEnabled());
    }

    /** @test */
    public function has_any_enabled_returns_false_when_all_disabled()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'token',
            'first_snapshot_enabled' => false,
            'daily_summary_enabled' => false,
            'final_summary_enabled' => false,
        ]);

        $this->assertFalse($preference->hasAnyEnabled());
    }

    /** @test */
    public function has_any_enabled_returns_true_when_multiple_enabled()
    {
        $preference = NotificationPreference::create([
            'email' => 'test@example.com',
            'token' => 'token',
            'first_snapshot_enabled' => true,
            'daily_summary_enabled' => true,
            'final_summary_enabled' => true,
        ]);

        $this->assertTrue($preference->hasAnyEnabled());
    }

    /** @test */
    public function token_is_unique_for_each_new_preference()
    {
        $pref1 = NotificationPreference::getForEmail('user1@example.com');
        $pref2 = NotificationPreference::getForEmail('user2@example.com');

        $this->assertNotEquals($pref1->token, $pref2->token);
    }
}
