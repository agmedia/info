@php
    $notificationTotal = (int) ($notifications['total'] ?? 0);
    $notificationGroups = (array) ($notifications['groups'] ?? []);
    $notificationBadge = $notificationTotal > 99 ? '99+' : (string) $notificationTotal;
@endphp

@if ($notificationGroups !== [])
    <details class="admin-message-notifications" data-admin-message-notifications>
        <summary
            class="admin-message-notifications__trigger"
            aria-label="{{ __('admin.layout.notifications.open', ['count' => $notificationTotal]) }}"
            aria-controls="admin-message-notifications-panel"
            aria-expanded="false"
        >
            <i class="fa-light fa-envelope" aria-hidden="true"></i>
            @if ($notificationTotal > 0)
                <span class="admin-message-notifications__badge" aria-hidden="true">{{ $notificationBadge }}</span>
            @endif
        </summary>

        <div id="admin-message-notifications-panel" class="admin-message-notifications__panel">
            <div class="admin-message-notifications__header">
                <div>
                    <p class="admin-message-notifications__eyebrow">{{ __('admin.layout.notifications.eyebrow') }}</p>
                    <h2 class="admin-message-notifications__title">{{ __('admin.layout.notifications.title') }}</h2>
                </div>
                <span
                    class="admin-message-notifications__total"
                    data-admin-message-notification-count="{{ $notificationTotal }}"
                    aria-label="{{ __('admin.layout.notifications.total', ['count' => $notificationTotal]) }}"
                >
                    {{ $notificationTotal }}
                </span>
            </div>

            @if ($notificationTotal === 0)
                <p class="admin-message-notifications__empty">
                    <i class="fa-light fa-circle-check" aria-hidden="true"></i>
                    <span>{{ __('admin.layout.notifications.empty') }}</span>
                </p>
            @endif

            <nav aria-label="{{ __('admin.layout.notifications.navigation') }}">
                <ul class="admin-message-notifications__groups">
                    @foreach ($notificationGroups as $group)
                        @php
                            $groupCount = (int) ($group['count'] ?? 0);
                        @endphp
                        <li>
                            <a
                                href="{{ $group['url'] }}"
                                class="admin-message-notifications__group {{ $groupCount > 0 ? 'has-new' : '' }}"
                                data-admin-message-group="{{ $group['key'] }}"
                                data-count="{{ $groupCount }}"
                            >
                                <span class="admin-message-notifications__group-icon" aria-hidden="true">
                                    <i class="{{ $group['icon'] }}"></i>
                                </span>
                                <span class="admin-message-notifications__group-copy">
                                    <span class="admin-message-notifications__group-label">{{ $group['label'] }}</span>
                                    <span class="admin-message-notifications__group-status">
                                        {{ $groupCount > 0
                                            ? __('admin.layout.notifications.group_count', ['count' => $groupCount])
                                            : __('admin.layout.notifications.group_empty') }}
                                    </span>
                                </span>
                                <span class="admin-message-notifications__group-count" aria-hidden="true">{{ $groupCount }}</span>
                                <i class="fa-light fa-chevron-right admin-message-notifications__group-arrow" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </details>
@endif
