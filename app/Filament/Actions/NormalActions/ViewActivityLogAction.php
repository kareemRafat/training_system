<?php

namespace App\Filament\Actions\NormalActions;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ViewActivityLogAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->slideOver()
            ->modalHeading(fn (Model $record) => "سجل الأنشطة الخاصة بـ {$record->name}")
            ->modalContent(function (Model $record) {
                return view('filament.pages.userLogs', [
                    'activityLogs' => $record->activityLogs,
                    'student' => $record,
                ]);
            })
            ->modalWidth('3xl')
            ->icon('heroicon-s-finger-print')
            ->color('teal')
            ->modalSubmitAction(false);
    }
}
