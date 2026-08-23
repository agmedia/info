<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('Settings') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Central place for public-site email, branding, content, integrations, and metadata settings.') }}</p>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <div class="mb-4 flex flex-wrap gap-2">
            @foreach ([
                'email' => 'Email',
                'appearance' => 'Appearance',
                'hero' => 'Homepage Hero',
                'branding' => 'Branding & Footer',
                'blog' => 'Blog',
                'integrations' => 'Integrations',
                'seo' => 'SEO',
                'og' => 'OG / Twitter',
                'schema' => 'Schema Markup',
            ] as $tabKey => $tabLabel)
                <button type="button" wire:click="$set('tab', '{{ $tabKey }}')" class="rounded-xl border px-3 py-1.5 text-xs font-semibold {{ $tab === $tabKey ? 'border-cyan-700 bg-cyan-700 text-white' : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                    {{ __($tabLabel) }}
                </button>
            @endforeach
        </div>

        <form wire:submit="save" class="space-y-4">
            @if ($tab === 'email')
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_email_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Enable email notifications') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Mailer') }}</label>
                        <select wire:model="form.store_email_mailer" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="smtp">{{ __('SMTP') }}</option>
                            <option value="sendmail">{{ __('Sendmail') }}</option>
                            <option value="log">{{ __('Log') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('From Address') }}</label>
                        <input type="email" wire:model="form.store_email_from_address" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.store_email_from_address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('From Name') }}</label>
                        <input type="text" wire:model="form.store_email_from_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Reply-To') }}</label>
                        <input type="email" wire:model="form.store_email_reply_to" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.store_email_reply_to') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Forms To') }}</label>
                        <input type="email" wire:model="form.store_email_contact_to" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.store_email_contact_to') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('SMTP Host') }}</label>
                            <input type="text" wire:model="form.store_email_smtp_host" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('SMTP Port') }}</label>
                            <input type="number" wire:model="form.store_email_smtp_port" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Encryption') }}</label>
                            <select wire:model="form.store_email_smtp_encryption" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="">{{ __('None') }}</option>
                                <option value="tls">{{ __('TLS') }}</option>
                                <option value="ssl">{{ __('SSL') }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('SMTP Username') }}</label>
                        <input type="text" wire:model="form.store_email_smtp_username" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('SMTP Password') }}</label>
                        <input type="password" wire:model="form.store_email_smtp_password" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sendmail Path') }}</label>
                        <input type="text" wire:model="form.store_email_sendmail_path" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            @endif

            @if ($tab === 'appearance')
                <div class="grid gap-4 md:grid-cols-2">
                    <div wire:key="website-font-field-{{ $form['store_front_google_font'] ?? 'default' }}">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-front-google-font">{{ __('Website font') }}</label>
                        <select id="store-front-google-font" wire:model.live="form.store_front_google_font" data-tom-select placeholder="{{ __('Search fonts...') }}" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach ($fontOptions as $fontKey => $fontLabel)
                                <option value="{{ $fontKey }}" @selected(($form['store_front_google_font'] ?? null) === $fontKey)>{{ $fontLabel }}</option>
                            @endforeach
                        </select>
                        @error('form.store_front_google_font') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        <p class="font-semibold text-slate-800">{{ __('Font catalog') }}</p>
                        <p class="mt-1">{{ __('Search the expanded Google Fonts catalog or choose General Sans from Fontshare. The selected font is used across the public website.') }}</p>
                    </div>
                </div>
            @endif

            @if ($tab === 'hero')
                <div class="space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        <p class="font-semibold text-slate-800">{{ __('Homepage Hero') }}</p>
                        <p class="mt-1">{{ __('Ove postavke vrijede samo za veliki hero na početnoj stranici. Font je odvojen od općeg fonta web-stranice, a desktop i mobilni video učitavaju se zasebno.') }}</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div wire:key="hero-font-field-{{ $form['store_home_hero_font'] ?? 'default' }}">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-font">{{ __('Hero font') }}</label>
                            <select id="store-home-hero-font" wire:model.live="form.store_home_hero_font" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                                @foreach ($heroFontOptions as $fontKey => $fontLabel)
                                    <option value="{{ $fontKey }}" @selected(($form['store_home_hero_font'] ?? null) === $fontKey)>{{ $fontLabel }}</option>
                                @endforeach
                            </select>
                            @error('form.store_home_hero_font') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div wire:key="hero-font-weight-field-{{ $form['store_home_hero_font'] ?? 'default' }}-{{ $form['store_home_hero_font_weight'] ?? 'default' }}">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-font-weight">{{ __('Hero weight') }}</label>
                            <select id="store-home-hero-font-weight" wire:model="form.store_home_hero_font_weight" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                @foreach ($heroFontWeightOptions as $fontWeight => $fontWeightLabel)
                                    <option value="{{ $fontWeight }}">{{ $fontWeightLabel }}</option>
                                @endforeach
                            </select>
                            @error('form.store_home_hero_font_weight') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-title">{{ __('Naslov') }}</label>
                            <input id="store-home-hero-title" type="text" wire:model="form.store_home_hero_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.store_home_hero_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-subtitle">{{ __('Podnaslov') }}</label>
                            <textarea id="store-home-hero-subtitle" rows="3" wire:model="form.store_home_hero_subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            @error('form.store_home_hero_subtitle') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-primary-label">{{ __('Tekst glavnog gumba') }}</label>
                            <input id="store-home-hero-primary-label" type="text" wire:model="form.store_home_hero_primary_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.store_home_hero_primary_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-primary-url">{{ __('Link glavnog gumba') }}</label>
                            <input id="store-home-hero-primary-url" type="text" wire:model="form.store_home_hero_primary_url" placeholder="/contact ili https://..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.store_home_hero_primary_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-secondary-label">{{ __('Tekst drugog gumba') }}</label>
                            <input id="store-home-hero-secondary-label" type="text" wire:model="form.store_home_hero_secondary_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.store_home_hero_secondary_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-secondary-url">{{ __('Link drugog gumba') }}</label>
                            <input id="store-home-hero-secondary-url" type="text" wire:model="form.store_home_hero_secondary_url" placeholder="/usluge ili https://..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.store_home_hero_secondary_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 p-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-desktop-video">{{ __('Desktop video') }}</label>
                            <input id="store-home-hero-desktop-video" type="file" wire:model="homeHeroDesktopVideoUpload" accept="video/mp4,video/webm" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                            <p class="mt-2 text-xs text-slate-500">{{ __('MP4 ili WebM, najviše 12 MB. Preporučen je široki 16:9 video.') }}</p>
                            <p wire:loading wire:target="homeHeroDesktopVideoUpload" class="mt-2 text-xs font-semibold text-cyan-700">{{ __('Video se učitava...') }}</p>
                            @error('homeHeroDesktopVideoUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            @if ($homeHeroDesktopVideoUrl)
                                <video controls preload="metadata" class="mt-4 aspect-video w-full rounded-xl bg-slate-950">
                                    <source src="{{ $homeHeroDesktopVideoUrl }}">
                                </video>
                            @endif
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500" for="store-home-hero-mobile-video">{{ __('Mobilni video') }}</label>
                            <input id="store-home-hero-mobile-video" type="file" wire:model="homeHeroMobileVideoUpload" accept="video/mp4,video/webm" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                            <p class="mt-2 text-xs text-slate-500">{{ __('MP4 ili WebM, najviše 12 MB. Koristi se samo na uskim ekranima.') }}</p>
                            <p wire:loading wire:target="homeHeroMobileVideoUpload" class="mt-2 text-xs font-semibold text-cyan-700">{{ __('Video se učitava...') }}</p>
                            @error('homeHeroMobileVideoUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            @if ($homeHeroMobileVideoUrl)
                                <video controls preload="metadata" class="mt-4 aspect-video w-full rounded-xl bg-slate-950">
                                    <source src="{{ $homeHeroMobileVideoUrl }}">
                                </video>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'branding')
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Store Name') }}</label>
                        <input type="text" wire:model="form.store_brand_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Footer Phone') }}</label>
                        <input type="text" wire:model="form.store_footer_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Footer Sales Email') }}</label>
                        <input type="email" wire:model="form.store_footer_email_sales" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Footer Support Email') }}</label>
                        <input type="email" wire:model="form.store_footer_email_support" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Footer Working Hours') }}</label>
                        <input type="text" wire:model="form.store_footer_hours" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2 rounded-xl border border-slate-200 p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Bottom Footer Bar') }}</p>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Copyright text') }}</label>
                                <input type="text" wire:model="form.store_footer_bottom_copyright_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Sva prava pridržana.') }}" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Bottom links (pages)') }}</label>
                                <div class="max-h-40 space-y-1 overflow-auto rounded-xl border border-slate-300 p-2 text-sm">
                                    @foreach (($pageOptions ?? []) as $option)
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="form.store_footer_bottom_link_page_ids" value="{{ (int) $option['id'] }}" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500">
                                            <span>{{ (string) $option['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-slate-500">{{ __('Order in this list = display order in footer.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Logo') }}</label>
                        <input type="file" wire:model="logoUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Allowed: JPG, PNG, WEBP, AVIF, SVG') }}</p>
                        @if ($form['store_brand_logo_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_brand_logo_path'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Favicon') }}</label>
                        <input type="file" wire:model="faviconUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Auto-generate: 16, 32, 180, 192, 512 and ICO.') }}</p>
                        @if ($form['store_brand_favicon_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_brand_favicon_path'] }}</p>
                        @endif
                    </div>
                    <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('X URL') }}</label>
                            <input type="url" wire:model="form.store_social_x_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_x_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Facebook URL') }}</label>
                            <input type="url" wire:model="form.store_social_facebook_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_facebook_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('LinkedIn URL') }}</label>
                            <input type="url" wire:model="form.store_social_linkedin_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_linkedin_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Instagram URL') }}</label>
                            <input type="url" wire:model="form.store_social_instagram_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_instagram_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('TikTok URL') }}</label>
                            <input type="url" wire:model="form.store_social_tiktok_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_tiktok_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('YouTube URL') }}</label>
                            <input type="url" wire:model="form.store_social_youtube_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_youtube_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                {{ __('Show in footer') }}
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            @if ($tab === 'blog')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Blog page settings') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Hero copy, category navigation behaviour and posts per page for the public blog listing.') }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Eyebrow') }}</label>
                        <input type="text" wire:model="form.store_blog_header_eyebrow" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('ALPHA CAPITALIS') }}" />
                        @error('form.store_blog_header_eyebrow') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero title') }}</label>
                        <input type="text" wire:model="form.store_blog_header_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Novosti i objave') }}" />
                        @error('form.store_blog_header_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero intro') }}</label>
                        <textarea wire:model="form.store_blog_header_intro" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Kratki uvod iznad blog liste i filtera.') }}"></textarea>
                        @error('form.store_blog_header_intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero CTA label') }}</label>
                        <input type="text" wire:model="form.store_blog_header_cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Kontaktirajte nas') }}" />
                        @error('form.store_blog_header_cta_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero CTA URL') }}</label>
                        <input type="text" wire:model="form.store_blog_header_cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="/contact" />
                        @error('form.store_blog_header_cta_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visible categories before "Vidi još"') }}</label>
                        <input type="number" min="1" max="40" wire:model="form.store_blog_category_preview_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Controls how many category checkboxes are shown before the expandable "Vidi još" section.') }}</p>
                        @error('form.store_blog_category_preview_limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Posts per page') }}</label>
                        <input type="number" min="1" max="48" wire:model="form.store_blog_posts_per_page" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Controls frontend pagination on /blog.') }}</p>
                        @error('form.store_blog_posts_per_page') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            @if ($tab === 'integrations')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('reCAPTCHA v3') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Protect contact and public forms from spam bots.') }}</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_captcha_recaptcha_v3_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Enable reCAPTCHA v3') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Site key') }}</label>
                        <input type="text" wire:model="form.store_captcha_recaptcha_v3_site_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Secret key') }}</label>
                        <input type="password" wire:model="form.store_captcha_recaptcha_v3_secret_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2 md:w-56">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Min score') }}</label>
                        <input type="number" step="0.1" min="0" max="1" wire:model="form.store_captcha_recaptcha_v3_min_score" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Google Analytics (GA4)') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Unesite Measurement ID iz GA4 web data streama. Google Tag Manager oznaka GTM-… nije Measurement ID.') }}</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_analytics_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Uključi GA4 praćenje') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('GA4 Measurement ID (počinje s G-)') }}</label>
                        <input type="text" wire:model="form.store_analytics_ga4_measurement_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="G-XXXXXXXXXX" autocomplete="off" spellcheck="false" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Pronađite ga u Google Analytics: Admin → Data streams → Web.') }}</p>
                        @error('form.store_analytics_ga4_measurement_id') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div></div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Google Tag Manager') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('GTM se učitava nakon analitičke ili marketinške privole i prima aktualno Google Consent Mode stanje.') }}</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_analytics_gtm_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Uključi Google Tag Manager') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('GTM Container ID') }}</label>
                        <input type="text" wire:model="form.store_analytics_gtm_container_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="GTM-XXXXXXX" autocomplete="off" spellcheck="false" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Pronađite ga u zaglavlju Google Tag Manager radnog prostora.') }}</p>
                        @error('form.store_analytics_gtm_container_id') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div></div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Google Ads') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Osnovna Google Ads oznaka učitava se samo uz marketinšku privolu.') }}</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_analytics_google_ads_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Uključi Google Ads praćenje') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Google Ads Conversion ID') }}</label>
                        <input type="text" wire:model="form.store_analytics_google_ads_conversion_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="AW-123456789" autocomplete="off" spellcheck="false" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Unesite samo Conversion ID; oznake pojedinih konverzija podešavaju se zasebno.') }}</p>
                        @error('form.store_analytics_google_ads_conversion_id') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div></div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Meta (Facebook) Pixel') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Meta Pixel i početni PageView događaj učitavaju se samo uz marketinšku privolu.') }}</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_analytics_meta_pixel_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Uključi Meta Pixel') }}
                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Pixel ID') }}</label>
                        <input type="text" inputmode="numeric" wire:model="form.store_analytics_meta_pixel_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="123456789012345" autocomplete="off" spellcheck="false" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Pronađite ga u Meta Events Manageru pod Data sources.') }}</p>
                        @error('form.store_analytics_meta_pixel_id') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div></div>

                    <div class="md:col-span-2 rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
                        {{ __('Ako iste GA4, Google Ads ili Meta oznake postavite i unutar GTM-a i u ovim izravnim postavkama, događaji se mogu zabilježiti dvaput.') }}
                    </div>
                </div>
            @endif

            @if ($tab === 'seo')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Default Title') }}</label>
                        <input type="text" wire:model="form.store_seo_default_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Default Meta Description') }}</label>
                        <textarea wire:model="form.store_seo_default_description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Robots') }}</label>
                        <input type="text" wire:model="form.store_seo_robots" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="index,follow" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Canonical policy') }}</label>
                        <select wire:model="form.store_seo_canonical_policy" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="self">{{ __('Self URL') }}</option>
                            <option value="none">{{ __('Disabled') }}</option>
                        </select>
                    </div>
                </div>
            @endif

            @if ($tab === 'og')
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Default OG image') }}</label>
                        <input type="file" wire:model="ogDefaultImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @if ($form['store_og_default_image_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_og_default_image_path'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Home OG override') }}</label>
                        <input type="file" wire:model="ogHomeImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @if ($form['store_og_home_image_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_og_home_image_path'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Category OG override') }}</label>
                        <input type="file" wire:model="ogCategoryImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @if ($form['store_og_category_image_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_og_category_image_path'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Page OG override') }}</label>
                        <input type="file" wire:model="ogPageImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @if ($form['store_og_page_image_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_og_page_image_path'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog OG override') }}</label>
                        <input type="file" wire:model="ogBlogImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @if ($form['store_og_blog_image_path'])
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current:') }} {{ $form['store_og_blog_image_path'] }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if ($tab === 'schema')
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_schema_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Enable schema markup JSON-LD') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_org_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Organization schema') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_website_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('WebSite + SearchAction') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_breadcrumbs_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('BreadcrumbList') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_itemlist_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('ItemList (blog list)') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_home_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Home WebPage') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_blog_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Blog / BlogPosting') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_page_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('Generic Page schema') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_faq_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        {{ __('FAQ schema (home)') }}
                    </label>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Organization type') }}</label>
                        <select wire:model="form.store_schema_org_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="Organization">{{ __('Organization') }}</option>
                            <option value="LocalBusiness">{{ __('LocalBusiness') }}</option>
                            <option value="Store">{{ __('Store') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Business Name') }}</label>
                        <input type="text" wire:model="form.store_schema_business_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Business Phone') }}</label>
                        <input type="text" wire:model="form.store_schema_business_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Business Email') }}</label>
                        <input type="email" wire:model="form.store_schema_business_email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Street') }}</label>
                            <input type="text" wire:model="form.store_schema_address_street" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('City') }}</label>
                            <input type="text" wire:model="form.store_schema_address_city" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Region') }}</label>
                            <input type="text" wire:model="form.store_schema_address_region" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Postal code') }}</label>
                            <input type="text" wire:model="form.store_schema_address_postal_code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Country code') }}</label>
                            <input type="text" wire:model="form.store_schema_address_country" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="HR" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('SameAs URLs (one per line)') }}</label>
                        <textarea wire:model="form.store_schema_same_as" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog author name (default)') }}</label>
                        <input type="text" wire:model="form.store_schema_blog_author_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog author profile URL') }}</label>
                        <input type="url" wire:model="form.store_schema_blog_author_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FAQ group code (optional)') }}</label>
                        <input type="text" wire:model="form.store_schema_faq_group" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="support" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FAQ max items') }}</label>
                        <input type="number" min="1" max="20" wire:model="form.store_schema_faq_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('ItemList max posts') }}</label>
                        <input type="number" min="1" max="48" wire:model="form.store_schema_itemlist_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:w-48" />
                    </div>
                </div>
            @endif

            <div class="pt-2">
                <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>
</div>
