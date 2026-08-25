<div class="rounded-2xl border border-slate-200 bg-white p-4">
    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $heading }}</p>
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Vidljivi natpis poveznice</label>
            <input type="text" wire:model="form.translation_payload.{{ $basePath }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Vrsta poveznice</label>
            <select wire:model="form.translation_payload.{{ $basePath }}.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="none">Bez poveznice</option>
                <option value="external">Web adresa ili interna stranica</option>
                <option value="blog">Blog objava</option>
                <option value="call">Natječaj</option>
                <option value="pdf">PDF dokument</option>
            </select>
        </div>
    </div>
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Web adresa</label>
            <input type="text" wire:model="form.translation_payload.{{ $basePath }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Slug objave ili natječaja</label>
            <input type="text" wire:model="form.translation_payload.{{ $basePath }}.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-3">
        @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
            'label' => 'PDF dokument',
            'currentPath' => (string) ($link['path'] ?? ''),
            'uploadModel' => 'assetUploads.'.str_replace('.', '_', $basePath.'_path'),
        ])
    </div>
    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Jezik PDF dokumenta</label>
        <select wire:model="form.translation_payload.{{ $basePath }}.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
            <option value="">Nije označeno</option>
            @foreach ($adminLocaleOptions as $localeOption)
                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Obavezno označite jezik PDF-a. Na EN stranici prikazuju se samo dokumenti označeni s <strong>en</strong>.</p>
    </div>
</div>
