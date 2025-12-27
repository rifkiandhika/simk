<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\TagihanPo;

class TagihanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    protected $tagihan;
    protected $tab;
    protected $rowNumber = 0;

    /**
     * Constructor
     */
    public function __construct($tagihan, $tab)
    {
        $this->tagihan = $tagihan;
        $this->tab = $tab;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->tagihan;
    }

    /**
     * Define headings
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. TAGIHAN',
            'NO. PO',
            'SUPPLIER',
            'TANGGAL TAGIHAN',
            'TANGGAL JATUH TEMPO',
            'GRAND TOTAL',
            'TOTAL DIBAYAR',
            'SISA TAGIHAN',
            'STATUS',
        ];
    }

    /**
     * Map data to columns
     * @param mixed $tagihan
     * @return array
     */
    public function map($tagihan): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $tagihan->no_tagihan,
            $tagihan->purchaseOrder->no_gr ?? '-',
            $tagihan->purchaseOrder->supplier->nama_supplier ?? '-',
            \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d/m/Y'),
            $tagihan->tanggal_jatuh_tempo
                ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d/m/Y')
                : '-',
            $tagihan->grand_total,
            $tagihan->total_dibayar,
            $tagihan->sisa_tagihan,
            $this->getStatusLabel($tagihan->status),
        ];
    }

    /**
     * Get status label in Indonesian
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dibayar_sebagian' => 'Dibayar Sebagian',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Dibatalkan',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Apply styles to worksheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->tagihan->count() + 1;

        // Style untuk header (row 1)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style untuk data rows
        $sheet->getStyle('A2:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alignment untuk kolom tertentu
        // Center: NO, Tanggal, Status
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align: Nominal
        $sheet->getStyle('G2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format number untuk kolom rupiah (G, H, I)
        $sheet->getStyle('G2:I' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Zebra stripes untuk data
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':J' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }
        }

        return [];
    }

    /**
     * Define column widths
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 18,  // NO. TAGIHAN
            'C' => 18,  // NO. PO
            'D' => 30,  // SUPPLIER
            'E' => 16,  // TANGGAL TAGIHAN
            'F' => 18,  // TANGGAL JATUH TEMPO
            'G' => 18,  // GRAND TOTAL
            'H' => 18,  // TOTAL DIBAYAR
            'I' => 18,  // SISA TAGIHAN
            'J' => 20,  // STATUS
        ];
    }

    /**
     * Register events
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $this->tagihan->count() + 1;
                $summaryRow = $lastRow + 2;

                // Add summary section
                $event->sheet->setCellValue('A' . $summaryRow, 'RINGKASAN');
                $event->sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);

                $event->sheet->getStyle('A' . $summaryRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

                // Summary data
                $summaryRow++;
                $event->sheet->setCellValue('A' . $summaryRow, 'Total Tagihan:');
                $event->sheet->setCellValue('B' . $summaryRow, $this->tagihan->count() . ' tagihan');

                $summaryRow++;
                $event->sheet->setCellValue('A' . $summaryRow, 'Total Grand Total:');
                $event->sheet->setCellValue('B' . $summaryRow, 'Rp ' . number_format($this->tagihan->sum('grand_total'), 0, ',', '.'));

                $summaryRow++;
                $event->sheet->setCellValue('A' . $summaryRow, 'Total Dibayar:');
                $event->sheet->setCellValue('B' . $summaryRow, 'Rp ' . number_format($this->tagihan->sum('total_dibayar'), 0, ',', '.'));

                $summaryRow++;
                $event->sheet->setCellValue('A' . $summaryRow, 'Total Outstanding:');
                $event->sheet->setCellValue('B' . $summaryRow, 'Rp ' . number_format($this->tagihan->sum('sisa_tagihan'), 0, ',', '.'));

                $event->sheet->getStyle('B' . ($summaryRow - 3) . ':B' . $summaryRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Add footer
                $footerRow = $summaryRow + 2;
                $event->sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . now()->format('d F Y H:i:s'));
                $event->sheet->mergeCells('A' . $footerRow . ':J' . $footerRow);
                $event->sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color' => ['rgb' => '666666'],
                        'size' => 9,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }

    /**
     * Sheet title
     * @return string
     */
    public function title(): string
    {
        return 'Tagihan ' . ucfirst($this->tab);
    }
}
