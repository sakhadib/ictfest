<?php

namespace App\Http\Controllers;

use App\Models\OperationsPersonnel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonnelFindController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('find', [
            'query' => $query,
            'searched' => $query !== '',
            'personnel' => $query === '' ? collect() : $this->searchPersonnel($query),
        ]);
    }

    private function searchPersonnel(string $query)
    {
        $needle = $this->normalizeName($query);

        if ($needle === '') {
            return collect();
        }

        return OperationsPersonnel::query()
            ->where(function (Builder $builder) use ($needle): void {
                $builder->whereRaw('LOWER(name) LIKE ?', ['%'.$needle.'%']);
            })
            ->orderByRaw("
                CASE
                    WHEN LOWER(name) = ? THEN 0
                    WHEN LOWER(name) LIKE ? THEN 1
                    ELSE 2
                END
            ", [$needle, $needle.'%'])
            ->orderBy('name')
            ->limit(40)
            ->get()
            ->unique(fn (OperationsPersonnel $person): string => implode('|', [
                $this->normalizeName($person->name),
                preg_replace('/\D+/', '', (string) $person->phone),
                $this->normalizeName((string) $person->team),
            ]))
            ->values();
    }

    private function normalizeName(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }
}
