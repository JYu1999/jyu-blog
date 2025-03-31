<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnWebsite')
                ->label('View on Website')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->url(fn (Post $record) => route('blog.show', $record->slug))
                ->openUrlInNewTab()
                ->visible(fn (Post $record) => $record->status === 'published'),
            Actions\DeleteAction::make(),
        ];
    }
}
