<?php

namespace App\Filament\Actions\NormalActions;

use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ShowCommentAction extends Action
{
    // The class name of the resource i use
    protected string $viewCommentsClass;

    public function viewCommentsClass(string $class): static
    {
        $this->viewCommentsClass = $class;

        return $this;
    }

    protected function setUp(): void
    {
        $this
            ->label('التعليقات')
            ->extraAttributes(['class' => 'text-xs p-1'])
            ->icon('heroicon-s-chat-bubble-bottom-center')
            ->badge(
                fn (Model $record) => $record->comments_count > 0
                    ? $record->comments_count
                    : null
            )
            ->url(function ($record) {
                return $this->viewCommentsClass::getUrl(['record' => $record]);
            })
            ->disabled(fn (Model $record) => $record->comments_count === 0)
            ->button()
            ->size(ActionSize::Small)
            ->color(fn (Model $record) => $record->comments_count > 0 ? 'rose' : Color::Stone);
    }
}
