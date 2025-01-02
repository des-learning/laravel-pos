<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\SaleTransaction;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Sales extends Page
{
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.sales';

    public ?array $data = [];

    public function __construct()
    {
        $this->data = [
            'cashier' => Auth::user()->name,
            'cashier_id' => Auth::user()->id,
            'item_input' => [
                'product_sku' => '',
                'product_name' => '',
                'product_price' => 0,
                'quantity_sold' => 1,
                'subtotal' => 0,
            ],
            'sale_transaction_items' => [],
            'selected_product_id' => 0,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Section::make()->schema(
                        [
                            TextInput::make('item_input.product_sku')
                                ->label('SKU')
                                ->columnSpan(2)
                                ->live(onBlur: true)
                                ->afterStateUpdated(
                                    function (string $state, Set $set) {
                                        $product = Product::where('sku', $state)
                                            ->first() ?? null;

                                        $set('selected_product_id', $product?->id ?? 0);
                                        $set('item_input.product_name', $product?->name ?? '');
                                        $set('item_input.product_price', $product?->price ?? 0);
                                    }
                                ),
                            TextInput::make('item_input.product_name')
                                ->label('Nama Barang')
                                ->disabled()
                                ->columnSpan(3)
                                ->live(onBlur: true),
                            TextInput::make('item_input.product_price')
                                ->label('@')
                                ->disabled()
                                ->live(onBlur: true),
                            TextInput::make('item_input.quantity_sold')
                                ->label('Qty')
                                ->numeric()
                                ->integer()
                                ->default(1)
                                ->rules(['min:1', 'max:99'])
                                ->live(onBlur: true)
                                ->afterStateUpdated(
                                    fn (Get $get, Set $set) =>
                                    $set(
                                        'item_input.subtotal',
                                        $get('item_input.product_price') * $get('item_input.quantity_sold')
                                    )
                                ),
                            TextInput::make('item_input.subtotal')
                                ->label('Subtotal')
                                ->disabled(),
                            Actions::make(
                                [
                                    $this->addTransactionItemAction(),
                                ]
                            ),
                        ]
                    )
                        ->columns(9),
                ]
            )
            ->statePath('data');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('chasier')
                        ->label('Cashier'),
                ]
            )
            ->state($this->data);
    }

    public function create(): void
    {
        $saleTransactionItems = $this->data['sale_transaction_items'];
        if (count($saleTransactionItems) <= 0) {
            Notification::make()
                ->title('Masukkan item yang dijual')
                ->danger()
                ->send();
            return;
        }


        try {
            DB::transaction(function () use ($saleTransactionItems) {
                // TODO: lakukan lock supaya tidak terjadi race condition
                $nextNumber = (DB::table('sale_transactions')->max('number') ?? 0) + 1;
                $saleTransaction = SaleTransaction::create([
                    'number' => $nextNumber,
                    'cashier_id' => $this->data['cashier_id'],
                    'transaction_time' => Carbon::now(),
                ]);

                $saleTransaction->items()->createMany($saleTransactionItems);
            });

            Notification::make()
                ->title('Transaksi penjualan tersimpan')
                ->success()
                ->send();

            redirect(Sales::getUrl());
        } catch (\Exception $e) {
            Log::error(
                'error ketika menyimpan transaksi penjualan',
                [
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                ]
            );

            Notification::make()
                ->title('Kesalahan ketika menyimpan transaksi penjualan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->color('gray');
    }

    protected function addTransactionItemAction(): FormAction
    {
        return FormAction::make('add_transaction_item')
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->action(
                function (Get $get, Set $set) {
                    $productId = $get('selected_product_id');
                    if ($productId <= 0) {
                        Notification::make()
                            ->title('Produk tidak ditemukan')
                            ->warning()
                            ->send();
                        return;
                    }

                    $invalidQuantity = is_null($get('item_input.quantity_sold'))
                        || $get('item_input.quantity_sold') < 1
                        || $get('item_input.quantity_sold') > 99;
                    if ($invalidQuantity) {
                        Notification::make()
                            ->title('Jumlah barang salah')
                            ->danger()
                            ->send();
                        return;
                    }

                    // get form data
                    $productSku = $get('item_input.product_sku');
                    $productName = $get('item_input.product_name');
                    $productPrice = $get('item_input.product_price');
                    $quantitySold = $get('item_input.quantity_sold');

                    $items = collect($get('sale_transaction_items'));

                    // retrieve item with product id
                    $item = $items->first(fn ($i) => $i['product_id'] == $productId);
                    $addNewItem = $item == null;

                    // or fallback to new item
                    $item = $item ??
                        [
                            'product_id' => $productId,
                            'product_sku' => $productSku,
                            'product_name' => $productName,
                            'product_price' => $productPrice,
                            'quantity_sold' => 0,
                        ];

                    // calculate
                    $item['quantity_sold'] += $quantitySold;
                    $item['subtotal'] = $item['product_price'] * $item['quantity_sold'];

                    if ($item['subtotal'] < 0) {
                        Log::error("invalid subtotal", [
                            'product_sku' => $productSku,
                            'quantity_sold' => $item['quantity_sold'],
                            'product_price' => $item['product_price'],
                            'subtotal' => $item['subtotal'],
                        ]);

                        Notification::make()
                            ->title('Subtotal negatif')
                            ->danger()
                            ->send();
                        return;
                    }

                    // put back updated item
                    if ($addNewItem) {
                        array_push($this->data['sale_transaction_items'], $item);
                    } else {
                        $set(
                            'sale_transaction_items',
                            $items->map(
                                fn ($i) => $i['product_id'] == $productId ? $item : $i
                            )->toArray()
                        );
                    }

                    // reset form
                    $set('item_input.product_sku', '');
                    $set('item_input.product_name', '');
                    $set('item_input.product_price', 0);
                    $set('item_input.quantity_sold', 1);
                    $set('item_input.subtotal', 0);
                    $set('selected_product_id', 0);
                }
            );
    }
}
