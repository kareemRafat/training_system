<?php

namespace App\Filament\Actions\NormalActions;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ViewActivityLogAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->slideOver()
            ->hidden(fn (Model $record) => $record->activityLogs->isEmpty() || Auth::user()->branch_id)
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
