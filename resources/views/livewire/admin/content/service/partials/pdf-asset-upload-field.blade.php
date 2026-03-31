<div>
    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label ?? __('Asset Path') }}</label>
    <input
        type="text"
        value="{{ $currentPath ?? '' }}"
        readonly
        placeholder="{{ __('No asset uploaded yet') }}"
        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm font-mono text-slate-600"
    />
    <p class="mt-1 text-xs text-slate-500">
        {{ __('Odaberite PDF datoteku i pri spremanju će se asset path popuniti automatski.') }}
    </p>

    <input
        type="file"
        wire:model="{{ $uploadModel }}"
        accept=".pdf,application/pdf"
        class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
    />
    @error($uploadModel) <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

    <div wire:loading wire:target="{{ $uploadModel }}" class="mt-2 text-xs text-slate-500">
        {{ __('Uploading PDF...') }}
    </div>
</div>
