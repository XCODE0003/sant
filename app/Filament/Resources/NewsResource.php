<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Новость')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Контент')
                            ->columnSpanFull()
                            ->required(),
                    ]),
                Forms\Components\Section::make('Дополнительно')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Обложка')
                            ->image()
                            ->directory('news')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(4096),
                        Forms\Components\Repeater::make('tags')
                            ->label('Теги')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(30),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Цвет')
                                    ->default('#3B82F6')
                                    ->nullable(),
                            ])
                            ->default([])
                            ->collapsed()
                            ->columns(2)
                            ->addActionLabel('Добавить тег')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Дата публикации'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Опубликовано')
                            ->default(true),
                        Forms\Components\TextInput::make('views')
                            ->label('Просмотры')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Авто'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Публикация')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Просмотры')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Теги')
                    ->formatStateUsing(function ($state) {
                        return collect($state ?? [])
                            ->map(function ($tag) {
                                $label = $tag['label'] ?? (is_string($tag) ? $tag : '');
                                $color = $tag['color'] ?? '#64748b';

                                return sprintf(
                                    '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color:%s">%s</span>',
                                    $color,
                                    e($label)
                                );
                            })
                            ->implode(' ');
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Статус')
                    ->placeholder('Все')
                    ->trueLabel('Активные')
                    ->falseLabel('Неактивные')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\Filter::make('published')
                    ->label('Опубликованные')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest('published_at');
    }
}
