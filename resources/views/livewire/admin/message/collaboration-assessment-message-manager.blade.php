<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.messages.collaboration_assessment.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.messages.collaboration_assessment.manager.subtitle') }}</p>
                <a href="{{ route('assessment.create') }}" target="_blank" rel="noreferrer" class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                    <i class="fa-regular fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    <span>{{ __('admin.common.open_front_form') }}</span>
                </a>
                <p class="mt-2 text-xs text-slate-500">{{ __('admin.messages.collaboration_assessment.manager.items_per_page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[48rem] items-end gap-3 md:grid-cols-[minmax(0,1fr)_12rem]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.search') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('admin.messages.collaboration_assessment.manager.search_placeholder') }}"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.state') }}</label>
                        <select wire:model.live="status" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.collaboration_assessment.manager.summary.all') }}</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($totals['all'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.collaboration_assessment.status.new') }}</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format((int) ($totals['new'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.collaboration_assessment.status.read') }}</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format((int) ($totals['read'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.collaboration_assessment.status.resolved') }}</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format((int) ($totals['resolved'] ?? 0)) }}</p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.collaboration_assessment.manager.table.contact') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.collaboration_assessment.manager.table.company') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.collaboration_assessment.manager.table.assessment') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.collaboration_assessment.manager.table.state') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.collaboration_assessment.manager.table.received') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $answers = (array) data_get($row->payload, 'answers', []);
                            $booleanMap = [
                                'yes' => __('admin.common.yes'),
                                'no' => __('admin.common.no'),
                            ];
                            $detailFields = [
                                'company_name',
                                'company_oib',
                                'activity',
                                'contact_email',
                                'contact_phone',
                                'incoming_invoices_monthly',
                                'outgoing_invoices_monthly',
                                'bank_accounts_monthly',
                                'payroll_calculations_monthly',
                                'other_calculations_monthly',
                                'incoming_invoice_payments',
                                'inventory_bookkeeping',
                                'travel_orders_monthly',
                                'cost_centers_tracking',
                                'intrastat_obligation',
                                'audit_obligation',
                                'monthly_reporting',
                                'vat_status',
                                'accounting_software',
                                'tax_issues',
                                'document_delivery',
                                'additional_requirements',
                                'potential_start_date',
                            ];
                            $statusClasses = match ($row->status) {
                                'read' => 'bg-sky-100 text-sky-800',
                                'resolved' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-amber-100 text-amber-800',
                            };
                        @endphp
                        <tr class="{{ $row->status === 'new' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-3 py-3 text-slate-800">
                                <div class="font-semibold text-slate-900">{{ $row->name }}</div>
                                <div class="mt-1 text-sm text-slate-600">
                                    <a href="mailto:{{ $row->email }}" class="hover:text-slate-900 hover:underline">{{ $row->email }}</a>
                                </div>
                                @if ($row->phone)
                                    <div class="mt-1 text-xs text-slate-500">{{ $row->phone }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">{{ $answers['company_name'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.company_oib') }}:
                                    {{ $answers['company_oib'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.activity') }}:
                                    {{ $answers['activity'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                @if (trim((string) ($answers['potential_start_date'] ?? '')) !== '')
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ __('admin.messages.collaboration_assessment.manager.labels.potential_start_date') }}:
                                        {{ $answers['potential_start_date'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.incoming_invoices_monthly') }}:
                                    {{ $answers['incoming_invoices_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.outgoing_invoices_monthly') }}:
                                    {{ $answers['outgoing_invoices_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.bank_accounts_monthly') }}:
                                    {{ $answers['bank_accounts_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.payroll_calculations_monthly') }}:
                                    {{ $answers['payroll_calculations_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.inventory_bookkeeping') }}:
                                    {{ $booleanMap[(string) ($answers['inventory_bookkeeping'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.cost_centers_tracking') }}:
                                    {{ $booleanMap[(string) ($answers['cost_centers_tracking'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.collaboration_assessment.manager.labels.monthly_reporting') }}:
                                    {{ $booleanMap[(string) ($answers['monthly_reporting'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided') }}
                                </div>
                                @if (trim((string) ($answers['additional_requirements'] ?? '')) !== '')
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ __('admin.messages.collaboration_assessment.manager.labels.additional_requirements') }}:
                                        {{ \Illuminate\Support\Str::limit((string) $answers['additional_requirements'], 120) }}
                                    </div>
                                @endif
                                @if (trim((string) ($answers['tax_issues'] ?? '')) !== '')
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ __('admin.messages.collaboration_assessment.manager.labels.tax_issues') }}:
                                        {{ \Illuminate\Support\Str::limit((string) $answers['tax_issues'], 120) }}
                                    </div>
                                @endif
                                <details class="mt-3 rounded-xl border border-slate-200 bg-white p-3 text-left">
                                    <summary class="flex cursor-pointer list-none items-center gap-2 text-xs font-semibold text-slate-700">
                                        <i class="fa-regular fa-list-check text-amber-700" aria-hidden="true"></i>
                                        <span>{{ __('admin.messages.collaboration_assessment.manager.all_details') }}</span>
                                    </summary>
                                    <dl class="mt-3 space-y-3 border-t border-slate-100 pt-3">
                                        @foreach ($detailFields as $field)
                                            @php
                                                $rawValue = trim((string) ($answers[$field] ?? ''));
                                                $displayValue = $booleanMap[$rawValue] ?? $rawValue;
                                            @endphp
                                            @if ($displayValue !== '')
                                                <div>
                                                    <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500">{{ __('assessment.form.'.$field) }}</dt>
                                                    <dd class="mt-1 whitespace-pre-line break-words text-xs text-slate-800">{{ $displayValue }}</dd>
                                                </div>
                                            @endif
                                        @endforeach
                                    </dl>
                                </details>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ __('admin.messages.collaboration_assessment.status.'.$row->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                {{ $row->created_at?->format('Y-m-d H:i') ?? '-' }}
                                @if ($row->reviewed_at)
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        {{ __('admin.messages.collaboration_assessment.manager.reviewed_by', ['name' => $row->reviewer?->name ?: __('admin.layout.admin')]) }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $row->reviewed_at->format('Y-m-d H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="markAsNew({{ (int) $row->id }})"
                                        class="rounded-lg border border-amber-200 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                                    >
                                        {{ __('admin.messages.collaboration_assessment.manager.actions.mark_new') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsRead({{ (int) $row->id }})"
                                        class="rounded-lg border border-sky-200 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        {{ __('admin.messages.collaboration_assessment.manager.actions.mark_read') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsResolved({{ (int) $row->id }})"
                                        class="rounded-lg border border-emerald-200 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                    >
                                        {{ __('admin.messages.collaboration_assessment.manager.actions.mark_resolved') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('admin.messages.collaboration_assessment.manager.empty') }}</td>
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
