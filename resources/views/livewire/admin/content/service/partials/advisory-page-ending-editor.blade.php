<div class="admin-panel admin-form-panel p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">Stručne objave</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Vidljivi natpisi blog sekcije ove podstranice</h2>
        <p class="mt-1 text-sm text-slate-600">Objave dolaze iz odabranog izvora, a ovi natpisi vrijede samo na ovoj podstranici.</p>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-3"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis poveznice svih objava</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.blog_section.all_posts_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis na kartici objave</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.blog_section.post_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
    </div>
</div>

<div class="admin-panel admin-form-panel p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">Kontaktni poziv</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Završna kontaktna sekcija ove podstranice</h2>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov kontaktne kartice</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div class="xl:col-span-2"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kontaktni tekst</label><textarea rows="4" wire:model="form.translation_payload.{{ $pageKey }}.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis gumba</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.meeting.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Napomena uz termin</label><input type="text" wire:model="form.translation_payload.{{ $pageKey }}.meeting.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
    </div>
</div>
