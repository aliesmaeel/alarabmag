<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\SongPollVoteResource\Pages;
use App\Models\SongPoll;
use App\Models\SongPollVote;
use App\Support\SongPollVoteCsv;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SongPollVoteResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = SongPollVote::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'أصوات التصويت';

    protected static ?string $modelLabel = 'صوت';

    protected static ?string $pluralModelLabel = 'أصوات التصويت';

    protected static ?string $navigationGroup = 'الجمهور';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(__('البريد الإلكتروني'))
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('poll.title')
                    ->label(__('الاستفتاء'))
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('entry.title')
                    ->label(__('الأغنية'))
                    ->searchable()
                    ->description(fn (SongPollVote $record): ?string => $record->entry?->artist),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ التصويت'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('song_poll_id')
                    ->label(__('الاستفتاء'))
                    ->options(fn () => SongPoll::query()->orderByDesc('id')->pluck('title', 'id')->all())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportSelected')
                        ->label(__('تصدير المحدد CSV'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id');

                            return SongPollVoteCsv::download(
                                SongPollVote::query()->whereIn('id', $ids),
                                'vote-emails-selected-'.now()->format('Y-m-d-His'),
                            );
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('تصدير CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        return SongPollVoteCsv::download(
                            $livewire->getFilteredTableQuery(),
                            'vote-emails-'.now()->format('Y-m-d-His'),
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSongPollVotes::route('/'),
        ];
    }
}
