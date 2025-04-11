<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudntsOverview extends BaseWidget
{
    protected static ?string $columns = '3';

    protected function getStats(): array
    {
        return [
            Stat::make('عدد المتدربين', \App\Models\Student::where(function ($query) {
                if (Auth::user()->branch_id) {
                    $query->where('branch_id', Auth::user()->branch_id);
                } else {

                }
            })->count())
                ->description('عدد الطلاب المسجلين بالتدريب منذ 2025')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('عدد المجموعات', \App\Models\TrainingGroup::where(function ($query) {
                if (Auth::user()->branch_id) {
                    $query->where('branch_id', Auth::user()->branch_id);
                } else {

                }
            })->count())
                ->description('عدد مجموعات التدريب منذ 2025')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([7, 8, 10, 11, 15, 17, 25]),
        ];
    }
}
