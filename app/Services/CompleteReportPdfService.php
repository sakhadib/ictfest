<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

class CompleteReportPdfService
{
    /**
     * @param array<string, mixed> $report
     */
    public function renderToTempFile(array $report, string $prefix = 'ictfest-full-report-'): string
    {
        $html = view('reports.complete-pdf', [
            'report' => $report,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $path = tempnam(sys_get_temp_dir(), $prefix);

        if (! $path) {
            throw new RuntimeException('Could not create a temporary complete report file.');
        }

        $pdfPath = $path.'.pdf';
        rename($path, $pdfPath);

        $bytesWritten = file_put_contents($pdfPath, $dompdf->output());

        if ($bytesWritten === false) {
            throw new RuntimeException('Could not write the temporary complete report file.');
        }

        return $pdfPath;
    }
}
