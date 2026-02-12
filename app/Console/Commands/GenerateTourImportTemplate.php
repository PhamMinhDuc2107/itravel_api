<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenerateTourImportTemplate extends Command
{
    protected $signature = 'tour:generate-template {--output= : Output file path}';

    protected $description = 'Generate a sample Excel template for tour import (Tours + Itineraries + Departures)';

    // ── Column definitions ───────────────────────────────────

    private function tourColumns(): array
    {
        return [
            ['code', 22, true],
            ['name', 40, true],
            ['slug', 40, false],
            ['category_id', 14, false],
            ['departure_location_id', 24, true],
            ['destination_location_id', 26, true],
            ['duration_days', 16, true],
            ['duration_nights', 16, false],
            ['price_adult', 16, true],
            ['price_child', 16, false],
            ['price_infant', 16, false],
            ['excerpt', 35, false],
            ['overview', 45, false],
            ['policy', 35, false],
            ['included', 30, false],
            ['excluded', 30, false],
            ['status', 12, false],
            ['meta_title', 30, false],
            ['meta_description', 40, false],
        ];
    }

    private function itineraryColumns(): array
    {
        return [
            ['tour_code', 22, true],
            ['day_number', 14, true],
            ['position', 12, false],
            ['title', 40, true],
            ['content', 60, false],
        ];
    }

    private function departureColumns(): array
    {
        return [
            ['tour_code', 22, true],
            ['start_date', 16, true],
            ['price_adult', 16, true],
            ['original_price_adult', 20, false],
            ['price_child', 16, false],
            ['original_price_child', 20, false],
            ['price_infant', 16, false],
            ['original_price_infant', 22, false],
            ['stock', 10, true],
            ['booked', 10, false],
            ['status', 14, false],
        ];
    }

    // ── Sample data ──────────────────────────────────────────

    private function tourSampleData(): array
    {
        return [
            ['TOUR-HNSP-3N2D', 'Tour Hà Nội - Sapa 3 ngày 2 đêm', 'tour-ha-noi-sapa-3n2d', 1, 1, 2, 3, 2, 3500000, 2500000, 0, 'Khám phá vẻ đẹp Sapa', 'Hành trình khám phá Sapa...', 'Hủy trước 7 ngày: hoàn 100%', 'Xe đưa đón, KS 3 sao, ăn, HDV', 'Vé máy bay, chi phí cá nhân', 1, 'Tour Hà Nội Sapa 3N2Đ', 'Tour du lịch Hà Nội - Sapa giá tốt'],
            ['TOUR-DLNT-4N3D', 'Tour Đà Lạt - Nha Trang 4 ngày 3 đêm', 'tour-da-lat-nha-trang-4n3d', 1, 3, 4, 4, 3, 4200000, 3000000, 500000, 'Thành phố hoa và biển', 'Tour 2 điểm đến miền Trung...', 'Hủy trước 5 ngày: hoàn 80%', 'Xe giường nằm, KS 4 sao', 'Vé tham quan, tip HDV', 1, 'Tour Đà Lạt Nha Trang 4N3Đ', 'Tour Đà Lạt Nha Trang trọn gói'],
            ['TOUR-PQ-5N4D', 'Tour Phú Quốc 5 ngày 4 đêm', 'tour-phu-quoc-5n4d', 2, 1, 5, 5, 4, 6800000, 5000000, 1000000, 'Nghỉ dưỡng biển đảo Phú Quốc', 'Resort 5 sao và hòn đảo ngọc...', 'Hủy trước 10 ngày: hoàn 100%', 'Vé MB, resort 5 sao, tour', 'Minibar, spa', 1, 'Tour Phú Quốc 5N4Đ', 'Tour Phú Quốc nghỉ dưỡng cao cấp'],
        ];
    }

    private function itinerarySampleData(): array
    {
        return [
            ['TOUR-HNSP-3N2D', 1, 1, 'Hà Nội - Lào Cai', 'Xuất phát từ Hà Nội bằng xe giường nằm, nghỉ đêm trên xe.'],
            ['TOUR-HNSP-3N2D', 2, 2, 'Sapa - Fansipan - Bản Cát Cát', 'Chinh phục đỉnh Fansipan, tham quan bản Cát Cát, thưởng thức ẩm thực địa phương.'],
            ['TOUR-HNSP-3N2D', 3, 3, 'Sapa - Hà Nội', 'Tham quan chợ Sapa, mua đặc sản, trở về Hà Nội.'],
            ['TOUR-DLNT-4N3D', 1, 1, 'Đà Lạt City Tour', 'Tham quan Hồ Xuân Hương, Dinh Bảo Đại, chợ đêm Đà Lạt.'],
            ['TOUR-DLNT-4N3D', 2, 2, 'Đà Lạt - Cổng trời', 'Khám phá cổng trời Đà Lạt, hái dâu, thưởng thức cà phê.'],
            ['TOUR-DLNT-4N3D', 3, 3, 'Đà Lạt - Nha Trang', 'Di chuyển đến Nha Trang, nhận phòng khách sạn, tắm biển.'],
            ['TOUR-DLNT-4N3D', 4, 4, 'Nha Trang - Vinpearl', 'Tham quan Vinpearl Land, lặn san hô, về Nha Trang.'],
            ['TOUR-PQ-5N4D', 1, 1, 'Sân bay - Phú Quốc', 'Đón tại sân bay, nhận phòng resort, nghỉ ngơi.'],
            ['TOUR-PQ-5N4D', 2, 2, 'Tour 4 đảo', 'Lặn ngắm san hô, câu cá, BBQ hải sản trên đảo.'],
            ['TOUR-PQ-5N4D', 3, 3, 'VinWonders - Safari', 'Khám phá VinWonders và Safari Phú Quốc.'],
            ['TOUR-PQ-5N4D', 4, 4, 'Bắc Đảo - Rạch Vẹm', 'Tham quan Rạch Vẹm, làng chài, mũi Gành Dầu.'],
            ['TOUR-PQ-5N4D', 5, 5, 'Free day - Sân bay', 'Tự do mua sắm, trả phòng, ra sân bay.'],
        ];
    }

    private function departureSampleData(): array
    {
        return [
            ['TOUR-HNSP-3N2D', '2026-03-15', 3500000, 4000000, 2500000, 3000000, 0, 0, 30, 5, 'available'],
            ['TOUR-HNSP-3N2D', '2026-03-22', 3500000, 4000000, 2500000, 3000000, 0, 0, 30, 0, 'available'],
            ['TOUR-HNSP-3N2D', '2026-04-05', 3800000, 4200000, 2700000, 3200000, 0, 0, 25, 0, 'available'],
            ['TOUR-DLNT-4N3D', '2026-03-20', 4200000, 5000000, 3000000, 3500000, 500000, 500000, 40, 12, 'available'],
            ['TOUR-DLNT-4N3D', '2026-04-10', 4500000, 5000000, 3200000, 3500000, 500000, 500000, 35, 0, 'available'],
            ['TOUR-PQ-5N4D', '2026-03-25', 6800000, 7500000, 5000000, 5500000, 1000000, 1200000, 20, 8, 'available'],
            ['TOUR-PQ-5N4D', '2026-04-15', 7200000, 8000000, 5200000, 6000000, 1000000, 1200000, 20, 0, 'available'],
        ];
    }

    // ── Guide content ────────────────────────────────────────

    private function guideContent(): array
    {
        return [
            'tours' => [
                ['code', 'Có', 'Mã tour duy nhất (VD: TOUR-HNSP-3N2D), tối đa 50 ký tự'],
                ['name', 'Có', 'Tên tour, tối đa 255 ký tự'],
                ['slug', 'Không', 'URL slug (tự tạo từ name nếu để trống)'],
                ['category_id', 'Không', 'ID danh mục (phải tồn tại trong DB)'],
                ['departure_location_id', 'Có', 'ID điểm khởi hành (phải tồn tại)'],
                ['destination_location_id', 'Có', 'ID điểm đến (phải tồn tại, khác departure)'],
                ['duration_days', 'Có', 'Số ngày (tối thiểu 1)'],
                ['duration_nights', 'Không', 'Số đêm (mặc định 0)'],
                ['price_adult', 'Có', 'Giá người lớn (VNĐ)'],
                ['price_child', 'Không', 'Giá trẻ em (mặc định 0)'],
                ['price_infant', 'Không', 'Giá em bé (mặc định 0)'],
                ['excerpt', 'Không', 'Mô tả ngắn'],
                ['overview', 'Không', 'Tổng quan chi tiết'],
                ['policy', 'Không', 'Chính sách hủy/đổi'],
                ['included', 'Không', 'Dịch vụ bao gồm'],
                ['excluded', 'Không', 'Dịch vụ không bao gồm'],
                ['status', 'Không', '0=Nháp, 1=Published, 2=Closed, 3=Hidden (mặc định: 0)'],
                ['meta_title', 'Không', 'SEO title (tối đa 255)'],
                ['meta_description', 'Không', 'SEO description (tối đa 500)'],
            ],
            'itineraries' => [
                ['tour_code', 'Có', 'Mã tour (phải khớp với cột code ở sheet Tours)'],
                ['day_number', 'Có', 'Ngày thứ mấy (bắt đầu từ 1)'],
                ['position', 'Không', 'Thứ tự sắp xếp (mặc định 0)'],
                ['title', 'Có', 'Tiêu đề lịch trình (VD: "Hà Nội - Sapa")'],
                ['content', 'Không', 'Nội dung chi tiết lịch trình trong ngày'],
            ],
            'departures' => [
                ['tour_code', 'Có', 'Mã tour (phải khớp với cột code ở sheet Tours)'],
                ['start_date', 'Có', 'Ngày khởi hành (định dạng: YYYY-MM-DD)'],
                ['price_adult', 'Có', 'Giá bán người lớn'],
                ['original_price_adult', 'Không', 'Giá gốc người lớn (để hiện giảm giá)'],
                ['price_child', 'Không', 'Giá bán trẻ em'],
                ['original_price_child', 'Không', 'Giá gốc trẻ em'],
                ['price_infant', 'Không', 'Giá bán em bé'],
                ['original_price_infant', 'Không', 'Giá gốc em bé'],
                ['stock', 'Có', 'Số chỗ tổng cộng'],
                ['booked', 'Không', 'Số chỗ đã đặt (mặc định 0)'],
                ['status', 'Không', 'available / sold_out / closed (mặc định: available)'],
            ],
        ];
    }

    // ── Main handle ──────────────────────────────────────────

    public function handle(): int
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Tours (nhập liệu)
        $toursSheet = $spreadsheet->getActiveSheet();
        $toursSheet->setTitle('Tours');
        $this->applyHeadingRow($toursSheet, $this->tourColumns());
        $this->fillSampleData($toursSheet, $this->tourColumns(), $this->tourSampleData());
        $toursSheet->freezePane('A2');

        // Sheet 2: Lịch trình
        $itinSheet = $spreadsheet->createSheet();
        $itinSheet->setTitle('Lịch trình');
        $this->applyHeadingRow($itinSheet, $this->itineraryColumns());
        $this->fillSampleData($itinSheet, $this->itineraryColumns(), $this->itinerarySampleData());
        $itinSheet->freezePane('A2');

        // Sheet 3: Ngày khởi hành
        $depSheet = $spreadsheet->createSheet();
        $depSheet->setTitle('Ngày khởi hành');
        $this->applyHeadingRow($depSheet, $this->departureColumns());
        $this->fillSampleData($depSheet, $this->departureColumns(), $this->departureSampleData());
        $depSheet->freezePane('A2');

        // Sheet 4: Hướng dẫn
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle('Hướng dẫn');
        $this->buildGuideSheet($guideSheet);

        // Default to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        // Write
        $outputPath = $this->option('output')
            ?? storage_path('app/templates/tour_import_template.xlsx');

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        $this->info("✅ Template created: {$outputPath}");
        $this->info("   Sheet 1: Tours (3 mẫu)");
        $this->info("   Sheet 2: Lịch trình (12 mẫu)");
        $this->info("   Sheet 3: Ngày khởi hành (7 mẫu)");
        $this->info("   Sheet 4: Hướng dẫn");

        return self::SUCCESS;
    }

    // ── Helpers ──────────────────────────────────────────────

    private function applyHeadingRow(Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $colIndex => [$label, $width, $required]) {
            $col = $this->colLetter($colIndex);
            $cell = $col . '1';

            $sheet->setCellValue($cell, $label);
            $sheet->getColumnDimension($col)->setWidth($width);

            $fillColor = $required ? '1F4E79' : '4472C4';
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);
    }

    private function fillSampleData(Worksheet $sheet, array $columns, array $data): void
    {
        foreach ($data as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $lastCol = $this->colLetter(count($columns) - 1);

            foreach ($row as $colIndex => $value) {
                $sheet->setCellValue($this->colLetter($colIndex) . $excelRow, $value);
            }

            $bgColor = ($rowIndex % 2 === 0) ? 'FFFFFF' : 'F5F5F5';
            $sheet->getStyle("A{$excelRow}:{$lastCol}{$excelRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
        }
    }

    private function buildGuideSheet(Worksheet $sheet): void
    {
        // Title
        $sheet->setCellValue('A1', 'HƯỚNG DẪN IMPORT TOUR');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E79']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'File gồm 3 sheet dữ liệu: Tours, Lịch trình, Ngày khởi hành. Dữ liệu liên kết qua cột "tour_code".');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $currentRow = 4;
        $guide = $this->guideContent();
        $sections = [
            'Sheet 1: TOURS' => $guide['tours'],
            'Sheet 2: LỊCH TRÌNH' => $guide['itineraries'],
            'Sheet 3: NGÀY KHỞI HÀNH' => $guide['departures'],
        ];

        foreach ($sections as $sectionTitle => $rows) {
            // Section header
            $sheet->setCellValue("A{$currentRow}", $sectionTitle);
            $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;

            // Table headers
            foreach (['Tên cột', 'Bắt buộc', 'Mô tả chi tiết'] as $ci => $h) {
                $sheet->setCellValue(chr(65 + $ci) . $currentRow, $h);
            }
            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D6E4F0']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $currentRow++;

            // Data
            foreach ($rows as $row) {
                foreach ($row as $ci => $val) {
                    $sheet->setCellValue(chr(65 + $ci) . $currentRow, $val);
                }
                $isRequired = $row[1] === 'Có';
                $bgColor = $isRequired ? 'DBEEF4' : 'FFFFFF';
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                ]);
                $currentRow++;
            }

            $currentRow++; // Spacer between sections
        }

        // Notes
        $currentRow++;
        $notes = [
            '📌 LƯU Ý QUAN TRỌNG:',
            '• Hàng đầu tiên (heading) là BẮT BUỘC — KHÔNG xóa hoặc đổi tên cột.',
            '• Cột có header màu xanh đậm là BẮT BUỘC có dữ liệu.',
            '• Cột "tour_code" ở sheet Lịch trình và Ngày khởi hành phải KHỚP với cột "code" ở sheet Tours.',
            '• Hệ thống đọc dữ liệu theo THỨ TỰ SHEET: Tours → Lịch trình → Ngày khởi hành.',
            '• Ảnh (image, gallery) KHÔNG import qua Excel — upload riêng qua API update tour.',
            '• File hỗ trợ: .xlsx, .xls, .csv — tối đa 10MB.',
        ];
        foreach ($notes as $i => $note) {
            $sheet->setCellValue("A{$currentRow}", $note);
            $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
            $style = ['font' => ['size' => 10, 'color' => ['rgb' => '333333']]];
            if ($i === 0) {
                $style['font'] = ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'CC0000']];
            }
            $sheet->getStyle("A{$currentRow}")->applyFromArray($style);
            $currentRow++;
        }

        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(65);
    }

    private function colLetter(int $index): string
    {
        return chr(65 + $index);
    }
}
