<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewComments extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'تعليقات الطالب';

    // if you make a view the view blade will work
    // if not thie view prop the infolist will work
    protected static string $view = 'filament.pages.student-comments';

    protected static ?string $breadcrumb = 'عرض التعليقات';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([]);
    }

    public function mount($record): void
    {
        // store the previous URL in the session to return back to it
        // except if the previous URL contains '/comments'
        if (! str_contains(url()->previous(), '/comments')) {
            session(['back_url' => url()->previous()]);
        }

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [

            // Add a comment action
            Actions\Action::make('add_comment')
                ->label('أضف تعليق')
                ->icon('heroicon-s-plus')
                ->action(function ($record, array $data): void {
                    // Logic to add a comment
                    // Example: Save the comment to the database
                    $this->record->comments()->create([
                        'user_id' => Auth::id(),
                        'created_at' => now()->setTimezone(config('app.timezone'))->toDateTimeString(),
                        'comment' => $data['comment'],
                    ]);

                    Notification::make()
                        ->title('تم إضافة التعليق بنجاح')
                        ->success()
                        ->send();
                })
                ->form([
                    Textarea::make('comment')
                        ->label('تعليق')
                        ->required(),
                ]),

            Actions\Action::make('back')
                ->label('رجوع')
                ->icon('heroicon-s-backward')
                ->color('warning')
                ->url(session('back_url')),
        ];
    }
}
