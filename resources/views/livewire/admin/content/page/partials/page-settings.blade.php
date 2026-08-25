<div id="info-page-settings-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">{{ $settingsStep ?? 'Postavke' }}. Postavke stranice</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Objava, jezik i adresa stranice</h2>
        <p class="mt-1 text-sm text-slate-600">Ove postavke upravljaju dostupnošću stranice. Ne mijenjaju njezin vizualni izgled.</p>
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Jezik sadržaja</label>
            <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                @foreach ($adminLocaleOptions as $localeOption)
                    <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                @endforeach
            </select>
            @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-1 xl:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naziv stranice</label>
            <input type="text" wire:model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <p class="mt-1 text-xs text-slate-500">Koristi se u naslovu preglednika i u administraciji.</p>
            @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Datum objave</label>
            <input type="datetime-local" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.published_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Adresa stranice (slug)</label>
            <input type="text" wire:model="form.slug" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-sm lowercase" />
            <p class="mt-1 text-xs text-slate-500">Adresa se sprema zasebno za svaki jezik. Nakon promjene provjerite lokaliziranu navigaciju.</p>
            @error('form.slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <button
            type="button"
            wire:click="$toggle('form.is_active')"
            class="admin-switch"
            data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
            role="switch"
            aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
            aria-label="Promijeni status stranice"
        >
            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
            <span class="admin-switch-label">{{ $form['is_active'] ? 'Aktivno' : 'Neaktivno' }}</span>
        </button>
    </div>

    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Sažetak stranice</label>
        <textarea rows="3" wire:model="form.excerpt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        <p class="mt-1 text-xs text-slate-500">Kratki opis sprema se zasebno za odabrani jezik.</p>
        @error('form.excerpt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <details class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <summary class="cursor-pointer text-sm font-semibold text-slate-700">Tehničke postavke</summary>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Interna oznaka</label>
                <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-sm" readonly />
                @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Predložak</label>
                <input type="text" wire:model="form.layout" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-sm" readonly />
                @error('form.layout') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Redoslijed</label>
                <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                @error('form.sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </details>
</div>
