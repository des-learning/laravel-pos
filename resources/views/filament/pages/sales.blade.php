<x-filament-panels::page>
    <x-filament-panels::form
        id="form"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="create"
    >
        {{ $this->infolist }}

        {{ $this->form }}

        <table class="table-fixed border-collapse border border-slate-500">
            <tr>
                <th class="border border-slate-500 p-2">No.</th>
                <th class="border border-slate-500 p-2">SKU</th>
                <th class="border border-slate-500 p-2">Nama Barang</th>
                <th class="border border-slate-500 p-2">Harga</th>
                <th class="border border-slate-500 p-2">Banyak</th>
                <th class="border border-slate-500 p-2">Subtotal
            </tr>
            @forelse ($this->data['sale_transaction_items'] as $item)
            <tr>
                <td class="border border-slate-500 p-2">{{ $loop->iteration }}</td>
                <td class="border border-slate-500 p-2">{{ $item['product_sku'] }}</td>
                <td class="border border-slate-500 p-2">{{ $item['product_name'] }}</td>
                <td class="border border-slate-500 p-2">{{ number_format($item['product_price'], 0) }}</td>
                <td class="border border-slate-500 p-2">{{ number_format($item['quantity_sold'], 0) }}</td>
                <td class="border border-slate-500 p-2">{{ number_format($item['subtotal'], 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="border border-slate-500 p-2">Belum ada produk</td>
            </tr>
            @endforelse
        </table>

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
