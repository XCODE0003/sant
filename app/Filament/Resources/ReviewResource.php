<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Каталог';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Отзыв')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Товар')
                            ->relationship('product', 'title')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('author_name')
                            ->label('Имя автора')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('rating')
                            ->label('Оценка')
                            ->options(array_combine(range(1, 5), range(1, 5)))
                            ->default(5)
                            ->required(),
                        Forms\Components\Textarea::make('body')
                            ->label('Комментарий')
                            ->rows(6)
                            ->columnSpanFull()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.title')
                    ->label('Товар')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('rating')
                    ->label('Оценка')
                    ->colors([
                        'danger' => static fn ($state) => $state <= 2,
                        'warning' => static fn ($state) => $state === 3,
                        'success' => static fn ($state) => $state >= 4,
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Комментарий')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->label('Товар')
                    ->relationship('product', 'title'),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Оценка')
                    ->options(array_combine(range(1, 5), range(1, 5))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('product');
    }
}
