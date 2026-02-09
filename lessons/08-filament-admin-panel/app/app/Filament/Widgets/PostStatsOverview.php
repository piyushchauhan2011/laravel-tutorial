<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', (string) Post::query()->count())
                ->description('All statuses')
                ->color('primary'),
            Stat::make('Published', (string) Post::query()->where('status', 'published')->count())
                ->description('Live content')
                ->color('success'),
            Stat::make('Draft + Review', (string) Post::query()->whereIn('status', ['draft', 'review'])->count())
                ->description('Needs publishing workflow')
                ->color('warning'),
        ];
    }
}
