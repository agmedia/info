<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Team\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        $members = TeamMember::query()
            ->where('is_active', true)
            ->with(['translations', 'media'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (TeamMember $member): array => $this->mapMember($member, $locale, $fallbackLocale))
            ->filter(fn (array $member): bool => $member['name'] !== '')
            ->values();

        return view($this->frontendView($request, 'team.index'), [
            'members' => $members,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapMember(TeamMember $member, string $locale, string $fallbackLocale): array
    {
        $translation = $member->translations->firstWhere('locale', $locale)
            ?? $member->translations->firstWhere('locale', $fallbackLocale)
            ?? $member->translations->first();

        $name = trim((string) ($translation?->name ?? ''));
        $position = trim((string) ($translation?->position ?? ''));
        $departments = collect(preg_split('/[\r\n,]+/u', (string) ($translation?->departments ?? '')) ?: [])
            ->map(static fn (string $department): string => trim($department))
            ->filter()
            ->values()
            ->all();

        $photoUrl = (string) ($member->getFirstMediaUrl('team_photo')
            ?: $member->getFirstMediaUrl('team_photo', 'detail_960x960'));
        $descriptionHtml = (string) ($translation?->description_html ?? '');
        $descriptionText = trim((string) preg_replace('/\s+/u', ' ', strip_tags($descriptionHtml)));
        $descriptionExcerpt = (string) Str::limit($descriptionText, 320, '...');

        $initials = $this->initialsForName($name !== '' ? $name : (string) $member->code);

        return [
            'id' => (int) $member->id,
            'name' => $name,
            'position' => $position,
            'departments' => $departments,
            'description_html' => $descriptionHtml,
            'description_excerpt' => $descriptionExcerpt,
            'has_long_description' => $descriptionText !== '' && $descriptionExcerpt !== $descriptionText,
            'email' => trim((string) ($member->email ?? '')),
            'mobile_phone' => trim((string) ($member->mobile_phone ?? '')),
            'facebook_url' => trim((string) ($member->facebook_url ?? '')),
            'twitter_url' => trim((string) ($member->twitter_url ?? '')),
            'linkedin_url' => trim((string) ($member->linkedin_url ?? '')),
            'photo_url' => $photoUrl,
            'initials' => $initials,
        ];
    }

    private function initialsForName(string $value): string
    {
        $parts = collect(preg_split('/\s+/u', trim($value)) ?: [])
            ->filter()
            ->take(2)
            ->map(static fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $parts !== '' ? $parts : 'AC';
    }
}
