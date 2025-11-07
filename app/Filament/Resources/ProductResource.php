<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Товары';
    protected static ?string $modelLabel = 'товар';
    protected static ?string $pluralModelLabel = 'товары';
    protected static ?string $navigationGroup = 'Каталог';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('article_id')
                            ->label('Артикул')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'title')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Цена')
                            ->numeric()
                            ->required()
                            ->prefix('₽')
                            ->rule('min:0')
                            ->inputMode('decimal'),
                        Forms\Components\TextInput::make('discount')
                            ->label('Скидка, %')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(90)
                            ->step(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('Описание')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Описание')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'strike', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo',
                            ]),
                    ]),
                Forms\Components\Section::make('Медиа и характеристики')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Галерея')
                            ->multiple()
                            ->image()
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpan(1)
                            ->maxFiles(10),
                        Forms\Components\KeyValue::make('characteristics')
                            ->label('Характеристики')
                            ->columnSpan(1)
                            ->keyLabel('Параметр')
                            ->valueLabel('Значение')
                            ->addButtonLabel('Добавить характеристику'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Категория')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->sortable()
                    ->formatStateUsing(fn (Product $record) => number_format($record->price, 2, '.', ' ') . ' ₽'),
                Tables\Columns\TextColumn::make('discount')
                    ->label('Скидка')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('final_price')
                    ->label('Цена со скидкой')
                    ->formatStateUsing(fn (Product $record) => number_format(($record->price * (100 - $record->discount)) / 100, 2, '.', ' ') . ' ₽'),
                Tables\Columns\TextColumn::make('reviews_avg_rating')
                    ->label('Рейтинг')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Отзывов')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Категория')
                    ->relationship('category', 'title'),
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
                Tables\Filters\Filter::make('has_discount')
                    ->label('Есть скидка')
                    ->query(fn (Builder $query) => $query->where('discount', '>', 0)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importXls')
                    ->label('Импорт из XLS')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Импорт товаров из XLS отчёта 1С')
                    ->modalDescription('Файл должен быть в формате *.xls. Категории и товары будут созданы или обновлены в соответствии с отчётом.')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Файл отчёта')
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/octet-stream', '.xls'])
                            ->directory('imports/products')
                            ->disk('local')
                            ->visibility('private')
                            ->preserveFilenames()
                            ->required(),
                    ])
                    ->action(fn (array $data) => static::importProductsFromXls($data['file']))
                    ->modalButton('Импортировать')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['category' => fn ($query) => $query->select('id', 'title')])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }

    protected static function importProductsFromXls(string $relativePath): void
    {
        $disk = Storage::disk('local');
        $fullPath = $disk->path($relativePath);

        $logContext = [
            'file' => $relativePath,
            'rows_processed' => 0,
            'categories_created' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'skipped_rows' => [],
            'price_skipped' => [],
        ];

        try {
            if (! $disk->exists($relativePath)) {
                throw new \RuntimeException('Файл не найден. Загрузите его повторно.');
            }

            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true, true);

            $currentCategory = null;
            $createdCategories = 0;
            $createdProducts = 0;
            $updatedProducts = 0;

            foreach ($rows as $index => $row) {
                if ($index <= 5) {
                    // пропускаем заголовки и служебные строки
                    continue;
                }

                $logContext['rows_processed']++;

                $code = static::convertEncoding(trim((string) ($row['A'] ?? '')));
                $name = static::convertEncoding(trim((string) ($row['B'] ?? '')));

                if ($code === '' && $name === '') {
                    $logContext['skipped_rows'][] = [
                        'row' => $index,
                        'reason' => 'empty_code_and_name',
                    ];
                    continue;
                }

                $stock = static::parseNumber($row['C'] ?? null);
                $purchasePrice = static::parseNumber($row['D'] ?? null);
                $retailPrice = static::parseNumber($row['E'] ?? null);
                $warehouseValue = static::parseNumber($row['F'] ?? null);

                $isCategoryRow = $name !== '' && $retailPrice === null && $stock === null && $purchasePrice === null;

                if ($isCategoryRow) {
                    $currentCategory = static::findOrCreateCategory($code, $name, $createdCategories);
                    $logContext['categories_created'] = $createdCategories;
                    continue;
                }

                if (! $currentCategory || $code === '' || $name === '') {
                    $logContext['skipped_rows'][] = [
                        'row' => $index,
                        'code' => $code,
                        'name' => $name,
                        'reason' => 'missing_category_or_identifiers',
                    ];
                    continue;
                }

                $product = Product::firstOrNew(['article_id' => $code]);
                $exists = $product->exists;

                if (! $exists) {
                    $product->title = $name;
                    $product->slug = static::generateUniqueSlug($name, $code, Product::class);
                    $product->description = $product->description ?? '';
                    $product->category_id = $currentCategory->id;
                } elseif (! $product->category_id) {
                    $product->category_id = $currentCategory->id;
                }

                if ($retailPrice !== null && $retailPrice > 0) {
                    $product->price = $retailPrice;
                } elseif ($retailPrice !== null) {
                    $logContext['price_skipped'][] = [
                        'row' => $index,
                        'article' => $code,
                        'name' => $name,
                        'value' => $retailPrice,
                    ];
                }

                $characteristics = $product->characteristics ?? [];

                // $characteristics['stock'] = $stock ?? 0;
                // $characteristics['purchase_price'] = $purchasePrice ?? null;
                // $characteristics['warehouse_value'] = $warehouseValue ?? null;

                $product->characteristics = $characteristics;
                $product->save();

                if ($exists) {
                    $updatedProducts++;
                } else {
                    $createdProducts++;
                }
            }

            $logContext['products_created'] = $createdProducts;
            $logContext['products_updated'] = $updatedProducts;

            Log::info('XLS import completed', $logContext);

            Notification::make()
                ->title('Импорт завершён')
                ->body("Создано категорий: {$createdCategories}\nСоздано товаров: {$createdProducts}\nОбновлено товаров: {$updatedProducts}")
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Log::error('XLS import failed', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'context' => $logContext,
            ]);

            Notification::make()
                ->title('Импорт не выполнен')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        } finally {
            if ($disk->exists($relativePath)) {
                $disk->delete($relativePath);
            }
        }
    }

    protected static function parseNumber($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(preg_replace('/[\s\x{A0}]+/u', '', (string) $value));
        $normalized = str_ireplace(['руб.', 'руб'], '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        if ($normalized === '') {
            return null;
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    protected static function findOrCreateCategory(?string $code, string $name, int &$createdCategories): Category
    {
        $category = Category::firstWhere('title', $name);

        if (! $category) {
            $slug = static::generateUniqueSlug($name, $code ?? Str::random(4), Category::class);

            $category = Category::create([
                'title' => $name,
                'slug' => $slug,
                'is_active' => true,
            ]);

            $createdCategories++;
        }

        return $category;
    }

    protected static function generateUniqueSlug(string $name, string $fallback, string $modelClass): string
    {
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = Str::slug($fallback) ?: 'item-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while ($modelClass::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected static function convertEncoding(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $value = trim($value);

        if (mb_check_encoding($value, 'UTF-8') && preg_match('/[А-Яа-яЁё]/u', $value)) {
            return $value;
        }

        $result = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
        if ($result !== false && mb_check_encoding($result, 'UTF-8') && preg_match('/[А-Яа-яЁё]/u', $result)) {
            return trim($result);
        }

        $value = preg_replace_callback('/[\x80-\xFF]+/', function ($matches) {
            $chunk = $matches[0];
            $utf8 = @iconv('Windows-1251', 'UTF-8//IGNORE', $chunk);
            return $utf8 !== false ? $utf8 : $chunk;
        }, $value);

        if (mb_check_encoding($value, 'UTF-8') && preg_match('/[А-Яа-яЁё]/u', $value)) {
            return trim($value);
        }

        $result = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $value);
        if ($result !== false && mb_check_encoding($result, 'UTF-8') && preg_match('/[А-Яа-яЁё]/u', $result)) {
            return trim($result);
        }

        return $value;
    }
}
