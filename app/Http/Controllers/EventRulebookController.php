<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventRulebookController extends Controller
{
    private const SLUG_TO_CODE = [
        'iupc' => '01',
        'hackathon' => '02',
        'datathon' => '03',
        'gamejam' => '04',
        'fifa' => '05',
        'valorant' => '06',
    ];

    public function __invoke(Request $request, string $eventSlug): View
    {
        abort_unless(array_key_exists($eventSlug, self::SLUG_TO_CODE), 404);

        return view('events.rulebook', [
            'eventSlug' => $eventSlug,
            'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
        ]);
    }
}
