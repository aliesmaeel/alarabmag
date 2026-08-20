<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use App\Support\SubscriberCsv;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriberResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'المشتركون';

    protected static ?string $modelLabel = 'مشترك';

    protected static ?string $pluralModelLabel = 'المشتركون';

    protected static ?string $navigationGroup = 'الجمهور';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
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
                Tables\Columns\TextColumn::make('source')
                    ->label(__('المصدر'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'newsletter' ? __('النشرة البريدية') : ($state ?: '—')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الاشتراك'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
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

                            return SubscriberCsv::download(
                                Subscriber::query()->whereIn('id', $ids),
                                'subscribers-selected-'.now()->format('Y-m-d-His'),
                            );
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('تصدير CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        return SubscriberCsv::download(
                            $livewire->getFilteredTableQuery(),
                            'subscribers-'.now()->format('Y-m-d-His'),
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
        ];
    }
}
