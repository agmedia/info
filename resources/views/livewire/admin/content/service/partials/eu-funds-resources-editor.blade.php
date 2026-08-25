<div id="eu-funds-resources-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">7. Programi i instrumenti</p><h2 class="mt-1 text-lg font-semibold text-slate-900">HBOR, HAMAG i ostali izvori potpore</h2></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.resources.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod sekcije</label><textarea rows="4" wire:model="form.translation_payload.resources.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
    </div>
    <div class="mt-6 space-y-5">
        @foreach (($translationPayload['resources']['cards'] ?? []) as $cardIndex => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">Kartica programa {{ $cardIndex + 1 }}</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka iznad naslova</label><input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.eyebrow" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                    <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                </div>
                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst kartice</label>
                    <textarea
                        rows="10"
                        wire:model.live.debounce.300ms="form.translation_payload.resources.cards.{{ $cardIndex }}.body_html"
                        data-quill-editor
                        data-quill-profile="service-text"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                    ></textarea>
                </div>

                @foreach (($card['groups'] ?? []) as $groupIndex => $group)
                    <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov popisa {{ $groupIndex + 1 }}</label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <div class="mt-4 space-y-3">
                            @foreach (($group['items'] ?? []) as $itemIndex => $item)
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Stavka {{ $itemIndex + 1 }}</label>
                                    <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                                    <div class="mt-3 grid gap-3 md:grid-cols-4">
                                        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Vrsta poveznice</label><select wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"><option value="none">Bez poveznice</option><option value="external">Web adresa</option><option value="blog">Blog objava</option><option value="call">Natječaj</option><option value="pdf">PDF</option></select></div>
                                        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Web adresa</label><input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.url" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                                        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Slug</label><input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.slug" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                                        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Jezik PDF-a</label><select wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm lowercase"><option value="">Nije označeno</option>@foreach ($adminLocaleOptions as $localeOption)<option value="{{ $localeOption }}">{{ $localeOption }}</option>@endforeach</select></div>
                                    </div>
                                    <div class="mt-3">@include('livewire.admin.content.service.partials.pdf-asset-upload-field', ['label' => 'PDF dokument', 'currentPath' => (string) ($item['link']['path'] ?? ''), 'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_groups_'.$groupIndex.'_items_'.$itemIndex.'_link_path'])</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    @include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Glavna poveznica', 'basePath' => 'resources.cards.'.$cardIndex.'.primary_link', 'link' => (array) ($card['primary_link'] ?? [])])
                    @include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Dodatna poveznica', 'basePath' => 'resources.cards.'.$cardIndex.'.secondary_link', 'link' => (array) ($card['secondary_link'] ?? [])])
                </div>
            </div>
        @endforeach
    </div>
</div>
