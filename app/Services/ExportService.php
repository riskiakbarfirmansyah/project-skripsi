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
        $sheet->setTitle('Narasumber Byphone');

        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Byphone', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:H1');
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($byphoneData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->byphone_schedule ? ($scheduleLabels[$row->byphone_schedule] ?? $row->byphone_schedule) : '');
            $sheet->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastRow = max(2, $byphoneData->count() + 1);
        if ($byphoneData->count() > 0) {
            $this->applyDataStyle($sheet, "A2:H{$lastRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet->getColumnDimension('H')->setWidth(40);

        $filename = 'Export_NarasumberByphone_' . Carbon::now()->format('Y-m-d') . '.xlsx';

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
        $sheet->setTitle('Informasi Siaran');

        $headers = ['No', 'Tanggal', 'Jam Mulai', 'Keterangan'];
        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:D1');
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($siaranData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", Carbon::parse($row->tanggal)->format(self::DATE_ONLY_FORMAT));
            $sheet->setCellValue("C{$rowNum}", $row->waktu);
            $sheet->setCellValue("D{$rowNum}", $row->keterangan);
        }

        $lastRow = max(2, $siaranData->count() + 1);
        if ($siaranData->count() > 0) {
            $this->applyDataStyle($sheet, "A2:D{$lastRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D']);
        $sheet->getColumnDimension('D')->setWidth(40);

        $filename = 'Export_InformasiSiaran_' . Carbon::now()->format('Y-m-d') . '.xlsx';

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
        $sheet->setTitle('Narasumber Talkshow');

        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Talkshow', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:H1');
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($talkshowData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->talkshow_schedule ? ($scheduleLabels[$row->talkshow_schedule] ?? $row->talkshow_schedule) : '');
            $sheet->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastRow = max(2, $talkshowData->count() + 1);
        if ($talkshowData->count() > 0) {
            $this->applyDataStyle($sheet, "A2:H{$lastRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet->getColumnDimension('H')->setWidth(40);

        $filename = 'Export_NarasumberTalkshow_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return $this->streamResponse($spreadsheet, $filename);
    }

    // ─────────────────────────────────────────────────────────────────
    // GATEKEEPER EXPORT (2 sheets – legacy, kept for compatibility)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build and stream an Excel file for Gatekeeper.
     * Sheet 1: Narasumber Byphone  (7 cols)
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

        // ── Sheet 1: Narasumber Byphone ──────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Narasumber Byphone');

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

        $headers1 = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Jadwal Byphone', 'Draft Pertanyaan'];
        $sheet1->fromArray($headers1, null, 'A1');
        $this->applyHeaderStyle($sheet1, 'A1:H1');
        $sheet1->getRowDimension(1)->setRowHeight(22);

        foreach ($byphoneData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet1->setCellValue("A{$rowNum}", $idx + 1);
            $sheet1->setCellValue("B{$rowNum}", $row->nama);
            $sheet1->setCellValue("C{$rowNum}", $row->instansi);
            $sheet1->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet1->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet1->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet1->setCellValue("G{$rowNum}", $row->byphone_schedule ? ($byphoneScheduleLabels[$row->byphone_schedule] ?? $row->byphone_schedule) : '');
            $sheet1->setCellValue("H{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastRow1 = max(2, $byphoneData->count() + 1);
        if ($byphoneData->count() > 0) {
            $this->applyDataStyle($sheet1, "A2:H{$lastRow1}");
        }
        $this->autoFitColumns($sheet1, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $sheet1->getColumnDimension('H')->setWidth(40);

        // ── Sheet 2: Informasi Siaran ────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Informasi Siaran');

        $headers2 = ['No', 'Tanggal', 'Jam Mulai', 'Keterangan'];
        $sheet2->fromArray($headers2, null, 'A1');
        $this->applyHeaderStyle($sheet2, 'A1:D1');
        $sheet2->getRowDimension(1)->setRowHeight(22);

        foreach ($siaranData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet2->setCellValue("A{$rowNum}", $idx + 1);
            $sheet2->setCellValue("B{$rowNum}", Carbon::parse($row->tanggal)->format(self::DATE_ONLY_FORMAT));
            $sheet2->setCellValue("C{$rowNum}", $row->waktu);
            $sheet2->setCellValue("D{$rowNum}", $row->keterangan);
        }

        $lastRow2 = max(2, $siaranData->count() + 1);
        if ($siaranData->count() > 0) {
            $this->applyDataStyle($sheet2, "A2:D{$lastRow2}");
        }
        $this->autoFitColumns($sheet2, ['A', 'B', 'C', 'D']);
        $sheet2->getColumnDimension('D')->setWidth(40);

        // Activate first sheet
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Export_Gatekeeper_' . Carbon::now()->format('Y-m-d') . '.xlsx';

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
        $sheet->setTitle('Narasumber Talkshow');

        $headers = ['No', 'Nama', 'Instansi', 'Nomor Telepon', 'Tema Wawancara', 'Waktu Wawancara', 'Draft Pertanyaan'];
        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:G1');
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($talkshowData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row->nama);
            $sheet->setCellValue("C{$rowNum}", $row->instansi);
            $sheet->setCellValue("D{$rowNum}", $row->nomor_telp);
            $sheet->setCellValue("E{$rowNum}", $row->tema_wawancara);
            $sheet->setCellValue("F{$rowNum}", Carbon::parse($row->waktu_wawancara)->format(self::DATE_FORMAT));
            $sheet->setCellValue("G{$rowNum}", $row->draft_pertanyaan ?? '');
        }

        $lastRow = max(2, $talkshowData->count() + 1);
        if ($talkshowData->count() > 0) {
            $this->applyDataStyle($sheet, "A2:G{$lastRow}");
        }
        $this->autoFitColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G']);
        $sheet->getColumnDimension('G')->setWidth(40);

        $filename = 'Export_PjProgram_' . Carbon::now()->format('Y-m-d') . '.xlsx';

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
