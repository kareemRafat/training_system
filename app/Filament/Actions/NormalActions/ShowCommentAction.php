<?php

namespace App\Filament\Actions\NormalActions;

use App\Models\Comment;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\StudentResource\Pages\ViewComments;

class ShowCommentAction extends Action
{



    protected function setUp(): void
    {
        $this
            ->label('عرض التعليقات')
            ->extraAttributes(['class' => 'text-xs p-1'])
            ->icon('heroicon-s-chat-bubble-bottom-center')
            ->badge(
                fn(Model $record) => $record->comments_count > 0
                    ? $record->comments_count
                    : null
            )
            ->url(fn($record) => ViewComments::getUrl(['record' => $record]))
            ->disabled(fn(Model $record) => $record->comments_count === 0)
            ->button()
            ->size(ActionSize::Small)
            ->color(fn(Model $record) => $record->comments_count > 0 ? 'rose' : Color::Stone);
    }
}
