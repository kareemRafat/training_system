<?php

namespace App\Filament\Resources\TrainingGroupResource\Pages;

use App\Filament\Resources\TrainingGroupResource;
use App\Models\Student;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewStudents extends ViewRecord
{
    protected static string $resource = TrainingGroupResource::class;

    protected static ?string $title = 'عـرض الـطلاب';

    protected static ?string $breadcrumb = 'عـرض الـطلاب';

    // no $view prop the infolist is rendered

    protected $start = 0;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('مـعـلومـات الـمـجـمـوعـة')
                    ->schema([
                        TextEntry::make('name')
                            ->label('إسـم الـمـجـمـوعـة')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('danger')
                            ->copyable(),
                        TextEntry::make('start_date')
                            ->label('تـاريـخ الـبـدايـة')
                            ->icon('heroicon-s-calendar')
                            ->iconColor('danger')
                            ->date('d - m - Y')
                            ->size(TextEntry\TextEntrySize::Medium),
                        // Other group fields...
                    ])
                    ->columns(2),

                Section::make('الـطـلاب')

                    ->schema([
                        RepeatableEntry::make('students')
                            ->label('الـطـلاب')
                            ->schema([
                                TextEntry::make('index')
                                    ->label('')
                                    ->formatStateUsing(fn () => ++$this->start)
                                    ->size(TextEntry\TextEntrySize::Small)
                                    ->default(1)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpanFull()
                                    ->extraAttributes([
                                        'class' => 'bg-gray-200 dark:bg-gray-600 text-black dark:text-white font-bold text-center rounded-full h-10 flex items-center px-4',
                                        'style' => 'width:fit-content',
                                    ]),
                                TextEntry::make('name')
                                    ->label('الإسـم')
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->icon('heroicon-s-clipboard-document-list')
                                    ->color('primary')
                                    ->copyable(),
                                TextEntry::make('phone')
                                    ->label('الـمـوبـايـل')
                                    ->copyable()
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->icon('heroicon-s-device-phone-mobile')
                                    ->iconColor('warning'),
                                TextEntry::make('group.name')
                                    ->label('جـروب الـكـورس')
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->icon('heroicon-s-user-group')
                                    ->iconColor('warning'),
                                // Other student fields...
                            ])
                            ->columns(4)
                            ->grid(1),
                    ]),
            ]);
    }
}
