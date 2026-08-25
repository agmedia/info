@extends('front.desktop.layouts.store')

@section('title', __('lease_calculator.page_title'))
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    @php
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''));
        $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    @endphp

    <div class="ac-lease-page" data-lease-calculator-root>
        <section class="ac-tool-intro" aria-labelledby="ac-lease-title">
            <div class="ac-tool-container ac-tool-intro-layout">
                <div class="ac-tool-intro-heading">
                    @php($leaseHeadingWords = $headingWords(__('lease_calculator.heading')))
                    <h1 class="values-title services-index-intro-title ac-tool-display-title" id="ac-lease-title" data-words-slide-from-right aria-label="{{ __('lease_calculator.heading') }}">
                        @foreach ($leaseHeadingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last && count($leaseHeadingWords) > 1 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="ac-tool-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <p>{{ __('lease_calculator.subheading') }}</p>
                </div>
            </div>
        </section>

        <section class="ac-lease-shell ac-tool-content-section" aria-labelledby="ac-lease-form-title">
            <div class="ac-tool-container">
                <section class="ac-lease-card content-reveal animation-index-0" data-image-reveal>
                    <div class="ac-lease-card-head">
                        <div class="ac-lease-card-head-copy">
                            <p class="ac-lease-kicker">{{ __('lease_calculator.form.kicker') }}</p>
                            <h2 id="ac-lease-form-title">{{ __('lease_calculator.form.title') }}</h2>
                            <p>{{ __('lease_calculator.form.intro') }}</p>
                        </div>

                        <p class="ac-lease-card-description">{{ __('lease_calculator.intro') }}</p>
                    </div>

                    <form
                        class="ac-lease-form"
                        data-lease-calculator
                        data-locale="{{ app()->getLocale() }}"
                        data-error-required="{{ __('lease_calculator.form.validation_required') }}"
                        data-error-range="{{ __('lease_calculator.form.validation_range') }}"
                        data-error-numeric="{{ __('lease_calculator.form.validation_numeric') }}"
                        novalidate
                    >
                        <div class="ac-lease-rows">
                            <div class="ac-lease-row">
                                <div class="ac-lease-row-label">
                                    <label for="lease-start-date">{{ __('lease_calculator.form.start_date') }}</label>
                                </div>
                                <div class="ac-lease-row-control">
                                    <div class="ac-lease-date-field" data-lease-date-field="lease-start-date">
                                        <span class="ac-lease-date-display is-placeholder" data-lease-date-display="lease-start-date">{{ __('lease_calculator.form.date_placeholder') }}</span>
                                        <input id="lease-start-date" type="date" name="start_date" class="ac-lease-input ac-lease-date-input">
                                        <button
                                            type="button"
                                            class="ac-lease-date-trigger"
                                            data-lease-date-trigger="lease-start-date"
                                            aria-expanded="false"
                                            aria-label="{{ __('lease_calculator.form.open_calendar') }}: {{ __('lease_calculator.form.start_date') }}"
                                        >
                                            <i class="fa-light fa-calendar-days" aria-hidden="true"></i>
                                        </button>
                                        <div class="ac-lease-calendar" data-lease-calendar-panel hidden>
                                            <div class="ac-lease-calendar-head">
                                                <button type="button" class="ac-lease-calendar-nav" data-lease-calendar-prev aria-label="{{ __('lease_calculator.form.previous_month') }}">
                                                    <i class="fa-light fa-chevron-left" aria-hidden="true"></i>
                                                </button>
                                                <div class="ac-lease-calendar-title" data-lease-calendar-title></div>
                                                <label class="sr-only" for="lease-start-year">{{ __('lease_calculator.form.select_year') }}</label>
                                                <select id="lease-start-year" class="ac-lease-calendar-year" data-lease-calendar-year></select>
                                                <button type="button" class="ac-lease-calendar-nav" data-lease-calendar-next aria-label="{{ __('lease_calculator.form.next_month') }}">
                                                    <i class="fa-light fa-chevron-right" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div class="ac-lease-calendar-weekdays" data-lease-calendar-weekdays></div>
                                            <div class="ac-lease-calendar-grid" data-lease-calendar-grid></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ac-lease-row">
                                <div class="ac-lease-row-label">
                                    <label for="lease-monthly-payment">{{ __('lease_calculator.form.monthly_payment') }}</label>
                                </div>
                                <div class="ac-lease-row-control">
                                    <input
                                        id="lease-monthly-payment"
                                        type="text"
                                        name="monthly_payment"
                                        class="ac-lease-input"
                                        inputmode="decimal"
                                        placeholder="{{ __('lease_calculator.form.monthly_payment_placeholder') }}"
                                    >
                                </div>
                            </div>

                            <div class="ac-lease-row">
                                <div class="ac-lease-row-label">
                                    <label for="lease-interest-rate">{{ __('lease_calculator.form.interest_rate') }}</label>
                                </div>
                                <div class="ac-lease-row-control">
                                    <input
                                        id="lease-interest-rate"
                                        type="text"
                                        name="interest_rate"
                                        class="ac-lease-input"
                                        inputmode="decimal"
                                        placeholder="{{ __('lease_calculator.form.interest_rate_placeholder') }}"
                                    >
                                </div>
                            </div>

                            <div class="ac-lease-row">
                                <div class="ac-lease-row-label">
                                    <label for="lease-end-date">{{ __('lease_calculator.form.end_date') }}</label>
                                </div>
                                <div class="ac-lease-row-control">
                                    <div class="ac-lease-date-field" data-lease-date-field="lease-end-date">
                                        <span class="ac-lease-date-display is-placeholder" data-lease-date-display="lease-end-date">{{ __('lease_calculator.form.date_placeholder') }}</span>
                                        <input id="lease-end-date" type="date" name="end_date" class="ac-lease-input ac-lease-date-input">
                                        <button
                                            type="button"
                                            class="ac-lease-date-trigger"
                                            data-lease-date-trigger="lease-end-date"
                                            aria-expanded="false"
                                            aria-label="{{ __('lease_calculator.form.open_calendar') }}: {{ __('lease_calculator.form.end_date') }}"
                                        >
                                            <i class="fa-light fa-calendar-days" aria-hidden="true"></i>
                                        </button>
                                        <div class="ac-lease-calendar" data-lease-calendar-panel hidden>
                                            <div class="ac-lease-calendar-head">
                                                <button type="button" class="ac-lease-calendar-nav" data-lease-calendar-prev aria-label="{{ __('lease_calculator.form.previous_month') }}">
                                                    <i class="fa-light fa-chevron-left" aria-hidden="true"></i>
                                                </button>
                                                <div class="ac-lease-calendar-title" data-lease-calendar-title></div>
                                                <label class="sr-only" for="lease-end-year">{{ __('lease_calculator.form.select_year') }}</label>
                                                <select id="lease-end-year" class="ac-lease-calendar-year" data-lease-calendar-year></select>
                                                <button type="button" class="ac-lease-calendar-nav" data-lease-calendar-next aria-label="{{ __('lease_calculator.form.next_month') }}">
                                                    <i class="fa-light fa-chevron-right" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div class="ac-lease-calendar-weekdays" data-lease-calendar-weekdays></div>
                                            <div class="ac-lease-calendar-grid" data-lease-calendar-grid></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ac-lease-row is-output" data-lease-output-row hidden>
                                <div class="ac-lease-row-label">
                                    <span>{{ __('lease_calculator.outputs.months') }}</span>
                                </div>
                                <div class="ac-lease-row-control">
                                    <output class="ac-lease-output is-placeholder" data-lease-output="months" data-placeholder="{{ __('lease_calculator.output_placeholders.months') }}">{{ __('lease_calculator.output_placeholders.months') }}</output>
                                </div>
                            </div>

                            <div class="ac-lease-row is-output" data-lease-output-row hidden>
                                <div class="ac-lease-row-label">
                                    <span>{{ __('lease_calculator.outputs.total_lease') }}</span>
                                </div>
                                <div class="ac-lease-row-control">
                                    <output class="ac-lease-output is-placeholder" data-lease-output="total_lease" data-placeholder="{{ __('lease_calculator.output_placeholders.total_lease') }}">{{ __('lease_calculator.output_placeholders.total_lease') }}</output>
                                </div>
                            </div>

                            <div class="ac-lease-row is-output" data-lease-output-row hidden>
                                <div class="ac-lease-row-label">
                                    <span>{{ __('lease_calculator.outputs.initial_liability') }}</span>
                                </div>
                                <div class="ac-lease-row-control">
                                    <output class="ac-lease-output is-placeholder" data-lease-output="initial_liability" data-placeholder="{{ __('lease_calculator.output_placeholders.initial_liability') }}">{{ __('lease_calculator.output_placeholders.initial_liability') }}</output>
                                </div>
                            </div>

                            <div class="ac-lease-row is-output" data-lease-output-row hidden>
                                <div class="ac-lease-row-label">
                                    <span>{{ __('lease_calculator.outputs.monthly_depreciation') }}</span>
                                </div>
                                <div class="ac-lease-row-control">
                                    <output class="ac-lease-output is-placeholder" data-lease-output="monthly_depreciation" data-placeholder="{{ __('lease_calculator.output_placeholders.monthly_depreciation') }}">{{ __('lease_calculator.output_placeholders.monthly_depreciation') }}</output>
                                </div>
                            </div>

                            <div class="ac-lease-row is-output" data-lease-output-row hidden>
                                <div class="ac-lease-row-label">
                                    <span>{{ __('lease_calculator.outputs.interest_total') }}</span>
                                </div>
                                <div class="ac-lease-row-control">
                                    <output class="ac-lease-output is-placeholder" data-lease-output="interest_total" data-placeholder="{{ __('lease_calculator.output_placeholders.interest_total') }}">{{ __('lease_calculator.output_placeholders.interest_total') }}</output>
                                </div>
                            </div>
                        </div>

                        <p class="ac-lease-form-error" data-lease-error hidden></p>

                        <div class="ac-lease-form-actions">
                            <button type="submit" class="editorial-dark-button">
                                <span>{{ __('lease_calculator.form.calculate') }}</span>
                            </button>
                            <button type="reset" class="button button-outline ac-lease-reset">
                                <span>{{ __('lease_calculator.form.reset') }}</span>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="ac-lease-results" data-lease-results hidden>
                    <div class="ac-lease-results-head">
                        <p class="ac-lease-kicker">{{ __('lease_calculator.results.kicker') }}</p>
                        <h2>{{ __('lease_calculator.results.title') }}</h2>
                        <p>{{ __('lease_calculator.results.intro') }}</p>
                    </div>

                    <div class="ac-lease-table-wrap" data-lease-results-table hidden>
                        <table class="ac-lease-table">
                            <thead>
                                <tr>
                                    <th>{{ __('lease_calculator.results.period') }}</th>
                                    <th>{{ __('lease_calculator.results.date') }}</th>
                                    <th>{{ __('lease_calculator.results.lease_liability') }}</th>
                                    <th>{{ __('lease_calculator.results.liability_reduction') }}</th>
                                    <th>{{ __('lease_calculator.results.depreciation_expense') }}</th>
                                    <th>{{ __('lease_calculator.results.lease_payment') }}</th>
                                    <th>{{ __('lease_calculator.results.interest_expense') }}</th>
                                </tr>
                            </thead>
                            <tbody data-lease-results-body></tbody>
                        </table>
                    </div>

                    <p class="ac-lease-disclaimer">
                        <strong>{{ __('lease_calculator.disclaimer_label') }}:</strong>
                        {{ __('lease_calculator.disclaimer') }}
                        @if ($contactEmail !== '')
                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                        @endif
                    </p>
                </section>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            (function () {
                const root = document.querySelector('[data-lease-calculator-root]');
                if (!root) {
                    return;
                }

                const form = root.querySelector('[data-lease-calculator]');
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const startInput = form.elements.namedItem('start_date');
                const endInput = form.elements.namedItem('end_date');
                const monthlyPaymentInput = form.elements.namedItem('monthly_payment');
                const interestRateInput = form.elements.namedItem('interest_rate');
                const errorNode = root.querySelector('[data-lease-error]');
                const resultsSection = root.querySelector('[data-lease-results]');
                const resultsTableWrap = root.querySelector('[data-lease-results-table]');
                const resultsBody = root.querySelector('[data-lease-results-body]');
                const dateTriggers = Array.from(root.querySelectorAll('[data-lease-date-trigger]'));
                const dateDisplays = Array.from(root.querySelectorAll('[data-lease-date-display]'));
                const dateFields = Array.from(root.querySelectorAll('[data-lease-date-field]'));
                const outputRows = Array.from(root.querySelectorAll('[data-lease-output-row]'));
                const outputNodes = {
                    months: root.querySelector('[data-lease-output="months"]'),
                    totalLease: root.querySelector('[data-lease-output="total_lease"]'),
                    initialLiability: root.querySelector('[data-lease-output="initial_liability"]'),
                    monthlyDepreciation: root.querySelector('[data-lease-output="monthly_depreciation"]'),
                    interestTotal: root.querySelector('[data-lease-output="interest_total"]'),
                };

                const locale = form.dataset.locale === 'hr' ? 'hr-HR' : 'en-US';
                const numberFormatter = new Intl.NumberFormat(locale, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
                const integerFormatter = new Intl.NumberFormat(locale, {
                    maximumFractionDigits: 0,
                });
                const dateFormatter = new Intl.DateTimeFormat(locale, {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                });
                const datePlaceholder = locale === 'hr-HR' ? 'dd.mm.gggg' : 'mm/dd/yyyy';
                const calendarWeekdays = locale === 'hr-HR'
                    ? ['Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub', 'Ned']
                    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                const calendarMonthFormatter = new Intl.DateTimeFormat(locale, {
                    month: 'long',
                });

                const formatAmount = function (value) {
                    return numberFormatter.format(Number.isFinite(value) ? value : 0);
                };

                const capitalize = function (value) {
                    return value.charAt(0).toUpperCase() + value.slice(1);
                };

                const formatInputDate = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    return year + '-' + month + '-' + day;
                };

                const formatDateValue = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    if (locale === 'hr-HR') {
                        return day + '.' + month + '.' + year + '.';
                    }

                    return month + '/' + day + '/' + year;
                };

                const parseDate = function (value) {
                    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || '').trim());
                    if (!match) {
                        return null;
                    }

                    const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
                    return Number.isNaN(date.getTime()) ? null : date;
                };

                const parseDecimal = function (value) {
                    let normalized = String(value || '').trim().replace(/\s+/g, '');
                    if (normalized === '') {
                        return Number.NaN;
                    }

                    if (normalized.includes(',') && normalized.includes('.')) {
                        if (normalized.lastIndexOf(',') > normalized.lastIndexOf('.')) {
                            normalized = normalized.replace(/\./g, '').replace(',', '.');
                        } else {
                            normalized = normalized.replace(/,/g, '');
                        }
                    } else if (normalized.includes(',')) {
                        normalized = normalized.replace(',', '.');
                    }

                    const parsed = Number(normalized);
                    return Number.isFinite(parsed) ? parsed : Number.NaN;
                };

                const getPeriodCount = function (startDate, endDate) {
                    let months = (endDate.getFullYear() - startDate.getFullYear()) * 12;
                    months += endDate.getMonth() - startDate.getMonth();

                    if (endDate.getDate() >= startDate.getDate()) {
                        months += 1;
                    }

                    return months;
                };

                const buildPeriodDate = function (startDate, period) {
                    return new Date(
                        startDate.getFullYear(),
                        startDate.getMonth() + period,
                        startDate.getDate() - 1
                    );
                };

                const presentValue = function (payment, monthlyRate, periods) {
                    if (monthlyRate === 0) {
                        return payment * periods;
                    }

                    return payment * (1 - Math.pow(1 + monthlyRate, -periods)) / monthlyRate;
                };

                const setOutput = function (node, value, isPlaceholder) {
                    if (!node) {
                        return;
                    }

                    node.textContent = value;
                    node.classList.toggle('is-placeholder', Boolean(isPlaceholder));
                };

                const hideError = function () {
                    if (!errorNode) {
                        return;
                    }

                    errorNode.hidden = true;
                    errorNode.textContent = '';
                };

                const showError = function (message) {
                    if (!errorNode) {
                        return;
                    }

                    errorNode.hidden = false;
                    errorNode.textContent = message;
                };

                const resetOutputs = function () {
                    Object.values(outputNodes).forEach(function (node) {
                        if (!node) {
                            return;
                        }

                        setOutput(node, node.dataset.placeholder || '—', true);
                    });
                };

                const setCalendarMonth = function (field, monthDate) {
                    field.dataset.calendarMonth = String(monthDate.getFullYear()) + '-' + String(monthDate.getMonth());
                };

                const getCalendarMonth = function (field, fallbackDate) {
                    const storedValue = String(field.dataset.calendarMonth || '');
                    const parts = storedValue.split('-');

                    if (parts.length === 2) {
                        const year = Number(parts[0]);
                        const month = Number(parts[1]);
                        if (Number.isInteger(year) && Number.isInteger(month)) {
                            return new Date(year, month, 1);
                        }
                    }

                    return new Date(fallbackDate.getFullYear(), fallbackDate.getMonth(), 1);
                };

                const closeCalendars = function () {
                    dateFields.forEach(function (field) {
                        const panel = field.querySelector('[data-lease-calendar-panel]');
                        const trigger = field.querySelector('[data-lease-date-trigger]');

                        field.classList.remove('is-open');

                        if (panel) {
                            panel.hidden = true;
                        }

                        if (trigger instanceof HTMLButtonElement) {
                            trigger.setAttribute('aria-expanded', 'false');
                        }
                    });
                };

                const renderCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const title = field.querySelector('[data-lease-calendar-title]');
                    const yearSelect = field.querySelector('[data-lease-calendar-year]');
                    const weekdays = field.querySelector('[data-lease-calendar-weekdays]');
                    const grid = field.querySelector('[data-lease-calendar-grid]');

                    if (!(input instanceof HTMLInputElement) || !title || !weekdays || !grid) {
                        return;
                    }

                    const selectedDate = parseDate(input.value);
                    const fallbackDate = selectedDate || new Date();
                    const monthDate = getCalendarMonth(field, fallbackDate);
                    const today = new Date();
                    const currentYear = today.getFullYear();
                    const daysInMonth = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
                    const firstWeekday = (new Date(monthDate.getFullYear(), monthDate.getMonth(), 1).getDay() + 6) % 7;
                    const cells = [];

                    title.textContent = capitalize(calendarMonthFormatter.format(monthDate));

                    if (yearSelect instanceof HTMLSelectElement) {
                        const minYear = Math.min(currentYear - 20, monthDate.getFullYear() - 5);
                        const maxYear = Math.max(currentYear + 30, monthDate.getFullYear() + 5);
                        const options = [];

                        for (let year = minYear; year <= maxYear; year += 1) {
                            options.push('<option value="' + year + '"' + (year === monthDate.getFullYear() ? ' selected' : '') + '>' + year + '</option>');
                        }

                        yearSelect.innerHTML = options.join('');
                        yearSelect.value = String(monthDate.getFullYear());
                    }

                    weekdays.innerHTML = calendarWeekdays.map(function (label) {
                        return '<span>' + label + '</span>';
                    }).join('');

                    for (let index = 0; index < firstWeekday; index += 1) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    for (let day = 1; day <= daysInMonth; day += 1) {
                        const cellDate = new Date(monthDate.getFullYear(), monthDate.getMonth(), day);
                        const isoDate = formatInputDate(cellDate);
                        const isSelected = selectedDate && formatInputDate(selectedDate) === isoDate;
                        const isToday = formatInputDate(today) === isoDate;

                        cells.push(
                            '<button type="button" class="ac-lease-calendar-day' +
                                (isSelected ? ' is-selected' : '') +
                                (isToday ? ' is-today' : '') +
                            '" data-lease-calendar-day="' + isoDate + '">' + day + '</button>'
                        );
                    }

                    while (cells.length % 7 !== 0) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    grid.innerHTML = cells.join('');

                    grid.querySelectorAll('[data-lease-calendar-day]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const isoDate = button.getAttribute('data-lease-calendar-day');
                            if (!isoDate) {
                                return;
                            }

                            input.value = isoDate;
                            syncDateDisplays();
                            closeCalendars();
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    });
                };

                const openCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const panel = field.querySelector('[data-lease-calendar-panel]');
                    const trigger = field.querySelector('[data-lease-date-trigger]');
                    const selectedDate = input instanceof HTMLInputElement ? parseDate(input.value) : null;
                    const baseDate = selectedDate || new Date();

                    if (window.matchMedia('(max-width: 760px)').matches) {
                        if (input instanceof HTMLInputElement) {
                            if (typeof input.showPicker === 'function') {
                                input.showPicker();
                            } else {
                                input.focus();
                                input.click();
                            }
                        }
                        return;
                    }

                    closeCalendars();
                    setCalendarMonth(field, new Date(baseDate.getFullYear(), baseDate.getMonth(), 1));
                    renderCalendar(field);

                    field.classList.add('is-open');

                    if (panel) {
                        panel.hidden = false;
                    }

                    if (trigger instanceof HTMLButtonElement) {
                        trigger.setAttribute('aria-expanded', 'true');
                    }
                };

                const syncDateDisplays = function () {
                    dateDisplays.forEach(function (display) {
                        const targetId = display.getAttribute('data-lease-date-display');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const parsedDate = parseDate(input.value);
                        const hasValue = parsedDate instanceof Date;

                        display.textContent = hasValue ? formatDateValue(parsedDate) : datePlaceholder;
                        display.classList.toggle('is-placeholder', !hasValue);
                    });
                };

                const setComputedVisibility = function (isVisible) {
                    outputRows.forEach(function (row) {
                        row.hidden = !isVisible;
                    });

                    if (resultsSection) {
                        resultsSection.hidden = !isVisible;
                    }
                };

                const resetResults = function () {
                    hideError();
                    resetOutputs();

                    if (resultsBody) {
                        resultsBody.innerHTML = '';
                    }

                    if (resultsTableWrap) {
                        resultsTableWrap.hidden = true;
                    }
                    setComputedVisibility(false);
                };

                const renderResults = function (rows) {
                    if (!resultsBody) {
                        return;
                    }

                    resultsBody.innerHTML = rows.map(function (row) {
                        return '<tr>' +
                            '<td>' + integerFormatter.format(row.period) + '</td>' +
                            '<td>' + dateFormatter.format(row.date) + '</td>' +
                            '<td>' + formatAmount(row.leaseLiability) + '</td>' +
                            '<td>' + formatAmount(row.liabilityReduction) + '</td>' +
                            '<td>' + formatAmount(row.depreciationExpense) + '</td>' +
                            '<td>' + formatAmount(row.leasePayment) + '</td>' +
                            '<td>' + formatAmount(row.interestExpense) + '</td>' +
                        '</tr>';
                    }).join('');

                    if (resultsTableWrap) {
                        resultsTableWrap.hidden = rows.length === 0;
                    }
                    setComputedVisibility(rows.length > 0);
                };

                const calculate = function (silent) {
                    hideError();

                    const startDate = parseDate(startInput && 'value' in startInput ? startInput.value : '');
                    const endDate = parseDate(endInput && 'value' in endInput ? endInput.value : '');
                    const monthlyPayment = parseDecimal(monthlyPaymentInput && 'value' in monthlyPaymentInput ? monthlyPaymentInput.value : '');
                    const annualInterest = parseDecimal(interestRateInput && 'value' in interestRateInput ? interestRateInput.value : '');

                    if (!startDate || !endDate || !Number.isFinite(monthlyPayment) || !Number.isFinite(annualInterest)) {
                        resetResults();
                        if (!silent) {
                            showError(form.dataset.errorRequired || '');
                        }
                        return;
                    }

                    if (monthlyPayment <= 0 || annualInterest < 0) {
                        resetResults();
                        if (!silent) {
                            showError(form.dataset.errorNumeric || '');
                        }
                        return;
                    }

                    const periods = getPeriodCount(startDate, endDate);

                    if (periods < 1) {
                        resetResults();
                        if (!silent) {
                            showError(form.dataset.errorRange || '');
                        }
                        return;
                    }

                    const monthlyRate = annualInterest / 100 / 12;
                    const totalLease = monthlyPayment * periods;
                    const initialLiability = presentValue(monthlyPayment, monthlyRate, periods);
                    const monthlyDepreciation = initialLiability / periods;

                    let openingLiability = initialLiability;
                    let totalInterest = 0;
                    const rows = [];

                    for (let period = 1; period <= periods; period += 1) {
                        const interestExpense = openingLiability * monthlyRate;
                        let liabilityReduction = monthlyPayment - interestExpense;

                        if (liabilityReduction < 0) {
                            liabilityReduction = 0;
                        }

                        if (liabilityReduction > openingLiability) {
                            liabilityReduction = openingLiability;
                        }

                        const closingLiability = Math.max(0, openingLiability - liabilityReduction);

                        rows.push({
                            period: period,
                            date: buildPeriodDate(startDate, period),
                            leaseLiability: openingLiability,
                            liabilityReduction: liabilityReduction,
                            depreciationExpense: monthlyDepreciation,
                            leasePayment: monthlyPayment,
                            interestExpense: interestExpense,
                        });

                        totalInterest += interestExpense;
                        openingLiability = closingLiability;
                    }

                    setOutput(outputNodes.months, integerFormatter.format(periods));
                    setOutput(outputNodes.totalLease, formatAmount(totalLease));
                    setOutput(outputNodes.initialLiability, formatAmount(initialLiability));
                    setOutput(outputNodes.monthlyDepreciation, formatAmount(monthlyDepreciation));
                    setOutput(outputNodes.interestTotal, formatAmount(totalInterest));

                    renderResults(rows);
                };

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    calculate(false);
                });

                form.addEventListener('reset', function () {
                    window.setTimeout(function () {
                        resetResults();
                    }, 0);
                });

                [startInput, endInput, monthlyPaymentInput, interestRateInput].forEach(function (field) {
                    if (!(field instanceof HTMLInputElement)) {
                        return;
                    }

                    const handleFieldChange = function () {
                        syncDateDisplays();

                        if (resultsSection && !resultsSection.hidden) {
                            calculate(true);
                        }
                    };

                    field.addEventListener('input', handleFieldChange);
                    field.addEventListener('change', handleFieldChange);
                });

                dateTriggers.forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        const targetId = trigger.getAttribute('data-lease-date-trigger');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const field = input.closest('[data-lease-date-field]');
                        if (!field) {
                            return;
                        }

                        openCalendar(field);
                    });
                });

                dateFields.forEach(function (field) {
                    const display = field.querySelector('[data-lease-date-display]');
                    const prevButton = field.querySelector('[data-lease-calendar-prev]');
                    const nextButton = field.querySelector('[data-lease-calendar-next]');
                    const yearSelect = field.querySelector('[data-lease-calendar-year]');

                    if (display) {
                        display.addEventListener('click', function () {
                            openCalendar(field);
                        });
                    }

                    if (prevButton instanceof HTMLButtonElement) {
                        prevButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (nextButton instanceof HTMLButtonElement) {
                        nextButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (yearSelect instanceof HTMLSelectElement) {
                        yearSelect.addEventListener('change', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            const nextYear = Number(yearSelect.value);

                            if (!Number.isInteger(nextYear)) {
                                return;
                            }

                            setCalendarMonth(field, new Date(nextYear, currentMonth.getMonth(), 1));
                            renderCalendar(field);
                        });
                    }
                });

                document.addEventListener('click', function (event) {
                    if (event.target.closest('[data-lease-date-field]')) {
                        return;
                    }

                    closeCalendars();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeCalendars();
                    }
                });

                syncDateDisplays();
                resetResults();
            }());
        </script>
    @endpush
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/tool-pages.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/tool-pages.css')) }}">
@endpush
