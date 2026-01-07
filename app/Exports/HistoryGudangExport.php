<?php

namespace App\Exports;

use App\Models\HistoryGudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Carbon;

class HistoryGudangExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected $gudangId;
    protected $tanggalMulai;
    protected $tanggalAkhir;
    protected $status;
    protected $rowNumber = 0;

    public function __construct($gudangId, $tanggalMulai = null, $tanggalAkhir = null, $status = null)
    {
        $this->gudangId = $gudangId;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->status = $status;
    }

    /**
     * Get collection data
     */
    public function collection()
    {
        $query = HistoryGudang::with(['supplier', 'barang', 'gudang', 'purchaseOrder'])
            ->where('gudang_id', $this->gudangId);

        // Filter tanggal
        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereBetween('waktu_proses', [
                Carbon::parse($this->tanggalMulai)->startOfDay(),
                Carbon::parse($this->tanggalAkhir)->endOfDay()
            ]);
        }

        // Filter status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('waktu_proses', 'desc')->get();
    }

    /**
     * Set headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Waktu Proses',
            'Status',
            'Tipe Referensi',
            'No. Referensi',
            'No. Batch',
            'Nama Barang',
            'Jumlah',
            'Supplier',
            'Keterangan'
        ];
    }

    /**
     * Map data to columns
     */
    public function map($history): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $history->waktu_proses->format('d/m/Y H:i:s'),
            $history->status == 'penerimaan' ? 'PENERIMAAN' : 'PENGIRIMAN',
            $history->referensi_type ?? '-',
            $history->no_referensi ?? '-',
            $history->no_batch ?? '-',
            $history->barang->nama ?? '-',
            $history->jumlah,
            $history->supplier->nama_supplier ?? '-',
            $history->keterangan ?? '-'
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        $gudang = \App\Models\Gudang::find($this->gudangId);

        // Merge cells untuk header
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        // Set values untuk header
        $sheet->setCellValue('A1', 'LAPORAN HISTORY GUDANG');
        $sheet->setCellValue('A2', strtoupper($gudang->nama_gudang ?? 'Gudang'));
        $sheet->setCellValue(
            'A3',
            'Periode: ' .
                ($this->tanggalMulai ? Carbon::parse($this->tanggalMulai)->format('d/m/Y') : '-') .
                ' s/d ' .
                ($this->tanggalAkhir ? Carbon::parse($this->tanggalAkhir)->format('d/m/Y') : '-')
        );

        // Style untuk header utama
        $sheet->getStyle('A1:J3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style untuk header kolom (baris 5)
        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        return [];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 18,  // Waktu Proses
            'C' => 15,  // Status
            'D' => 18,  // Tipe Referensi
            'E' => 18,  // No. Referensi
            'F' => 15,  // No. Batch
            'G' => 30,  // Nama Barang
            'H' => 12,  // Jumlah
            'I' => 25,  // Supplier
            'J' => 30   // Keterangan
        ];
    }

    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'History Gudang';
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Add borders to all data cells
                $sheet->getStyle('A5:J' . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Center align untuk kolom tertentu
                $sheet->getStyle('A6:A' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('C6:C' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('H6:H' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Auto row height
                foreach (range(1, $highestRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                // Add summary section
                $summaryRow = $highestRow + 2;
                $sheet->mergeCells('A' . $summaryRow . ':G' . $summaryRow);
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL PENERIMAAN:');
                $sheet->setCellValue('H' . $summaryRow, '=SUMIF(C6:C' . $highestRow . ',"PENERIMAAN",H6:H' . $highestRow . ')');

                $summaryRow++;
                $sheet->mergeCells('A' . $summaryRow . ':G' . $summaryRow);
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL PENGIRIMAN:');
                $sheet->setCellValue('H' . $summaryRow, '=SUMIF(C6:C' . $highestRow . ',"PENGIRIMAN",H6:H' . $highestRow . ')');

                // Style summary
                $sheet->getStyle('A' . ($highestRow + 2) . ':H' . ($summaryRow))
                    ->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E7E6E6']
                        ]
                    ]);

                // Add footer
                $footerRow = $summaryRow + 3;
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true);
            }
        ];
    }
}
