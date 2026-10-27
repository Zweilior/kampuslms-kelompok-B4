<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Notifications\TugasBaru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationWebTest extends TestCase
{
    use RefreshDatabase;

    /** Kirim satu notifikasi nyata (queue di test = sync) ke user. */
    private function notify(User $user): void
    {
        $course = Course::factory()
            ->for(User::factory()->dosen()->create(), 'lecturer')
            ->create();

        $user->notify(new TugasBaru(Assignment::factory()->for($course)->create()));
    }

    public function test_guest_cannot_use_notification_routes(): void
    {
        $this->getJson(route('notifications.unread-count'))->assertUnauthorized();
        $this->postJson(route('notifications.read', 'apa-saja'))->assertUnauthorized();
        $this->postJson(route('notifications.read-all'))->assertUnauthorized();
    }

    public function test_index_lists_only_own_notifications(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $other = User::factory()->mahasiswa()->create();
        $this->notify($me);
        $this->notify($other);
        $this->notify($other);

        $this->actingAs($me)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertViewHas('notifications', fn ($page) => $page->total() === 1);
    }

    public function test_unread_count_counts_only_own_and_limits_latest_to_five(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $other = User::factory()->mahasiswa()->create();

        foreach (range(1, 7) as $i) {
            $this->notify($me);
        }
        $this->notify($other);

        $this->actingAs($me)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('count', 7)
            ->assertJsonCount(5, 'latest')
            ->assertJsonStructure(['count', 'latest' => [['id', 'message', 'is_read', 'created_at', 'url']]]);
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $this->notify($me);
        $notification = $me->notifications()->first();

        $this->actingAs($me)
            ->postJson(route('notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_someone_elses_notification_as_read(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $victim = User::factory()->mahasiswa()->create();
        $this->notify($victim);
        $victimNotification = $victim->notifications()->first();

        $this->actingAs($me)
            ->postJson(route('notifications.read', $victimNotification->id))
            ->assertNotFound();

        $this->assertNull($victimNotification->fresh()->read_at);
    }

    public function test_unknown_notification_id_returns_404(): void
    {
        $me = User::factory()->mahasiswa()->create();

        $this->actingAs($me)
            ->postJson(route('notifications.read', '00000000-0000-0000-0000-000000000000'))
            ->assertNotFound();
    }

    public function test_mark_all_as_read_only_affects_own_notifications(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $other = User::factory()->mahasiswa()->create();
        $this->notify($me);
        $this->notify($me);
        $this->notify($other);

        $this->actingAs($me)
            ->postJson(route('notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('count', 0);

        $this->assertSame(0, $me->unreadNotifications()->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
    }

    public function test_user_can_open_notification_and_be_redirected_to_related_page(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $course = Course::factory()
            ->for(User::factory()->dosen()->create(), 'lecturer')
            ->create();
        $assignment = Assignment::factory()->for($course)->create();

        $me->notify(new TugasBaru($assignment));
        $notification = $me->notifications()->first();

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($me)->get(route('notifications.open', $notification->id));

        $response->assertRedirect(route('mahasiswa.courses.assignments.index', $course->id));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_open_someone_elses_notification(): void
    {
        $me = User::factory()->mahasiswa()->create();
        $victim = User::factory()->mahasiswa()->create();
        $this->notify($victim);
        $victimNotification = $victim->notifications()->first();

        $this->actingAs($me)
            ->get(route('notifications.open', $victimNotification->id))
            ->assertNotFound();

        $this->assertNull($victimNotification->fresh()->read_at);
    }
}