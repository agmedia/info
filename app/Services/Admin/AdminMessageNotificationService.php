<?php

namespace App\Services\Admin;

use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Models\Content\Support\CareerApplication;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;

final class AdminMessageNotificationService
{
    /**
     * @return array{
     *     total: int,
     *     groups: array<int, array{key: string, label: string, url: string, icon: string, count: int}>
     * }
     */
    public function summaryFor(User $user): array
    {
        $isSuperadmin = $user->isA('superadmin');
        $canViewContact = $isSuperadmin || $user->can('messages.contact.view');
        $canViewCollaborationAssessment = $isSuperadmin || $user->can('messages.collaboration_assessment.view');
        $canViewCareer = $isSuperadmin || $user->can('messages.career.view');
        $canViewDownloadRequests = $isSuperadmin || $user->can('messages.download_requests.view');
        $canViewEuFundsQuestionnaire = $isSuperadmin || $user->can('messages.eu_funds_questionnaire.view');

        $contactFormTypes = [];

        if ($canViewContact) {
            $contactFormTypes[] = ContactMessage::FORM_TYPE_CONTACT;
            $contactFormTypes[] = ContactMessage::FORM_TYPE_SERVICE_CONTACT;
        }

        if ($canViewCollaborationAssessment) {
            $contactFormTypes[] = ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT;
        }

        if ($canViewEuFundsQuestionnaire) {
            $contactFormTypes[] = ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE;
        }

        $contactCounts = collect();

        if ($contactFormTypes !== []) {
            $contactCounts = ContactMessage::query()
                ->selectRaw('form_type, COUNT(*) as aggregate')
                ->where('status', ContactMessage::STATUS_NEW)
                ->whereIn('form_type', $contactFormTypes)
                ->groupBy('form_type')
                ->pluck('aggregate', 'form_type');
        }

        $groups = [];

        if ($canViewContact) {
            $groups[] = $this->group(
                key: 'contact',
                label: (string) __('admin.layout.menu.contact'),
                routeName: 'admin.messages.contact.index',
                icon: 'fa-light fa-envelope',
                count: (int) $contactCounts->get(ContactMessage::FORM_TYPE_CONTACT, 0)
                    + (int) $contactCounts->get(ContactMessage::FORM_TYPE_SERVICE_CONTACT, 0),
            );
        }

        if ($canViewCollaborationAssessment) {
            $groups[] = $this->group(
                key: 'collaboration-assessment',
                label: (string) __('admin.layout.menu.collaboration_assessment'),
                routeName: 'admin.messages.collaboration-assessment.index',
                icon: 'fa-light fa-clipboard-check',
                count: (int) $contactCounts->get(ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT, 0),
            );
        }

        if ($canViewCareer) {
            $groups[] = $this->group(
                key: 'career',
                label: (string) __('admin.layout.menu.career_cv_form'),
                routeName: 'admin.messages.career.index',
                icon: 'fa-light fa-file-user',
                count: CareerApplication::query()
                    ->where('status', CareerApplication::STATUS_NEW)
                    ->count(),
            );
        }

        if ($canViewDownloadRequests) {
            $groups[] = $this->group(
                key: 'download-requests',
                label: (string) __('admin.layout.menu.download_requests'),
                routeName: 'admin.messages.download-requests.index',
                icon: 'fa-light fa-file-arrow-down',
                count: ResourceDownloadRequest::query()
                    ->where('status', ResourceDownloadRequest::STATUS_NEW)
                    ->count(),
            );
        }

        if ($canViewEuFundsQuestionnaire) {
            $groups[] = $this->group(
                key: 'eu-funds-questionnaire',
                label: (string) __('admin.layout.menu.eu_funds_questionnaire'),
                routeName: 'admin.messages.eu-funds-questionnaire.index',
                icon: 'fa-light fa-rectangle-list',
                count: (int) $contactCounts->get(ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE, 0),
            );
        }

        return [
            'total' => array_sum(array_column($groups, 'count')),
            'groups' => $groups,
        ];
    }

    /**
     * @return array{key: string, label: string, url: string, icon: string, count: int}
     */
    private function group(
        string $key,
        string $label,
        string $routeName,
        string $icon,
        int $count,
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'url' => route($routeName),
            'icon' => $icon,
            'count' => $count,
        ];
    }
}
