<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('author.name')->label('Author'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('published_at')->dateTime('Y-m-d H:i'),
                        TextEntry::make('view_count')->numeric(),
                        IconEntry::make('is_featured')->boolean()->label('Featured'),
                        TextEntry::make('excerpt')->columnSpanFull(),
                        TextEntry::make('content')->html()->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
