<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Admin Users') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('Manage administrator accounts, roles and access in one place.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('Items per page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] grid-cols-[minmax(30rem,1fr)_12rem] items-end gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.search') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Name or email...') }}"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Role') }}</label>
                        <select wire:model.live="role" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            <option value="">{{ __('All roles') }}</option>
                            @foreach ($roles as $roleItem)
                                <option value="{{ $roleItem->name }}">{{ $roleItem->title ?: ucfirst($roleItem->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($canCreateEditor)
                    <a href="{{ route('admin.users.create') }}" class="shrink-0 rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                        {{ __('Add Editor') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('id')" class="inline-flex items-center gap-1">
                                {{ __('ID') }} <span class="text-xs">{{ $sortBy === 'id' ? ($sortDir === 'asc' ? '^' : 'v') : '<>' }}</span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-left font-semibold">
                            <button type="button" wire:click="sort('name')" class="inline-flex items-center gap-1">
                                {{ __('Name') }} <span class="text-xs">{{ $sortBy === 'name' ? ($sortDir === 'asc' ? '^' : 'v') : '<>' }}</span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-left font-semibold">
                            <button type="button" wire:click="sort('email')" class="inline-flex items-center gap-1">
                                {{ __('Email') }} <span class="text-xs">{{ $sortBy === 'email' ? ($sortDir === 'asc' ? '^' : 'v') : '<>' }}</span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('Role') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('email_verified_at')" class="inline-flex items-center gap-1">
                                {{ __('Verified') }} <span class="text-xs">{{ $sortBy === 'email_verified_at' ? ($sortDir === 'asc' ? '^' : 'v') : '<>' }}</span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('created_at')" class="inline-flex items-center gap-1">
                                {{ __('Created') }} <span class="text-xs">{{ $sortBy === 'created_at' ? ($sortDir === 'asc' ? '^' : 'v') : '<>' }}</span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $displayRole = $row->roles->reject(fn ($role) => $role->name === 'customer')->sortBy('id')->first();
                            $roleName = $displayRole?->name ?? 'admin';
                            $roleTitle = $displayRole?->title ?? ucfirst($roleName);
                            $isCurrent = auth()->id() === $row->id;
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-center font-mono text-xs text-slate-700">{{ $row->id }}</td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium">{{ $row->name }}</div>
                                @if ($isCurrent)
                                    <div class="text-xs text-cyan-700">{{ __('Current user') }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-700">{{ $row->email }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $roleTitle }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $row->email_verified_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $row->email_verified_at ? __('admin.common.yes') : __('admin.common.no') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-600">{{ optional($row->created_at)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-right">
                                @if ($canEditUsers)
                                    <a href="{{ route('admin.users.edit', ['user' => $row->id]) }}" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('admin.common.edit') }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">{{ __('View only') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('No admin users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>
</div>
