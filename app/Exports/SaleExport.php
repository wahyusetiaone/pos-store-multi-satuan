<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SaleExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $sales;
    protected $row = 0;
    protected $startRow = 1;
    protected $mergeCells = [];
    protected $paymentMethods = ['cash', 'transfer', 'pending'];
    protected $sheetRows = [];
    protected $isOwner;

    public function __construct($sales)
    {
        // Group sales by payment method and maintain original collection
        $this->sales = $sales;
        $this->isOwner = auth()->user()->hasRole('owner');

        // Initialize sheetRows for each payment method
        foreach ($this->paymentMethods as $method) {
            $this->sheetRows[$method] = [
                'row' => 0,
                'mergeCells' => []
            ];
        }
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        $this->row = 1;
        $headers = [
            'Tanggal',
            'No Invoice',
            'Toko',
            'Pelanggan',
            'Produk',
            'Qty',
            'Harga',
            'Diskon Item',
            'Subtotal',
            'Total Diskon',
            'Total Bayar'
        ];

        if ($this->isOwner) {
            $headers[] = 'Profit/Loss';
        }

        $headers = array_merge($headers, [
            'Metode Pembayaran',
            'Kasir'
        ]);

        return $headers;
    }

    public function map($sale): array
    {
        $rows = [];
        $itemCount = count($sale->items);
        $paymentMethod = $sale->payment_method;
        $totalProfitLoss = 0;

        // Calculate total profit loss for this invoice
        if ($itemCount > 1) {
            $totalProfitLoss = $sale->items->sum('unit_profit_loss');
        }

        $this->sheetRows[$paymentMethod]['row']++;
        $currentRow = $this->sheetRows[$paymentMethod]['row'];

        foreach ($sale->items as $index => $item) {
            if ($index === 0 && $itemCount > 1) {
                $mergeColumns = ['A', 'B', 'C', 'D'];
                if ($this->isOwner) {
                    $mergeColumns = ['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M', 'N'];
                } else {
                    $mergeColumns = ['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M'];
                }

                $this->sheetRows[$paymentMethod]['mergeCells'][] = [
                    'start_row' => $currentRow + 1,
                    'end_row' => $currentRow + $itemCount,
                    'columns' => $mergeColumns
                ];
            }

            $row = [
                $sale->sale_date->format('d/m/Y H:i'),
                $sale->id,
                $sale->store->name ?? '-',
                $sale->customer->name ?? 'Umum',
                $item->product->name,
                $item->quantity,
                $item->price,
                $item->discount,
                $item->subtotal,
                $sale->discount,
                $sale->paid
            ];

            if ($this->isOwner) {
                // If multiple items, show total profit/loss on first row only
                if ($itemCount > 1) {
                    $row[] = $index === 0 ? $totalProfitLoss : null;
                } else {
                    $row[] = $item->unit_profit_loss;
                }
            }

            $row = array_merge($row, [
                ucfirst($paymentMethod),
                $sale->user->name ?? '-'
            ]);

            $rows[] = $row;

            if ($index > 0) {
                $this->sheetRows[$paymentMethod]['row']++;
            }
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();

                // Apply merge cells for the first sheet (cash)
                $this->formatSheet($event->sheet);
                $this->applyMergeCells($event->sheet, $this->sales);
                $worksheet->setTitle('Semua Penjualan');

                // Create new sheets for other payment methods
                foreach ($this->paymentMethods as $method) {

                    // Skip if there's no data for this payment method
                    $methodSales = $this->sales->where('payment_method', $method);
                    if ($methodSales->isEmpty()) {
                        continue;
                    }

                    // Create new sheet
                    $newSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($event->sheet->getDelegate()->getParent(), 'Pembayaran ' . ucfirst($method));
                    $event->sheet->getDelegate()->getParent()->addSheet($newSheet);

                    // Copy data and apply formatting
                    $this->copyDataToSheet($newSheet, $methodSales);
                    $this->formatSheet($newSheet);
                    $this->applyMergeCells($newSheet, $methodSales);
                }
            },
        ];
    }

    private function copyDataToSheet($sheet, $sales)
    {
        // Set headers
        foreach ($this->headings() as $index => $heading) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $heading);
        }

        $row = 2; // Start after headers

        foreach ($sales as $sale) {
            $startRow = $row;
            $itemCount = count($sale->items);
            $totalProfitLoss = 0;

            // Calculate total profit loss for multiple items
            if ($itemCount > 1) {
                $totalProfitLoss = $sale->items->sum('unit_profit_loss');
            }

            foreach ($sale->items as $index => $item) {
                $col = 1;
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->sale_date->format('d/m/Y H:i'));
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->id);
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->store->name ?? '-');
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->customer->name ?? 'Umum');
                $sheet->setCellValueByColumnAndRow($col++, $row, $item->product->name);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item->quantity);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item->price);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item->discount);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item->subtotal);
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->discount);
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->paid);

                if ($this->isOwner) {
                    // If multiple items, show total profit/loss on first row only
                    if ($itemCount > 1) {
                        $profitLoss = $index === 0 ? $totalProfitLoss : null;
                    } else {
                        $profitLoss = $item->unit_profit_loss;
                    }
                    $sheet->setCellValueByColumnAndRow($col++, $row, $profitLoss);
                }

                $sheet->setCellValueByColumnAndRow($col++, $row, ucfirst($sale->payment_method));
                $sheet->setCellValueByColumnAndRow($col++, $row, $sale->user->name ?? '-');
                $row++;
            }
        }
    }

    private function applyMergeCells($sheet, $sales)
    {
        $row = 2; // Start after headers
        foreach ($sales as $sale) {
            $itemCount = count($sale->items);

            if ($itemCount > 1) {
                // Columns to merge: A (Tanggal), B (No Invoice), C (Toko), D (Pelanggan),
                // J (Total Diskon), K (Total Bayar), L (Metode Pembayaran), M (Kasir)
                $columns = ['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M'];

                foreach ($columns as $column) {
                    $startCell = $column . $row;
                    $endCell = $column . ($row + $itemCount - 1);
                    $sheet->mergeCells($startCell . ':' . $endCell);
                }
            }

            $row += $itemCount;
        }
    }

    private function formatSheet($sheet)
    {
        $lastColumn = $this->isOwner ? 'N' : 'M';
        $lastRow = $sheet->getHighestRow();
        $range = 'A1:' . $lastColumn . $lastRow;

        // Apply borders to all cells
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Left align product names
        $sheet->getStyle('E2:E'.$lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Right align numbers
        if ($this->isOwner) {
            $sheet->getStyle('F2:L'.$lastRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->getStyle('F2:K'.$lastRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Format number columns
        $numberColumns = $this->isOwner ? range('G', 'L') : range('G', 'K');
        foreach($numberColumns as $column) {
            $sheet->getStyle($column.'2:'.$column.$lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        // Style the header
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto size columns
        foreach(range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
