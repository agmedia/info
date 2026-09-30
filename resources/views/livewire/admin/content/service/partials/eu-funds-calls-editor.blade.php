<div id="eu-funds-calls-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Natječaji</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Naslovi iznad aktualnih natječaja</h2><p class="mt-1 text-sm text-slate-600">Kartice automatski dolaze iz sadržaja Natječaji. Ovdje uređujete vidljive natpise sekcije.</p></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2"><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka iznad naslova</label><input type="text" wire:model="form.translation_payload.calls.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.calls.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div class="xl:col-span-2"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod</label><textarea rows="4" wire:model="form.translation_payload.calls.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis za prikaz svih natječaja</label><input type="text" wire:model="form.translation_payload.calls.view_all_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div></div>
    <div class="mt-5 max-w-2xl">
        @include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Dokument uz natječaje', 'basePath' => 'calls.download_link', 'link' => (array) ($translationPayload['calls']['download_link'] ?? [])])
    </div>
    <div class="mt-6 border-t border-slate-200 pt-5">
        <div class="grid gap-4 xl:grid-cols-2">
            <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov ostalih poziva</label><input type="text" wire:model="form.translation_payload.calls.other_calls.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
            <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod ostalih poziva</label><textarea rows="3" wire:model="form.translation_payload.calls.other_calls.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
        </div>
        <div class="mt-5 space-y-4">
            @foreach (($translationPayload['calls']['other_calls']['items'] ?? []) as $itemIndex => $item)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Ostali poziv {{ $itemIndex + 1 }}</p><button type="button" wire:click="removeTranslationListItem('calls.other_calls.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button></div>
                    <div class="mt-3"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.calls.other_calls.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                    <div class="mt-4">@include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Poveznica', 'basePath' => 'calls.other_calls.items.'.$itemIndex.'.link', 'link' => (array) ($item['link'] ?? [])])</div>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addTranslationListItem('calls.other_calls.items', 'eu_funds_link_item')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj ostali poziv</button>
    </div>
</div>
