<?php

namespace App\Filament\Actions\NormalActions\TrainingGroupActions;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class PdfAction
{
    public static function make(): Action
    {
        return Action::make('printPDF')
            ->disabled(fn($record) => $record->status == 'finished')
            ->label('طباعة')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (Model $record) {
                // Generate PDF
                $mpdf = new Mpdf([
                    'default_font' => 'readexpro',
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'fontDir' => [
                        public_path('fonts/'), // Path to your font files
                        ...(new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir']
                    ],
                    'fontdata' => [
                        'readexpro' => [
                            'R' => 'ReadexPro-Regular.ttf',
                            'useOTL' => 0xFF,
                        ],
                    ],

                    'default_font_size' => 12,
                    'directionality' => 'ltr', // or 'rtl' if needed
                    'autoScriptToLang' => true, // Automatically detect language
                    'autoLangToFont' => true,   // Automatically switch font for language
                    'autoArabic' => true,       // Enable Arabic script processing
                    'useSubstitutions' => true, // Allow character substitutions
                    'useKashida' => 75,        // Kashida elongation percentage
                ]);

                $mpdf->SetDirectionality('rtl');

                // Get students for this training group
                $students = $record->students; // Adjust based on your relationship

                // HTML content
                $html = view('filament.pdf.students', [
                    'students' => $students,
                    'trainingGroup' => $record,
                ])->render();

                $mpdf->WriteHTML($html);

                // Output the PDF
                return response()->streamDownload(
                    fn() => $mpdf->Output(),
                    "{$record->name}.pdf"
                );
            });
    }
}
