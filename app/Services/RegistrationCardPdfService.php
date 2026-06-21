<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

class RegistrationCardPdfService
{
    /**
     * @param array<string, mixed> $report
     */
    public function renderToTempFile(array $report, string $prefix = 'ictfest-regcards-'): string
    {
        $html = view('telegram.registration-cards-pdf', [
            'report' => $report,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $path = tempnam(sys_get_temp_dir(), $prefix);

        if (! $path) {
            throw new RuntimeException('Could not create a temporary registration card file.');
        }

        $pdfPath = $path.'.pdf';
        rename($path, $pdfPath);

        $bytesWritten = file_put_contents($pdfPath, $dompdf->output());

        if ($bytesWritten === false) {
            throw new RuntimeException('Could not write the temporary registration card file.');
        }

        return $pdfPath;
    }
}
