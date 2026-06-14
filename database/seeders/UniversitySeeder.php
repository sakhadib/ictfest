<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;
use SplFileObject;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = public_path('universities.csv');
        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $headers = null;

        foreach ($file as $row) {
            if (! is_array($row) || $row === [null] || count(array_filter($row, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(
                    fn (string $header) => trim($header),
                    $row,
                );

                continue;
            }

            $data = array_combine($headers, $row);

            if (! is_array($data)) {
                continue;
            }

            $name = $this->normalizeValue($data['University Name'] ?? null);

            if ($name === null) {
                continue;
            }

            University::updateOrCreate(
                ['university_name' => $name],
                [
                    'acronym' => $this->normalizeValue($data['Acronym'] ?? null),
                    'estd' => $this->normalizeInteger($data['ESTD'] ?? null),
                    'type' => $this->normalizeValue($data['Type'] ?? null),
                    'location' => $this->normalizeValue($data['Location'] ?? null),
                    'specialization' => $this->normalizeValue($data['Specialization'] ?? null),
                    'website' => $this->normalizeWebsite($data['Website'] ?? null),
                ],
            );
        }
    }

    private function normalizeValue(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        if ($value === null || $value === '' || strcasecmp($value, 'NaN') === 0) {
            return null;
        }

        return $value;
    }

    private function normalizeWebsite(mixed $value): ?string
    {
        $value = $this->normalizeValue($value);

        if ($value === null) {
            return null;
        }

        return $value;
    }

    private function normalizeInteger(mixed $value): ?int
    {
        $value = $this->normalizeValue($value);

        return $value === null ? null : (int) $value;
    }
}
