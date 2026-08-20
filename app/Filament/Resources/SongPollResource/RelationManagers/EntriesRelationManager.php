<?php

namespace App\Filament\Resources\SongPollResource\RelationManagers;

use App\Filament\Resources\SongPollEntryResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('الأغاني');
    }

    public function form(Form $form): Form
    {
        return $form->schema(SongPollEntryResource::songSchema(includePoll: false));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->columns(SongPollEntryResource::songTableColumns())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('إضافة أغنية'))
                    ->modalWidth('5xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('5xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
