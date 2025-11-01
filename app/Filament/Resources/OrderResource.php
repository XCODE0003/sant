<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\ProductResource;
use App\Models\Order;
use Closure;
use Filament\Forms\Get;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Продажи';

    protected static ?string $navigationLabel = 'Заказы';

    protected static ?string $modelLabel = 'заказ';

    protected static ?string $pluralModelLabel = 'заказы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Общие сведения')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('number')
                            ->label('Номер заказа')
                            ->content(fn (Order $record): string => $record->number),
                        Placeholder::make('status')
                            ->label('Статус')
                            ->content(fn (Order $record): string => Order::statusLabel($record->status)),
                        Placeholder::make('created_at')
                            ->label('Создан')
                            ->content(fn (Order $record): string => optional($record->created_at)?->format('d.m.Y H:i') ?? '—'),
                        Placeholder::make('total_price')
                            ->label('Сумма заказа')
                            ->content(fn (Order $record): string => number_format((float) $record->total_price, 2, '.', ' ') . ' ₽'),
                        Placeholder::make('payment_method')
                            ->label('Способ оплаты')
                            ->content(fn (Order $record): string => Order::paymentLabel($record->payment_method)),
                        Placeholder::make('delivery_method')
                            ->label('Способ доставки')
                            ->content(fn (Order $record): string => Order::deliveryLabel($record->delivery_method)),
                    ]),

                Forms\Components\Section::make('Покупатель')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('customer_first_name')
                            ->label('Имя')
                            ->content(fn (Order $record): string => $record->customer_first_name),
                        Placeholder::make('customer_last_name')
                            ->label('Фамилия')
                            ->content(fn (Order $record): string => $record->customer_last_name),
                        Placeholder::make('customer_phone')
                            ->label('Телефон')
                            ->content(fn (Order $record): string => $record->customer_phone),
                        Placeholder::make('customer_email')
                            ->label('Email')
                            ->content(fn (Order $record): string => $record->customer_email),
                    ]),

                Forms\Components\Section::make('Доставка')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('delivery_city')
                            ->label('Город')
                            ->content(fn (Order $record): string => $record->delivery_city ?? '—'),
                        Placeholder::make('delivery_street')
                            ->label('Улица')
                            ->content(fn (Order $record): string => $record->delivery_street ?? '—'),
                        Placeholder::make('delivery_house')
                            ->label('Дом')
                            ->content(fn (Order $record): string => $record->delivery_house ?? '—'),
                        Placeholder::make('delivery_apartment')
                            ->label('Квартира')
                            ->content(fn (Order $record): string => $record->delivery_is_private_house ? '—' : ($record->delivery_apartment ?? '—')),
                        Placeholder::make('delivery_entrance')
                            ->label('Подъезд')
                            ->content(fn (Order $record): string => $record->delivery_is_private_house ? '—' : ($record->delivery_entrance ?? '—')),
                        Placeholder::make('delivery_comment')
                            ->label('Комментарий для курьера')
                            ->content(fn (Order $record): string => $record->delivery_comment ?? '—'),
                        Placeholder::make('delivery_is_private_house')
                            ->label('Частный дом')
                            ->content(fn (Order $record): string => $record->delivery_is_private_house ? 'Да' : 'Нет'),
                    ]),

                Forms\Components\Section::make('Комментарий клиента')
                    ->schema([
                        Placeholder::make('comment')
                            ->label('Комментарий')
                            ->content(fn (Order $record): string => $record->comment ?: '—'),
                    ]),

                Forms\Components\Section::make('Состав заказа')
                    ->schema([
                        Forms\Components\Repeater::make('products')
                            ->label('Товары')
                            ->columns(1)
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Placeholder::make('title')
                                            ->label('Товар')
                                            ->content(function ($state, Get $get): HtmlString {
                                                $title = $state ?: '—';
                                                $productId = $get('id');

                                                if (! $productId) {
                                                    return new HtmlString(e($title));
                                                }

                                                $adminUrl = ProductResource::getUrl('edit', ['record' => $productId]);
                                                $clientUrl = route('product', ['id' => $productId]);

                                                $links = sprintf(
                                                    '<div class="flex flex-wrap gap-2 mt-2 text-xs">
                                                        <a href="%s" target="_blank" class="bg-primary/10 text-primary hover:bg-primary/20 px-2 py-1 font-medium rounded">В админке</a>
                                                        <a href="%s" target="_blank" class="hover:bg-gray-200 px-2 py-1 font-medium text-gray-700 bg-gray-100 rounded">На сайте</a>
                                                    </div>',
                                                    e($adminUrl),
                                                    e($clientUrl),
                                                );

                                                return new HtmlString(sprintf('<div class="flex flex-col gap-1">%s%s</div>', e($title), $links));
                                            }),
                                        Placeholder::make('quantity')
                                            ->label('Количество')
                                            ->content(fn ($state): string => (string) ($state ?? 0)),
                                        Placeholder::make('final_price')
                                            ->label('Цена')
                                            ->content(function ($state): string {
                                                $value = (float) ($state ?? 0);

                                                return number_format($value, 2, '.', ' ') . ' ₽';
                                            }),
                                        Placeholder::make('total_price')
                                            ->label('Итого')
                                            ->content(function ($state): string {
                                                $value = (float) ($state ?? 0);

                                                return number_format($value, 2, '.', ' ') . ' ₽';
                                            }),
                                    ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->default(function (?Order $record): array {
                                if (! $record) {
                                    return [];
                                }

                                return collect($record->products ?? [])
                                    ->map(function ($item) {
                                        $item = (array) $item;

                                        $quantity = (int) ($item['quantity'] ?? 1);
                                        $unitPrice = (float) ($item['final_price'] ?? $item['price'] ?? 0);
                                        $total = (float) ($item['total_price'] ?? ($unitPrice * $quantity));

                                        return [
                                            'id' => $item['id'] ?? null,
                                            'title' => $item['title'] ?? 'Товар',
                                            'slug' => $item['slug'] ?? null,
                                            'image' => $item['image'] ?? null,
                                            'quantity' => $quantity,
                                            'final_price' => $unitPrice,
                                            'price' => $item['price'] ?? null,
                                            'total_price' => $total,
                                        ];
                                    })
                                    ->values()
                                    ->all();
                            })
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Номер')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_full_name')
                    ->label('Покупатель')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Телефон')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_price')
                    ->label('Сумма')
                    ->sortable()
                    ->formatStateUsing(fn (Order $record): string => number_format((float) $record->total_price, 2, '.', ' ') . ' ₽'),
                TextColumn::make('items_count')
                    ->label('Позиций')
                    ->sortable()
                    ->alignRight(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'primary' => [Order::STATUS_PENDING],
                        'info' => [Order::STATUS_CONFIRMED, Order::STATUS_SHIPPED],
                        'success' => [Order::STATUS_DELIVERED],
                        'danger' => [Order::STATUS_CANCELLED],
                    ])
                    ->formatStateUsing(fn (string $state): string => Order::statusLabel($state)),
                TextColumn::make('payment_method')
                    ->label('Оплата')
                    ->formatStateUsing(fn (string $state): string => Order::paymentLabel($state))
                    ->toggleable(),
                TextColumn::make('delivery_method')
                    ->label('Доставка')
                    ->formatStateUsing(fn (string $state): string => Order::deliveryLabel($state))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(Order::STATUS_LABELS),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Оплата')
                    ->options(Order::PAYMENT_LABELS),
                Tables\Filters\SelectFilter::make('delivery_method')
                    ->label('Доставка')
                    ->options(Order::DELIVERY_LABELS),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()->where('status', Order::STATUS_PENDING)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}

