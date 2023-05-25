<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk;

use MyParcelCom\ApiSdk\Exceptions\LabelCombinerException;
use MyParcelCom\ApiSdk\Resources\File;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use setasign\Fpdi\Fpdi;

class LabelCombiner implements LabelCombinerInterface
{
    const LABEL_WIDTH = 420.94;
    const LABEL_HEIGHT = 297.64;

    public function combineLabels(
        array $files,
        string $pageSize = self::PAGE_SIZE_A4,
        int $startLocation = self::LOCATION_TOP_LEFT,
        int $margin = 0
    ): FileInterface {
        [$labelsPerRow, $labelsPerColumn] = $this->getLabelsPerPage($pageSize);
        $labelsPerPage = $labelsPerRow * $labelsPerColumn;

        $pdf = new Fpdi(
            $this->getOrientation($pageSize),
            'pt',
            $pageSize === self::PAGE_SIZE_A6
                ? [self::LABEL_HEIGHT, self::LABEL_WIDTH]
                : $pageSize
        );

        // If we're not starting at position 0, add a page, because it won't be added in the loop.
        if (($labelPosition = $this->getStartPosition($pageSize, $startLocation)) > 0) {
            $pdf->AddPage();
        }

        foreach ($files as $file) {
            // Whenever we the max number of labels is on a page, create a new page.
            if ($labelPosition % $labelsPerPage === 0) {
                $pdf->AddPage();
            }

            [$x, $y, $width] = $this->calculateDimensions($labelPosition, $labelsPerRow, $labelsPerColumn, $margin);

            $formats = $file->getFormats();
            $format = reset($formats);
            if ($format[FileInterface::FORMAT_MIME_TYPE] !== FileInterface::MIME_TYPE_PDF) {
                // Add the image (label) to the pdf.
                $pdf->Image($file->getTemporaryFilePath(), $x, $y, $width);
            } else {
                // Add the page (label) to the pdf.
                $pdf->setSourceFile($file->getTemporaryFilePath());
                $pageId = $pdf->importPage(1);
                $pdf->useTemplate($pageId, $x, $y, $width);
            }

            $labelPosition++;
        }

        return (new File())
            ->addFormat(FileInterface::MIME_TYPE_PDF, 'pdf')
            ->setBase64Data(
                base64_encode($pdf->Output('S')),
                FileInterface::MIME_TYPE_PDF
            );
    }

    /**
     * Get an array with the number of labels per row and column for given page size.
     */
    private function getLabelsPerPage(string $pageSize): array
    {
        return match ($pageSize) {
            self::PAGE_SIZE_A4 => [2, 2],
            self::PAGE_SIZE_A5 => [1, 2],
            self::PAGE_SIZE_A6 => [1, 1],
            default => throw new LabelCombinerException('unknown page size: ' . $pageSize),
        };
    }

    /**
     * Get the orientation for given page size.
     */
    private function getOrientation(string $pageSize): string
    {
        return match ($pageSize) {
            self::PAGE_SIZE_A4, self::PAGE_SIZE_A6 => 'L',
            self::PAGE_SIZE_A5 => 'P',
            default => throw new LabelCombinerException('unknown page size: ' . $pageSize),
        };
    }

    /**
     * Return the start position, 0 being top left and 3 being bottom right.
     */
    private function getStartPosition(string $pageSize, int $startLocation): int
    {
        $top = $startLocation & self::LOCATION_TOP;
        $left = $startLocation & self::LOCATION_LEFT;

        return match ($pageSize) {
            self::PAGE_SIZE_A4 => ($top ? 0 : 2) + ($left ? 0 : 1),
            self::PAGE_SIZE_A5 => $top ? 0 : 1,
            self::PAGE_SIZE_A6 => 0,
            default => throw new LabelCombinerException('unknown page size: ' . $pageSize),
        };
    }

    /**
     * Get an array with the x position, y position and width of the label on given label position.
     */
    private function calculateDimensions(
        int $labelPosition,
        int $labelsPerRow,
        int $labelsPerColumn,
        int $margin,
    ): array {
        // The x position should be the margin, plus the label width for every label printed on the right side.
        $x = $margin + ($labelPosition % $labelsPerRow) * self::LABEL_WIDTH;

        // The y position should be the margin scaled to the margin for x (√2:2), plus the label height if the label is on the bottom.
        $y = $margin * sqrt(2) / 2 + (floor($labelPosition / $labelsPerRow) % $labelsPerColumn) * self::LABEL_HEIGHT;

        // The width is the label width, minus twice the margin (1 for each side).
        $width = self::LABEL_WIDTH - $margin * 2;

        return [
            $x,
            $y,
            $width,
        ];
    }
}
