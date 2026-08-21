@php
    $previewUrl = $previewUrl ?? null;
    $submitLabel = $submitLabel ?? __('Spremi promjene');
    $cancelLabel = $cancelLabel ?? __('Odustani');
@endphp

<div class="admin-sticky-actions" data-admin-sticky-actions>
    <div class="admin-sticky-actions__status" aria-live="polite">
        <span class="admin-sticky-actions__saved" data-admin-clean-status wire:loading.remove wire:target="save">
            <i class="fa-light fa-circle-check" aria-hidden="true"></i>
            {{ __('Nema nespremljenih izmjena') }}
        </span>
        <span class="admin-sticky-actions__dirty" data-admin-dirty-status wire:loading.remove wire:target="save">
            <i class="fa-light fa-pen-to-square" aria-hidden="true"></i>
            {{ __('Nespremljene izmjene') }}
        </span>
        <span class="admin-sticky-actions__saving" wire:loading wire:target="save">
            <i class="fa-light fa-spinner-third fa-spin" aria-hidden="true"></i>
            {{ __('Spremanje...') }}
        </span>
    </div>

    <div class="admin-sticky-actions__buttons">
        @if ($previewUrl)
            <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="admin-action-button admin-action-button--preview">
                <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
                {{ __('Otvori front') }}
            </a>
        @endif
        <button type="button" wire:click="backToList" data-admin-leave class="admin-action-button admin-action-button--cancel">
            {{ $cancelLabel }}
        </button>
        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="admin-action-button admin-action-button--save">
            <i class="fa-light fa-floppy-disk" aria-hidden="true"></i>
            <span wire:loading.remove wire:target="save">{{ $submitLabel }}</span>
            <span wire:loading wire:target="save">{{ __('Spremam') }}</span>
        </button>
    </div>
</div>
