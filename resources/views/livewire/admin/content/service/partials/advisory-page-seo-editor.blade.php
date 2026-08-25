<div class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">SEO</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Meta podaci podstranice</h2>
        <p class="mt-1 text-sm text-slate-600">Polja vrijede samo za ovu podstranicu i odabrani jezik. Ako ih ostavite praznima, meta podaci izvode se iz lokaliziranog naslova i uvodnog sadržaja.</p>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Meta naslov</label>
            <input type="text" wire:model="form.translation_payload.{{ $pageKey }}.meta_title" maxlength="255" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.'.$pageKey.'.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Meta opis</label>
            <textarea rows="4" wire:model="form.translation_payload.{{ $pageKey }}.meta_description" maxlength="320" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
            @error('form.translation_payload.'.$pageKey.'.meta_description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
