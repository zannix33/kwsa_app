<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Generates billing workbooks by populating the supplied Kingwizard workbook.
 * The source workbook is deliberately used as the template so its merged cells,
 * widths, heights, borders, fonts, number formats and page setup stay intact.
 */
class BillingExcelPreviewExport
{
    private const TEMPLATE = 'resources/templates/kingwizard-billing-template.xlsx';
    private const FIRST_GUARD_ROW = 14;
    private const TEMPLATE_GUARD_ROWS = 17; // rows 14 through 30 in the supplied workbook
    private const TOTAL_ROW = 31;

    public function __construct(private readonly array $viewData)
    {
    }

    public function download(string $filename): StreamedResponse
    {
        $templatePath = base_path(self::TEMPLATE);
        abort_unless(is_file($templatePath), 500, 'The Kingwizard billing Excel template is missing.');

        $spreadsheet = IOFactory::load($templatePath);
        $areas = $this->collection($this->viewData['areas'] ?? []);
        abort_if($areas->isEmpty(), 404, 'There is no billing data to export.');

        $firstSheet = $spreadsheet->getSheet(0);
        $templateSheet = clone $firstSheet; // keep an untouched copy of the original layout
        $usedFirstSheet = false;
        $sheetNames = [];
        $sheetIndex = 0;
        $companies = $this->collection($this->viewData['companies'] ?? []);

        foreach ($areas->values() as $area) {
            $areaBranches = $this->collection($area['branches'] ?? []);
            $companyGroups = $areaBranches->groupBy(fn ($branch) => (string) ($branch['company_id'] ?? 'unassigned'));

            foreach ($companyGroups as $companyId => $groupedBranches) {
                if (!$usedFirstSheet) {
                    $sheet = $firstSheet;
                    $usedFirstSheet = true;
                } else {
                    $sheet = clone $templateSheet;
                    $sheet->setTitle('Template-' . ($sheetIndex + 1));
                    $spreadsheet->addSheet($sheet);
                }
                $sheetIndex++;

                $branches = $this->collection($groupedBranches->values());
                $guards = [];
                foreach ($branches as $branch) {
                    foreach (($branch['guards'] ?? []) as $guard) {
                        $guards[] = ['branch' => $branch, 'guard' => $guard];
                    }
                }

                $company = $companies->get($companyId);
                $this->populateSheet($sheet, $area, $branches, $guards, $company);

                $companyLabel = data_get($company, 'name');
                $baseTitle = trim((string) ($area['area_name'] ?? ('Area ' . $sheetIndex)));
                if ($companyLabel) {
                    $baseTitle .= ' - ' . $companyLabel;
                }
                $baseTitle = preg_replace('/[\\\\\/\?\*\[\]:]/', '-', $baseTitle) ?: 'Billing';
                $baseTitle = mb_substr($baseTitle, 0, 31);
                $title = $baseTitle;
                $suffix = 2;
                while (isset($sheetNames[$title])) {
                    $ending = '-' . $suffix++;
                    $title = mb_substr($baseTitle, 0, 31 - mb_strlen($ending)) . $ending;
                }
                $sheetNames[$title] = true;
                $sheet->setTitle($title);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getCalculationEngine()->disableCalculationCache();
        $spreadsheet->getProperties()->setCreator('Kingwizard Security Agency');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(true);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    private function populateSheet(Worksheet $sheet, array $area, Collection $branches, array $guards, mixed $company): void
    {
        $count = count($guards);
        $extraRows = max(0, $count - self::TEMPLATE_GUARD_ROWS);
        $unusedRows = max(0, self::TEMPLATE_GUARD_ROWS - $count);

        if ($extraRows > 0) {
            $sheet->insertNewRowBefore(self::TOTAL_ROW, $extraRows);
            // New rows inherit the exact detail-row formatting from the template.
            for ($row = self::TOTAL_ROW; $row < self::TOTAL_ROW + $extraRows; $row++) {
                $sourceRow = self::TOTAL_ROW - 1;
                $sheet->duplicateStyle($sheet->getStyle("A{$sourceRow}:AJ{$sourceRow}"), "A{$row}:AJ{$row}");
                $sheet->getRowDimension($row)->setRowHeight($sheet->getRowDimension($sourceRow)->getRowHeight() ?: 14.25);
            }
        } elseif ($unusedRows > 0) {
            $sheet->removeRow(self::FIRST_GUARD_ROW + $count, $unusedRows);
        }

        $totalRow = self::TOTAL_ROW + $extraRows - $unusedRows;
        $summaryGrossRow = $totalRow + 2;
        $summaryEwtRow = $totalRow + 3;
        $summaryNetRow = $totalRow + 4;

        $firstGuard = $guards[0]['guard'] ?? [];
        $firstBranch = $guards[0]['branch'] ?? [];
        $firstContract = $firstGuard['contract_cost'] ?? [];
        $dailyRate = (float) ($firstGuard['billing_daily_rate'] ?? 0);
        $monthlyRate = (float) ($firstContract['contract_cost_excluding_retirement'] ?? 0);
        $retirement = (float) ($firstContract['retirement_contribution'] ?? 0);

        $period = $this->viewData['period'] ?? null;
        $dateFrom = data_get($period, 'date_from');
        $dateTo = data_get($period, 'date_to');
        $periodLabel = $dateFrom && $dateTo
            ? date('F d', strtotime((string) $dateFrom)) . ' - ' . date('d, Y', strtotime((string) $dateTo))
            : (string) data_get($period, 'name', '');

        // Keep the template's original header coordinates and merged-cell layout.
        $sheet->setCellValue('A1', 'KINGWIZARD SECURITY AGENCY');
        $sheet->setCellValue('C2', data_get($company, 'name', $firstBranch['company_name'] ?? ''));
        $sheet->setCellValue('C3', data_get($company, 'vat_registration_number', ''));
        $sheet->setCellValue('C4', data_get($company, 'account_number', ''));
        $sheet->setCellValue('C5', $periodLabel);
        $sheet->setCellValue('C7', $area['area_name'] ?? '');
        $sheet->setCellValue('C8', 'REG/OT');
        $sheet->setCellValue('L4', $dailyRate);
        $sheet->setCellValue('L5', $monthlyRate);
        $sheet->setCellValue('L6', $retirement);
        $sheet->setCellValue('L7', '=L5*20%');
        $sheet->setCellValue('L8', '=L5+L6+L7');
        $sheet->setCellValue('M8', '(non VAT)');
        $sheet->setCellValue('D13', '=L5/2');
        $sheet->setCellValue('F13', '=$L$5*12/365');
        $sheet->setCellValue('H13', '=L4/8*125%');
        $sheet->setCellValue('J13', '=L4/8*169%');
        $sheet->setCellValue('L13', '=L4/8*260%');
        $sheet->setCellValue('N13', '=H13*10%');
        $sheet->setCellValue('P13', '=J13*10%');
        $sheet->setCellValue('R13', '=L13*10%');
        $sheet->setCellValue('T13', '=L8*12/365/8');
        $sheet->setCellValue('X13', '=L6/2');
        $sheet->setCellValue('AA13', 200);

        // Clear the sample guard names and values while leaving all template styles intact.
        for ($row = self::FIRST_GUARD_ROW; $row < self::FIRST_GUARD_ROW + $count; $row++) {
            for ($column = 1; $column <= 36; $column++) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($column) . $row, null);
            }
        }

        foreach ($guards as $index => $item) {
            $row = self::FIRST_GUARD_ROW + $index;
            $branch = $item['branch'];
            $guard = $item['guard'];
            $billing = $guard['billing'] ?? [];
            $previousBranch = $guards[$index - 1]['branch'] ?? null;
            $sameBranch = $previousBranch !== null
                && (($previousBranch['branch_id'] ?? null) !== null || ($branch['branch_id'] ?? null) !== null)
                    ? (string) ($previousBranch['branch_id'] ?? '') === (string) ($branch['branch_id'] ?? '')
                    : (($previousBranch['branch_name'] ?? null) === ($branch['branch_name'] ?? null));
            $branchName = ($index === 0 || !$sameBranch) ? ($branch['branch_name'] ?? '') : '';

            $sheet->setCellValue("A{$row}", $branchName);
            $sheet->setCellValue("B{$row}", $guard['guard_name'] ?? '');
            $guardCount = (float) ($billing['guard_count'] ?? 1);
            $sheet->setCellValue("C{$row}", $guardCount > 0 ? $guardCount : null);
            $sheet->setCellValue("D{$row}", "=\$D\$13*C{$row}");
            $sheet->setCellValue("E{$row}", (float) ($billing['additional_post_days'] ?? 0));
            $sheet->setCellValue("F{$row}", "=\$F\$13*E{$row}");
            $sheet->setCellValue("G{$row}", (float) ($guard['regular_ot_hours'] ?? 0));
            $sheet->setCellValue("H{$row}", "=\$H\$13*G{$row}");
            $sheet->setCellValue("I{$row}", (float) ($guard['sunday_special_ot_hours'] ?? 0));
            $sheet->setCellValue("J{$row}", "=\$J\$13*I{$row}");
            $sheet->setCellValue("K{$row}", (float) ($guard['regular_holiday_ot_hours'] ?? 0));
            $sheet->setCellValue("L{$row}", "=\$L\$13*K{$row}");
            $sheet->setCellValue("M{$row}", (float) ($guard['regular_nd_hours'] ?? 0));
            $sheet->setCellValue("N{$row}", "=\$N\$13*M{$row}");
            $sheet->setCellValue("O{$row}", (float) ($guard['sunday_special_nd_hours'] ?? 0));
            $sheet->setCellValue("P{$row}", "=\$P\$13*O{$row}");
            $sheet->setCellValue("Q{$row}", (float) ($guard['legal_holiday_nd_hours'] ?? 0));
            $sheet->setCellValue("R{$row}", "=\$R\$13*Q{$row}");
            $sheet->setCellValue("S{$row}", (float) ($billing['deduction_hours'] ?? ((float) ($guard['late_hours'] ?? 0) + (float) ($guard['undertime_hours'] ?? 0))));
            $sheet->setCellValue("T{$row}", "=\$T\$13*S{$row}");
            $sheet->setCellValue("U{$row}", "=D{$row}+F{$row}+H{$row}+J{$row}+L{$row}+N{$row}+P{$row}+R{$row}-T{$row}");
            $sheet->setCellValue("V{$row}", "=U{$row}*20%");
            $sheet->setCellValue("W{$row}", "=V{$row}*0.02");
            $sheet->setCellValue("X{$row}", "=\$X\$13*C{$row}");
            $sheet->setCellValue("Y{$row}", "=U{$row}+V{$row}-W{$row}+X{$row}");
            $sheet->setCellValue("Z{$row}", (float) ($billing['penalty_hours'] ?? 0));
            $sheet->setCellValue("AA{$row}", "=\$AA\$13*Z{$row}");
            $sheet->setCellValue("AB{$row}", "=Y{$row}-AA{$row}");
            $sheet->setCellValue("AD{$row}", "=D{$row}+F{$row}-T{$row}-AA{$row}+X{$row}");
            $sheet->setCellValue("AE{$row}", "=H{$row}+J{$row}+L{$row}+N{$row}+P{$row}+R{$row}");
            $sheet->setCellValue("AF{$row}", "=V{$row}");
            $sheet->setCellValue("AG{$row}", "=-W{$row}");
            $sheet->setCellValue("AH{$row}", "=AD{$row}+AE{$row}+AF{$row}+AG{$row}");
        }

        // One overall area total; there are intentionally no branch subtotal rows.
        $sumColumns = ['C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AD','AE','AF','AG','AH'];
        $lastGuardRow = self::FIRST_GUARD_ROW + $count - 1;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        foreach ($sumColumns as $column) {
            $sheet->setCellValue("{$column}{$totalRow}", $count > 0 ? "=SUM({$column}" . self::FIRST_GUARD_ROW . ":{$column}{$lastGuardRow})" : '=0');
        }

        // Retain the template's GROSS / EWT / NET summary block and formulas.
        $sheet->setCellValue("L{$summaryGrossRow}", 'GROSS');
        $sheet->setCellValue("N{$summaryGrossRow}", "=Y{$totalRow}");
        $sheet->setCellValue("L{$summaryEwtRow}", 'EWT');
        $sheet->setCellValue("N{$summaryEwtRow}", "=W{$totalRow}");
        $sheet->setCellValue("L{$summaryNetRow}", 'NET');
        $sheet->setCellValue("N{$summaryNetRow}", "=AB{$totalRow}");

        // The source workbook has a note in A33; keep it at the corresponding final row.
        $sheet->setCellValue("A{$summaryGrossRow}", 'NOTE: For additional rows, the worksheet expands automatically.');
        $sheet->mergeCells("A{$summaryGrossRow}:J{$summaryGrossRow}");
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->setShowGridlines(false);
    }

    private function collection(mixed $value): Collection
    {
        if ($value instanceof Collection) {
            return $value;
        }
        return collect($value ?? []);
    }
}
