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

        if ($eventSlug === 'hackathon') {
            return view('events.rulebooks.hackathon', [
                'eventSlug' => $eventSlug,
                'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
            ]);
        }

        if ($eventSlug === 'datathon') {
            return view('events.rulebooks.datathon', [
                'eventSlug' => $eventSlug,
                'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
            ]);
        }

        if ($eventSlug === 'gamejam') {
            return view('events.rulebooks.gamejam', [
                'eventSlug' => $eventSlug,
                'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
            ]);
        }

        if ($eventSlug === 'valorant') {
            return view('events.rulebooks.valorant', [
                'eventSlug' => $eventSlug,
                'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
            ]);
        }

        return view('events.rulebook', [
            'eventSlug' => $eventSlug,
            'eventRecord' => Event::where('code', self::SLUG_TO_CODE[$eventSlug])->firstOrFail(),
        ]);
    }
}
