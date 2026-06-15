<?php

namespace Tests\Feature;

use App\Jobs\SendRegistrationSms;
use App\Mail\RegistrationStageUpdated;
use App\Models\Event;
use App\Models\FinalRegistration;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FinalRegistrationFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The pdo_sqlite extension is required for database-backed feature tests.');
        }

        $this->artisan('migrate:fresh')->run();
    }

    public function test_type_one_event_qualifies_then_collects_and_confirms_final_payment(): void
    {
        Mail::fake();
        Queue::fake();

        $admin = User::factory()->create();
        $event = Event::create([
            'code' => '01',
            'name' => 'Inter University Programming Contest (IUPC)',
            'min_team_size' => 3,
            'max_team_size' => 3,
            'amount' => 1500,
        ]);
        $registration = $this->registration($event, [
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $participant = $registration->participants()->create($this->participantData());

        $this->actingAs($admin)
            ->patch(route('dashboard.events.registrations.approve', [$event, $registration]))
            ->assertRedirect();

        $registration->refresh();

        $this->assertSame('verified', $registration->status);
        $this->assertSame('unpaid', $registration->payment_status);
        $this->assertNull($registration->payment);
        Mail::assertQueued(RegistrationStageUpdated::class);
        Queue::assertPushed(SendRegistrationSms::class);

        Mail::fake();
        Queue::fake();

        $this->post(route('final-registration.store', ['registration_code' => $registration->registration_code]), [
            'team_name' => 'Team Alpha Updated',
            'payment_method' => 'bkash',
            'trx_id' => 'T1FINAL123',
            'participants' => [
                [
                    'id' => $participant->id,
                    'full_name' => 'Updated Lead',
                    'email' => 'updated@example.test',
                    'phone' => '01700000000',
                    'student_id' => 'S-002',
                    'tshirt_size' => 'L',
                ],
            ],
        ])->assertRedirect();

        $registration->refresh();

        $this->assertSame('submitted', $registration->payment_status);
        $this->assertSame(1500, $registration->payment->amount);
        $this->assertSame('bkash', $registration->payment->method);
        $this->assertSame('T1FINAL123', $registration->payment->trx_id);
        $this->assertSame(FinalRegistration::STATUS_SUBMITTED, $registration->finalRegistration->status);
        $this->assertSame('Team Alpha Updated', $registration->team_name);
        $this->assertSame('Updated Lead', $participant->refresh()->full_name);
        $this->assertSame('IUT', $participant->university);

        $this->actingAs($admin)
            ->patch(route('dashboard.events.registrations.approve', [$event, $registration]))
            ->assertRedirect();

        $registration->refresh();

        $this->assertSame('paid', $registration->status);
        $this->assertSame('confirmed', $registration->payment_status);
        $this->assertSame('confirmed', $registration->payment->status);
        $this->assertSame(FinalRegistration::STATUS_APPROVED, $registration->finalRegistration->status);
        Mail::assertQueued(RegistrationStageUpdated::class);
        Queue::assertPushed(SendRegistrationSms::class);
    }

    public function test_type_two_event_confirms_initial_payment_then_collects_and_approves_tshirt_intake(): void
    {
        Mail::fake();
        Queue::fake();

        $admin = User::factory()->create();
        $event = Event::create([
            'code' => '03',
            'name' => 'Datathon',
            'min_team_size' => 1,
            'max_team_size' => 4,
            'amount' => 800,
        ]);
        $registration = $this->registration($event, [
            'status' => 'pending',
            'payment_status' => 'submitted',
        ]);
        $participant = $registration->participants()->create($this->participantData());
        Payment::create([
            'registration_id' => $registration->id,
            'amount' => 800,
            'method' => 'nagad',
            'trx_id' => 'INITIAL123',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('dashboard.events.registrations.approve', [$event, $registration]))
            ->assertRedirect();

        $registration->refresh();

        $this->assertSame('paid', $registration->status);
        $this->assertSame('confirmed', $registration->payment_status);
        $this->assertSame('confirmed', $registration->payment->status);
        Mail::assertQueued(RegistrationStageUpdated::class);
        Queue::assertPushed(SendRegistrationSms::class);

        Mail::fake();
        Queue::fake();

        $this->actingAs($admin)
            ->patch(route('dashboard.events.registrations.approve', [$event, $registration]))
            ->assertRedirect();

        $this->assertSame(FinalRegistration::STATUS_INVITED, $registration->refresh()->finalRegistration->status);

        $this->post(route('final-registration.store', ['registration_code' => $registration->registration_code]), [
            'team_name' => 'Team Beta',
            'participants' => [
                [
                    'id' => $participant->id,
                    'full_name' => 'Updated Lead',
                    'email' => 'updated@example.test',
                    'phone' => '01700000000',
                    'student_id' => 'S-002',
                    'tshirt_size' => 'M',
                ],
            ],
        ])->assertRedirect();

        $registration->refresh();

        $this->assertSame('INITIAL123', $registration->payment->trx_id);
        $this->assertNull($registration->finalRegistration->trx_id);
        $this->assertSame(FinalRegistration::STATUS_SUBMITTED, $registration->finalRegistration->status);
        $this->assertSame('M', $participant->refresh()->tshirt_size);
        $this->assertSame('Updated Lead', $participant->full_name);
        $this->assertSame('IUT', $participant->university);

        $this->actingAs($admin)
            ->patch(route('dashboard.events.registrations.approve', [$event, $registration]))
            ->assertRedirect();

        $this->assertSame(FinalRegistration::STATUS_APPROVED, $registration->refresh()->finalRegistration->status);
        Mail::assertQueued(RegistrationStageUpdated::class);
        Queue::assertPushed(SendRegistrationSms::class);
    }

    /**
     * @param array<string, string> $overrides
     */
    private function registration(Event $event, array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'registration_code' => $event->code.'-12345',
            'event_id' => $event->id,
            'team_name' => 'Team Alpha',
            'institution' => 'IUT',
            'contact_name' => 'Team Lead',
            'contact_email' => 'lead@example.test',
            'contact_phone' => '01645534121',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ], $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    private function participantData(): array
    {
        return [
            'full_name' => 'Team Lead',
            'email' => 'lead@example.test',
            'phone' => '01645534121',
            'student_id' => 'S-001',
            'university' => 'IUT',
            'is_leader' => true,
        ];
    }
}
