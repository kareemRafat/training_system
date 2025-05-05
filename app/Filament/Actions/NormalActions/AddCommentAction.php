<?php

namespace App\Filament\Actions\NormalActions;

use App\Traits\AddActivityLogs;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddCommentAction extends Action
{
    use AddActivityLogs;

    protected function setUp(): void
    {
        $this
            ->label('تعليق')
            ->color('info')
            ->extraAttributes(['class' => 'font-bold'])
            ->icon('heroicon-s-chat-bubble-bottom-center-text')
            ->modalHeading('إضافة تعليق على الطالب')
            ->modalSubmitActionLabel('تأكيد')
            ->action(function (Model $record, $data) {
                // send timezone with $data
                $data['created_at'] =
                    $data['created_at'] ?? now()->setTimezone(config('app.timezone'))->toDateTimeString();
                $this->update($record, $data);
            })
            ->form([
                Textarea::make('comment')
                    ->label('تعليق')
                    ->required(),
            ]);
    }

    public function update(Model $record, $data): void
    {

        // Use a database transaction to ensure data consistency
        DB::transaction(function () use ($record, $data) {

            // ✅ Add the comment using Eloquent model and relation
            $comment = $record->comments()->create([
                'comment' => $data['comment'],
                'user_id' => Auth::user()->id,
                'created_at' => $data['created_at'], // Pass the client time from the form
            ]);

            // ✅ Add the comment to the activity logs
            AddActivityLogs::Add(
                event: 'comment',
                action: 'اضافة تعليق',
                value: $comment->toArray(),
                record: $record
            );
        });

        // ✅ Show success notification
        Notification::make()
            ->title('تم إضافة التعليق بنجاح')
            ->success()
            ->send();
    }
}
