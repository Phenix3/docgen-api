<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PdfGeneratorService
{
    public function __construct(
        private Pdf $knpSnappyPdf,
        private LoggerInterface $logger,
        #[Autowire('%env(WKHTMLTOPDF_PATH)%')] private string $wkhtmltopdfPath
    ) {
        // Ensure binary path is set correctly from env
        if ($this->wkhtmltopdfPath && file_exists($this->wkhtmltopdfPath)) {
            $this->knpSnappyPdf->setBinary($this->wkhtmltopdfPath);
        } else {
             $this->logger->warning("WKHTMLTOPDF binary not found at configured path: {$this->wkhtmltopdfPath}. Using default from KnpSnappy.");
        }
    }

    public function generatePdfFromHtml(string $html, string $outputPath): void
    {
        try {
            $this->knpSnappyPdf->generateFromHtml($html, $outputPath, [], true);
            $this->logger->info("PDF generated successfully at: {$outputPath}");
        } catch (\Exception $e) {
            $this->logger->error("Error generating PDF: " . $e->getMessage());
            throw $e;
        }
    }
}
