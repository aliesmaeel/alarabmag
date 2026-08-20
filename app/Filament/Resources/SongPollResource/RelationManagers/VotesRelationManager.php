<?php

namespace App\Filament\Resources\SongPollResource\RelationManagers;

use App\Models\SongPollVote;
use App\Support\SongPollVoteCsv;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VotesRelationManager extends RelationManager
{
    protected static string $relationship = 'votes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('أصوات التصويت');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(__('البريد الإلكتروني'))
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('entry.title')
                    ->label(__('الأغنية'))
                    ->searchable()
                    ->description(fn (SongPollVote $record): ?string => $record->entry?->artist),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ التصويت'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('تصدير CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return SongPollVoteCsv::download(
                            $this->getFilteredTableQuery(),
                            'vote-emails-poll-'.$this->getOwnerRecord()->getKey().'-'.now()->format('Y-m-d-His'),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
