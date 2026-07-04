<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\EmailController;
use App\Http\Controllers\Dashboard\EventRegistrationController;
use App\Http\Controllers\Dashboard\EventStatusController;
use App\Http\Controllers\Dashboard\IupcBkashRecipientController;
use App\Http\Controllers\Dashboard\IupcSlotController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\StatusLookupController;
use App\Http\Controllers\Dashboard\TshirtController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\EventRulebookController;
use App\Http\Controllers\IupcCoachPortalController;
use App\Http\Controllers\IupcRegistrationController;
use App\Models\IupcUniversityAllocation;
use App\Http\Controllers\HackathonRegistrationController;
use App\Http\Controllers\DatathonRegistrationController;
use App\Http\Controllers\FifaRegistrationController;
use App\Http\Controllers\FinalRegistrationController;
use App\Http\Controllers\GamejamRegistrationController;
use App\Http\Controllers\RegistrationStatusController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValorantRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nDisallow:\n\nSitemap: ".url('/sitemap.xml')."\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
});

Route::get('/sitemap.xml', function () {
    $pages = collect([
        ['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
        ['url' => url('/iupc'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => route('iupc.slots'), 'priority' => '0.6', 'changefreq' => 'daily'],
        ['url' => url('/hackathon'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => url('/datathon'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => url('/gamejam'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => url('/fifa'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => url('/valorant'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'iupc']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'hackathon']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'datathon']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'gamejam']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'fifa']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('events.rulebook', ['eventSlug' => 'valorant']), 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => route('registration.status'), 'priority' => '0.5', 'changefreq' => 'weekly'],
        ['url' => route('contact'), 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['url' => route('about'), 'priority' => '0.4', 'changefreq' => 'monthly'],
    ]);

    $lastmod = now()->toDateString();
    $urls = $pages->map(fn (array $page): string => sprintf(
        "    <url>\n        <loc>%s</loc>\n        <lastmod>%s</lastmod>\n        <changefreq>%s</changefreq>\n        <priority>%s</priority>\n    </url>",
        e($page['url']),
        $lastmod,
        $page['changefreq'],
        $page['priority'],
    ))->implode("\n");

    return response(
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n".$urls."\n</urlset>\n",
        200,
        ['Content-Type' => 'application/xml; charset=UTF-8']
    );
});

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn () => redirect()->route('dashboard.users.index'))->name('dashboard');
    Route::get('/dashboard/status', [StatusLookupController::class, 'index'])->name('dashboard.status.index');
    Route::get('/dashboard/tshirt', [TshirtController::class, 'index'])->name('dashboard.tshirts.index');
    Route::get('/dashboard/reports', [ReportController::class, 'index'])->name('dashboard.reports.index');
    Route::get('/dashboard/reports/download', [ReportController::class, 'download'])->name('dashboard.reports.download');
    Route::get('/dashboard/reports/complete-pdf', [ReportController::class, 'completePdf'])->name('dashboard.reports.complete-pdf');
    Route::get('/dashboard/email', [EmailController::class, 'index'])->name('dashboard.emails.index');
    Route::get('/dashboard/email/compose', [EmailController::class, 'compose'])->name('dashboard.emails.compose');
    Route::post('/dashboard/email/compose', [EmailController::class, 'storeCompose'])->name('dashboard.emails.compose.store');
    Route::get('/dashboard/email/recipients', [EmailController::class, 'recipients'])->name('dashboard.emails.recipients');
    Route::post('/dashboard/email/recipients', [EmailController::class, 'storeRecipients'])->name('dashboard.emails.recipients.store');
    Route::get('/dashboard/email/review', [EmailController::class, 'review'])->name('dashboard.emails.review');
    Route::post('/dashboard/email/send', [EmailController::class, 'send'])->name('dashboard.emails.send');
    Route::get('/dashboard/email/history', [EmailController::class, 'history'])->name('dashboard.emails.history');
    Route::get('/dashboard/email/history/{notification}', [EmailController::class, 'show'])->name('dashboard.emails.show');
    Route::get('/dashboard/email-logs', [EmailController::class, 'history'])->name('dashboard.email-logs.index');
    Route::get('/dashboard/email-logs/{notification}', [EmailController::class, 'show'])->name('dashboard.email-logs.show');
    Route::get('/dashboard/event-status', [EventStatusController::class, 'index'])->name('dashboard.event-status.index');
    Route::patch('/dashboard/event-status/{event:code}', [EventStatusController::class, 'update'])->name('dashboard.event-status.update');
    Route::get('/dashboard/iupc-slots', [IupcSlotController::class, 'index'])->name('dashboard.iupc-slots.index');
    Route::patch('/dashboard/iupc-slots', [IupcSlotController::class, 'updateSlots'])->name('dashboard.iupc-slots.update');
    Route::patch('/dashboard/iupc-slots/aliases/{alias}', [IupcSlotController::class, 'moveAlias'])->name('dashboard.iupc-slots.aliases.update');
    Route::post('/dashboard/iupc-slots/send-all-links', [IupcSlotController::class, 'sendAllLinks'])->name('dashboard.iupc-slots.send-all-links');
    Route::post('/dashboard/iupc-slots/{allocation}/send-links', [IupcSlotController::class, 'sendLinks'])->name('dashboard.iupc-slots.send-links');
    Route::patch('/dashboard/iupc-slots/links/{link}/disable', [IupcSlotController::class, 'disableLink'])->name('dashboard.iupc-slots.links.disable');
    Route::patch('/dashboard/iupc-slots/links/{link}/regenerate', [IupcSlotController::class, 'regenerateLink'])->name('dashboard.iupc-slots.links.regenerate');
    Route::get('/dashboard/iupc-bkash', [IupcBkashRecipientController::class, 'index'])->name('dashboard.iupc-bkash.index');
    Route::post('/dashboard/iupc-bkash', [IupcBkashRecipientController::class, 'store'])->name('dashboard.iupc-bkash.store');
    Route::patch('/dashboard/iupc-bkash/{recipient}', [IupcBkashRecipientController::class, 'update'])->name('dashboard.iupc-bkash.update');
    Route::patch('/dashboard/iupc-bkash/{recipient}/activate', [IupcBkashRecipientController::class, 'activate'])->name('dashboard.iupc-bkash.activate');
    Route::patch('/dashboard/iupc-bkash/{recipient}/deactivate', [IupcBkashRecipientController::class, 'deactivate'])->name('dashboard.iupc-bkash.deactivate');
    Route::patch('/dashboard/iupc-bkash/{recipient}/current', [IupcBkashRecipientController::class, 'current'])->name('dashboard.iupc-bkash.current');
    Route::delete('/dashboard/iupc-bkash/{recipient}', [IupcBkashRecipientController::class, 'destroy'])->name('dashboard.iupc-bkash.destroy');
    Route::get('/dashboard/events/{event:code}', [EventRegistrationController::class, 'index'])->name('dashboard.events.registrations.index');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/approve', [EventRegistrationController::class, 'approve'])->name('dashboard.events.registrations.approve');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/reject', [EventRegistrationController::class, 'reject'])->name('dashboard.events.registrations.reject');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/reject-final', [EventRegistrationController::class, 'rejectFinal'])->name('dashboard.events.registrations.reject-final');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/unapprove', [EventRegistrationController::class, 'unapprove'])->name('dashboard.events.registrations.unapprove');
    Route::resource('/dashboard/users', UserController::class)
        ->except(['create', 'show'])
        ->names('dashboard.users');
});

Route::get('/status', [RegistrationStatusController::class, 'index'])->name('registration.status');
Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');
Route::get('/final-reg/{registration_code}', [FinalRegistrationController::class, 'show'])->name('final-registration.show');
Route::post('/final-reg/{registration_code}', [FinalRegistrationController::class, 'store'])->name('final-registration.store');
Route::get('/iupc/coach/{token}', [IupcCoachPortalController::class, 'show'])->name('iupc.coach.show');
Route::post('/iupc/coach/{token}/teams/{registration}', [IupcCoachPortalController::class, 'submit'])->name('iupc.coach.teams.submit');

Route::get('/test-sms', function () {
    $url = config('services.bulk_sms.url');
    $apiKey = config('services.bulk_sms.api_key');
    $senderId = config('services.bulk_sms.sender_id');
    $number = '8801832560411';
    $message = 'Test SMS check from IUT ICT FEST';

    try {
        $response = Http::timeout(20)->asForm()->post($url, [
            'api_key' => $apiKey,
            'number' => $number,
            'senderid' => $senderId,
            'message' => $message,
        ]);

        return response()->json([
            'request' => [
                'url' => $url,
                'api_key_set' => filled($apiKey),
                'sender_id' => $senderId,
                'number' => $number,
                'message' => $message,
            ],
            'response' => [
                'http_status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
            ],
        ]);
    } catch (\Throwable $exception) {
        return response()->json([
            'request' => [
                'url' => $url,
                'api_key_set' => filled($apiKey),
                'sender_id' => $senderId,
                'number' => $number,
                'message' => $message,
            ],
            'error' => $exception->getMessage(),
        ], 500);
    }
});

Route::get('/{eventSlug}/rulebook', EventRulebookController::class)
    ->whereIn('eventSlug', ['iupc', 'hackathon', 'datathon', 'gamejam', 'fifa', 'valorant'])
    ->name('events.rulebook');

Route::get('/iupc', function () {
    return view('events.iupc', ['eventRecord' => \App\Models\Event::where('code', '01')->firstOrFail()]);
});

Route::get('/iupc/slots', function () {
    $allocations = IupcUniversityAllocation::query()
        ->where('is_active', true)
        ->where('slot_count', '>', 0)
        ->orderByDesc('slot_count')
        ->orderBy('name')
        ->get();

    return view('events.iupc-slots', [
        'allocations' => $allocations,
        'totalSlots' => $allocations->sum('slot_count'),
    ]);
})->name('iupc.slots');

Route::get('/iupc/register', [IupcRegistrationController::class, 'create'])->name('iupc.register');
Route::post('/iupc/register', [IupcRegistrationController::class, 'store'])->name('iupc.register.store');
Route::get('/iupc/register/{code}', [IupcRegistrationController::class, 'success'])->name('iupc.register.success');

Route::get('/hackathon', function () {
    return view('events.hackathon', ['eventRecord' => \App\Models\Event::where('code', '02')->firstOrFail()]);
});

Route::get('/hackathon/register', [HackathonRegistrationController::class, 'create'])->name('hackathon.register');
Route::post('/hackathon/register', [HackathonRegistrationController::class, 'store'])->name('hackathon.register.store');
Route::get('/hackathon/register/{code}', [HackathonRegistrationController::class, 'success'])->name('hackathon.register.success');

Route::get('/datathon', function () {
    return view('events.datathon', ['eventRecord' => \App\Models\Event::where('code', '03')->firstOrFail()]);
});

Route::get('/datathon/register', [DatathonRegistrationController::class, 'create'])->name('datathon.register');
Route::post('/datathon/register', [DatathonRegistrationController::class, 'store'])->name('datathon.register.store');
Route::get('/datathon/register/{code}', [DatathonRegistrationController::class, 'success'])->name('datathon.register.success');

Route::get('/gamejam', function () {
    return view('events.gamejam', ['eventRecord' => \App\Models\Event::where('code', '04')->firstOrFail()]);
});

Route::get('/gamejam/register', [GamejamRegistrationController::class, 'create'])->name('gamejam.register');
Route::post('/gamejam/register', [GamejamRegistrationController::class, 'store'])->name('gamejam.register.store');
Route::get('/gamejam/register/{code}', [GamejamRegistrationController::class, 'success'])->name('gamejam.register.success');

Route::get('/fifa', function () {
    return view('events.fifa', ['eventRecord' => \App\Models\Event::where('code', '05')->firstOrFail()]);
});

Route::get('/fifa/register', [FifaRegistrationController::class, 'create'])->name('fifa.register');
Route::post('/fifa/register', [FifaRegistrationController::class, 'store'])->name('fifa.register.store');
Route::get('/fifa/register/{code}', [FifaRegistrationController::class, 'success'])->name('fifa.register.success');

Route::get('/valorant', function () {
    return view('events.valorant', ['eventRecord' => \App\Models\Event::where('code', '06')->firstOrFail()]);
});

Route::get('/valorant/register', [ValorantRegistrationController::class, 'create'])->name('valorant.register');
Route::post('/valorant/register', [ValorantRegistrationController::class, 'store'])->name('valorant.register.store');
Route::get('/valorant/register/{code}', [ValorantRegistrationController::class, 'success'])->name('valorant.register.success');
