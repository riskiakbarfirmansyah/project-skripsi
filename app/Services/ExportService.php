<?php

namespace App\Services;

use App\Models\informasisiaran;
use App\Models\narasumberbyphone;
use App\Models\narasumbertalkshow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Header background colour (teal).
     */
    private const HEADER_BG = '0F766E';

    /**
     * Title background colour (darker teal).
     */
    private const TITLE_BG = '0D5C56';

    /**
     * Meta/info background colour (light grey).
     */
    private const META_BG = 'F3F4F6';

    /**
     * Date format for cells.
     */
    private const DATE_FORMAT = 'd-m-Y H:i';

    /**
     * Date-only format for cells.
     */
    private const DATE_ONLY_FORMAT = 'd-m-Y';

    /**
     * Resolve the start-of-period based on `period` query param.
     *
     * @param  string  $period  week | month | year
     */
    private function startDate(string $period): Carbon
    {
        return match ($period) {
            'week'  => Carbon::now()->subDays(7)->startOfDay(),
            'year'  => Carbon::now()->subDays(365)->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(), // month
        };
    }

    /**
     * Get human-readable period label in Bahasa Indonesia.
     */
    private function periodLabel(string $period): string
    {
        return match ($period) {
            'week'  => '7 Hari Terakhir (Mingguan)',
            'year'  => '365 Hari Terakhir (Tahunan)',
            default => '30 Hari Terakhir (Bulanan)',
        };
    }

    /**
     * Get period date range description.
     */
    private function periodRange(string $period): string
    {
        $start = $this->startDate($period);
        $end   = Carbon::now();

        $start->locale('id');
        $end->locale('id');

        return $start->translatedFormat('d F Y') . '  s/d  ' . $end->translatedFormat('d F Y');
    }

    /**
     * Get period prefix for filename (Mingguan / Bulanan / Tahunan).
     */
    private function periodFilenamePrefix(string $period): string
    {
        return match ($period) {
            'week'  => 'Mingguan',
            'year'  => 'Tahunan',
            default => 'Bulanan',
        };
    }

    /**
     * Apply shared header style to a range, e.g. "A1:G1".
     */
    private function applyHeaderStyle(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::HEADER_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
    }

    /**
     * Apply data-row style to a range.
     */
    private function applyDataStyle(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_TOP,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
    }

    /**
     * Auto-fit all used columns in a sheet.
     */
    private function autoFitColumns(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Write report title section above the data header.
     *
     * Row 1: Report title (merged, bold, white on dark teal)
     * Row 2: Period label (merged)
     * Row 3: Period date range (merged)
     * Row 4: Blank separator
     * Row 5: Data headers (the previous row 1)
     *
     * @param  string  $reportTitle   e.g. "LAPORAN DATA NARASUMBER BYPHONE"
     * @param  string  $period        week | month | year
     * @param  string  $lastCol       Last column letter e.g. "H"
     * @return int     The row number where data headers are placed (always 5)
     */
    private function writeReportHeader(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $reportTitle,
        string $period,
        string $lastCol
    ): int {
        // ── Row 1: Report Title ──────────────────────────────────────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $reportTitle);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 14,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::TITLE_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ── Row 2: Period Label ──────────────────────────────────────
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Periode: ' . $this->periodLabel($period));
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '374151'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::META_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ── Row 3: Date Range ────────────────────────────────────────
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'Rentang Tanggal: ' . $this->periodRange($period));
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => [
                'size'  => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::META_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ── Row 4: Blank separator ───────────────────────────────────
        $sheet->getRowDimension(4)->setRowHeight(8);

        // Data headers will start at row 5
        return 5;
    }

    /**
     * Write export metadata at the bottom of the data (file name + export datetime).
     *
     * @param  int     $startRow  The row number to start writing metadata
     * @param  string  $filename  The filename of the export
     * @param  string  $lastCol   Last column letter e.g. "H"
     */
    private function writeExportFooter(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int $startRow,
        string $filename,
        string $lastCol
    ): void {
        // Blank separator row
        $sheet->getRowDimension($startRow)->setRowHeight(8);
        $footerRow1 = $startRow + 1;
        $footerRow2 = $startRow + 2;

        // ── Footer Row 1: Nama File ──────────────────────────────────
        $sheet->mergeCells("A{$footerRow1}:{$lastCol}{$footerRow1}");
        $sheet->setCellValue("A{$footerRow1}", 'Nama File: ' . $filename);
        $sheet->getRowDimension($footerRow1)->setRowHeight(18);
        $sheet->getStyle("A{$footerRow1}:{$lastCol}{$footerRow1}")->applyFromArray([
            'font' => [
                'italic' => true,
                'size'   => 9,
                'color'  => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ── Footer Row 2: Tanggal Export ─────────────────────────────
        $sheet->mergeCells("A{$footerRow2}:{$lastCol}{$footerRow2}");
        $exportDate = Carbon::now()->locale('id');
        $sheet->setCellValue("A{$footerRow2}", 'Tanggal Export: ' . $exportDate->translatedFormat('d F Y, H:i:s') . ' WIB');
        $sheet->getRowDimension($footerRow2)->setRowHeight(18);
        $sheet->getStyle("A{$footerRow2}:{$lastCol}{$footerRow2}")->applyFromArray([
            'font' => [
                'italic' => true,
                'size'   => 9,
                'color'  => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // GATEKEEPER EXPORTS (individual)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build and stream an Excel file with only Narasumber Byphone data.
     * Sheet 1: Narasumber Byphone (8 cols – includes Jadwal Byphone)
     */
    public function exportNarasumberByphone(string $period): StreamedResponse
    {
        $start = $this->startDate($period);

        $byphoneData = narasumberbyphone::where('waktu_wawancara', '>=', $start)
            ->orderBy('waktu_wawancara')
            ->get();

        $scheduleLabels = [
            'pendidikan_kota_kita' => 'Pendidikan Kota Kita',
            'youth_speak_up'       => 'Youth Speak Up',
            'layanan_publik'       => 'Layanan Publik',
            'anda_dan_keluarga'    => 'Anda dan Keluarga',
            'dinkes_menyapa'       => 'Dinkes Menyapa',
            'komunitas_bicara'     => 'Komunitas Bicara',
            'indie_vibes'          => 'Indie Vibes',
            'kojah_sastra'         => 'Kojah Sastra',
            'inspirasi_bisnis'     => 'Inspirasi Bisnis',
            'obrolan_agama_islam'  => 'Obrolan Seputar Agama Islam',
            'ngosasi'              => 'Ngosasi (Ngobrol Asik Sana Sini)',
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Export Narasumber Byphone')
            ->setCreator('Radio Management System');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nrs Byphone_' . Carbon::now()->format('Y-m-d'));

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow = $this->writeReportHeader($sheet, 'LAPORAN DATA NARASUMBER BYPHONE', $period, 'H');

        // ── Data headers at row 5 ────────────────────────────────────
        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Byphone', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, "A{$headerRow}");
        $this->applyHeaderStyle($sheet, "A{$headerRow}:H{$headerRow}");
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $dataStartRow = $headerRow + 1; // row 6
        foreach ($byphoneData as $idx => $row) {
            $rowNum = $dataStartRow + $idx;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->byphone_schedule ? ($scheduleLabels[$row->byphone_schedule] ?? $row->byphone_schedule) : '');
            $sheet->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastDataRow = max($dataStartRow, $dataStartRow + $byphoneData->count() - 1);
        if ($byphoneData->count() > 0) {
            $this->applyDataStyle($sheet, "A{$dataStartRow}:H{$lastDataRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet->getColumnDimension('H')->setWidth(40);

        $filename = $this->periodFilenamePrefix($period) . '_NarasumberByphone_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        // ── Export footer ────────────────────────────────────────────
        $footerStartRow = $lastDataRow + 2;
        $this->writeExportFooter($sheet, $footerStartRow, $filename, 'H');

        return $this->streamResponse($spreadsheet, $filename);
    }

    /**
     * Build and stream an Excel file with only Informasi Siaran data.
     * Sheet 1: Informasi Siaran (4 cols)
     */
    public function exportInformasiSiaran(string $period): StreamedResponse
    {
        $start = $this->startDate($period);

        $siaranData = informasisiaran::where('tanggal', '>=', $start->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Export Informasi Siaran')
            ->setCreator('Radio Management System');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Info Siaran_' . Carbon::now()->format('Y-m-d'));

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow = $this->writeReportHeader($sheet, 'LAPORAN DATA INFORMASI SIARAN', $period, 'D');

        // ── Data headers at row 5 ────────────────────────────────────
        $headers = ['No', 'Tanggal', 'Jam Mulai', 'Keterangan'];
        $sheet->fromArray($headers, null, "A{$headerRow}");
        $this->applyHeaderStyle($sheet, "A{$headerRow}:D{$headerRow}");
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $dataStartRow = $headerRow + 1; // row 6
        foreach ($siaranData as $idx => $row) {
            $rowNum = $dataStartRow + $idx;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", Carbon::parse($row->tanggal)->format(self::DATE_ONLY_FORMAT));
            $sheet->setCellValue("C{$rowNum}", $row->waktu);
            $sheet->setCellValue("D{$rowNum}", $row->keterangan);
        }

        $lastDataRow = max($dataStartRow, $dataStartRow + $siaranData->count() - 1);
        if ($siaranData->count() > 0) {
            $this->applyDataStyle($sheet, "A{$dataStartRow}:D{$lastDataRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D']);
        $sheet->getColumnDimension('D')->setWidth(40);

        $filename = $this->periodFilenamePrefix($period) . '_InformasiSiaran_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        // ── Export footer ────────────────────────────────────────────
        $footerStartRow = $lastDataRow + 2;
        $this->writeExportFooter($sheet, $footerStartRow, $filename, 'D');

        return $this->streamResponse($spreadsheet, $filename);
    }

    /**
     * Build and stream an Excel file with only Narasumber Talkshow data.
     * Sheet 1: Narasumber Talkshow (8 cols – includes Jadwal Talkshow)
     */
    public function exportNarasumberTalkshow(string $period): StreamedResponse
    {
        $start = $this->startDate($period);

        $talkshowData = narasumbertalkshow::where('waktu_wawancara', '>=', $start)
            ->orderBy('waktu_wawancara')
            ->get();

        $scheduleLabels = [
            'pendidikan_kota_kita' => 'Pendidikan Kota Kita',
            'youth_speak_up'       => 'Youth Speak Up',
            'layanan_publik'       => 'Layanan Publik',
            'anda_dan_keluarga'    => 'Anda dan Keluarga',
            'dinkes_menyapa'       => 'Dinkes Menyapa',
            'komunitas_bicara'     => 'Komunitas Bicara',
            'indie_vibes'          => 'Indie Vibes',
            'kojah_sastra'         => 'Kojah Sastra',
            'inspirasi_bisnis'     => 'Inspirasi Bisnis',
            'obrolan_agama_islam'  => 'Obrolan Seputar Agama Islam',
            'ngosasi'              => 'Ngosasi (Ngobrol Asik Sana Sini)',
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Export Narasumber Talkshow')
            ->setCreator('Radio Management System');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nrs Talkshow_' . Carbon::now()->format('Y-m-d'));

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow = $this->writeReportHeader($sheet, 'LAPORAN DATA NARASUMBER TALKSHOW', $period, 'H');

        // ── Data headers at row 5 ────────────────────────────────────
        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Talkshow', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, "A{$headerRow}");
        $this->applyHeaderStyle($sheet, "A{$headerRow}:H{$headerRow}");
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $dataStartRow = $headerRow + 1; // row 6
        foreach ($talkshowData as $idx => $row) {
            $rowNum = $dataStartRow + $idx;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->talkshow_schedule ? ($scheduleLabels[$row->talkshow_schedule] ?? $row->talkshow_schedule) : '');
            $sheet->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastDataRow = max($dataStartRow, $dataStartRow + $talkshowData->count() - 1);
        if ($talkshowData->count() > 0) {
            $this->applyDataStyle($sheet, "A{$dataStartRow}:H{$lastDataRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet->getColumnDimension('H')->setWidth(40);

        $filename = $this->periodFilenamePrefix($period) . '_NarasumberTalkshow_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        // ── Export footer ────────────────────────────────────────────
        $footerStartRow = $lastDataRow + 2;
        $this->writeExportFooter($sheet, $footerStartRow, $filename, 'H');

        return $this->streamResponse($spreadsheet, $filename);
    }

    // ─────────────────────────────────────────────────────────────────
    // GATEKEEPER EXPORT (2 sheets – legacy, kept for compatibility)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build and stream an Excel file for Gatekeeper.
     * Sheet 1: Narasumber Byphone  (8 cols)
     * Sheet 2: Informasi Siaran    (4 cols)
     */
    public function exportGatekeeper(string $period): StreamedResponse
    {
        $start = $this->startDate($period);

        $byphoneData = narasumberbyphone::where('waktu_wawancara', '>=', $start)
            ->orderBy('waktu_wawancara')
            ->get();

        $siaranData = informasisiaran::where('tanggal', '>=', $start->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Export Gatekeeper')
            ->setCreator('Radio Management System');

        $filename = $this->periodFilenamePrefix($period) . '_Gatekeeper_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        // ── Sheet 1: Narasumber Byphone ──────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Nrs Byphone_' . Carbon::now()->format('Y-m-d'));

        $byphoneScheduleLabels = [
            'pendidikan_kota_kita' => 'Pendidikan Kota Kita',
            'youth_speak_up'       => 'Youth Speak Up',
            'layanan_publik'       => 'Layanan Publik',
            'anda_dan_keluarga'    => 'Anda dan Keluarga',
            'dinkes_menyapa'       => 'Dinkes Menyapa',
            'komunitas_bicara'     => 'Komunitas Bicara',
            'indie_vibes'          => 'Indie Vibes',
            'kojah_sastra'         => 'Kojah Sastra',
            'inspirasi_bisnis'     => 'Inspirasi Bisnis',
            'obrolan_agama_islam'  => 'Obrolan Seputar Agama Islam',
            'ngosasi'              => 'Ngosasi (Ngobrol Asik Sana Sini)',
        ];

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow1 = $this->writeReportHeader($sheet1, 'LAPORAN DATA NARASUMBER BYPHONE', $period, 'H');

        $headers1 = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Byphone', 'Draft Pertanyaan'];
        $sheet1->fromArray($headers1, null, "A{$headerRow1}");
        $this->applyHeaderStyle($sheet1, "A{$headerRow1}:H{$headerRow1}");
        $sheet1->getRowDimension($headerRow1)->setRowHeight(22);

        $dataStartRow1 = $headerRow1 + 1;
        foreach ($byphoneData as $idx => $row) {
            $rowNum = $dataStartRow1 + $idx;
            $sheet1->setCellValue("A{$rowNum}", $idx + 1);
            $sheet1->setCellValue("B{$rowNum}", $row->nama);
            $sheet1->setCellValue("C{$rowNum}", $row->instansi);
            $sheet1->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet1->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet1->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet1->setCellValue("G{$rowNum}", $row->byphone_schedule ? ($byphoneScheduleLabels[$row->byphone_schedule] ?? $row->byphone_schedule) : '');
            $sheet1->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastDataRow1 = max($dataStartRow1, $dataStartRow1 + $byphoneData->count() - 1);
        if ($byphoneData->count() > 0) {
            $this->applyDataStyle($sheet1, "A{$dataStartRow1}:H{$lastDataRow1}");
        }
        $this->autoFitColumns($sheet1, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet1->getColumnDimension('H')->setWidth(40);

        // ── Export footer sheet 1 ────────────────────────────────────
        $footerStartRow1 = $lastDataRow1 + 2;
        $this->writeExportFooter($sheet1, $footerStartRow1, $filename, 'H');

        // ── Sheet 2: Informasi Siaran ────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Info Siaran_' . Carbon::now()->format('Y-m-d'));

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow2 = $this->writeReportHeader($sheet2, 'LAPORAN DATA INFORMASI SIARAN', $period, 'D');

        $headers2 = ['No', 'Tanggal', 'Jam Mulai', 'Keterangan'];
        $sheet2->fromArray($headers2, null, "A{$headerRow2}");
        $this->applyHeaderStyle($sheet2, "A{$headerRow2}:D{$headerRow2}");
        $sheet2->getRowDimension($headerRow2)->setRowHeight(22);

        $dataStartRow2 = $headerRow2 + 1;
        foreach ($siaranData as $idx => $row) {
            $rowNum = $dataStartRow2 + $idx;
            $sheet2->setCellValue("A{$rowNum}", $idx + 1);
            $sheet2->setCellValue("B{$rowNum}", Carbon::parse($row->tanggal)->format(self::DATE_ONLY_FORMAT));
            $sheet2->setCellValue("C{$rowNum}", $row->waktu);
            $sheet2->setCellValue("D{$rowNum}", $row->keterangan);
        }

        $lastDataRow2 = max($dataStartRow2, $dataStartRow2 + $siaranData->count() - 1);
        if ($siaranData->count() > 0) {
            $this->applyDataStyle($sheet2, "A{$dataStartRow2}:D{$lastDataRow2}");
        }
        $this->autoFitColumns($sheet2, ['A', 'B', 'C', 'D']);
        $sheet2->getColumnDimension('D')->setWidth(40);

        // ── Export footer sheet 2 ────────────────────────────────────
        $footerStartRow2 = $lastDataRow2 + 2;
        $this->writeExportFooter($sheet2, $footerStartRow2, $filename, 'D');

        // Activate first sheet
        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamResponse($spreadsheet, $filename);
    }

    // ─────────────────────────────────────────────────────────────────
    // PJ PROGRAM EXPORT (1 sheet)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build and stream an Excel file for PJ Program.
     * Sheet 1: Narasumber Talkshow (7 cols)
     */
    public function exportPjProgram(string $period): StreamedResponse
    {
        $start = $this->startDate($period);

        $talkshowData = narasumbertalkshow::where('waktu_wawancara', '>=', $start)
            ->orderBy('waktu_wawancara')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Export PJ Program')
            ->setCreator('Radio Management System');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nrs Talkshow_' . Carbon::now()->format('Y-m-d'));

        // ── Report header (rows 1–4) ─────────────────────────────────
        $headerRow = $this->writeReportHeader($sheet, 'LAPORAN DATA NARASUMBER TALKSHOW', $period, 'G');

        // ── Data headers at row 5 ────────────────────────────────────
        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, "A{$headerRow}");
        $this->applyHeaderStyle($sheet, "A{$headerRow}:G{$headerRow}");
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $dataStartRow = $headerRow + 1;
        foreach ($talkshowData as $idx => $row) {
            $rowNum = $dataStartRow + $idx;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastDataRow = max($dataStartRow, $dataStartRow + $talkshowData->count() - 1);
        if ($talkshowData->count() > 0) {
            $this->applyDataStyle($sheet, "A{$dataStartRow}:G{$lastDataRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G']);
        $sheet->getColumnDimension('G')->setWidth(40);

        $filename = $this->periodFilenamePrefix($period) . '_PjProgram_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        // ── Export footer ────────────────────────────────────────────
        $footerStartRow = $lastDataRow + 2;
        $this->writeExportFooter($sheet, $footerStartRow, $filename, 'G');

        return $this->streamResponse($spreadsheet, $filename);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────────────────────────

    private function streamResponse(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
