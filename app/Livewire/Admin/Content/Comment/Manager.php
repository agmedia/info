<?php

namespace App\Livewire\Admin\Content\Comment;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Support\Comment;
use App\Models\Content\Support\Faq;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $locale = 'all';
    public string $status = Comment::STATUS_APPROVED;
    public string $target = 'detached';
    public bool $showCreateForm = false;
    public ?int $editingCommentId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [
        'locale' => 'en',
        'author_name' => '',
        'company' => '',
        'body' => '',
        'rating' => 5,
        'is_featured' => true,
    ];

    public function mount(): void
    {
        $selectedLocale = $this->adminLocale();

        $this->locale = (string) (request()->query('locale') ?: 'all');
        $this->form['locale'] = $selectedLocale;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocale(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedTarget(): void
    {
        $this->resetPage();
    }

    public function createComment(): void
    {
        $this->showCreateForm = true;

        $validated = $this->validate($this->creationRules());
        $payload = $validated['form'];
        $company = trim((string) $payload['company']);
        $isEditing = $this->editingCommentId !== null;
        $status = Comment::STATUS_APPROVED;

        if ($isEditing) {
            $comment = Comment::query()->find($this->editingCommentId);

            if (! $comment) {
                $this->dispatch('notify', type: 'warning', message: __('Comment not found.'));

                return;
            }

            $comment->update([
                'author_name' => trim((string) $payload['author_name']),
                'author_email' => null,
                'locale' => strtolower(trim((string) ($payload['locale'] ?? $this->adminLocale()))),
                'body' => trim((string) $payload['body']),
                'rating' => (int) $payload['rating'],
                'is_featured' => (bool) ($payload['is_featured'] ?? false),
                'payload' => $company !== '' ? ['company' => $company] : null,
            ]);
        } else {
            $reviewedBy = auth()->id();
            $reviewedAt = now();

            $comment = Comment::query()->create([
                'commentable_type' => null,
                'commentable_id' => null,
                'author_name' => trim((string) $payload['author_name']),
                'author_email' => null,
                'locale' => strtolower(trim((string) ($payload['locale'] ?? $this->adminLocale()))),
                'body' => trim((string) $payload['body']),
                'rating' => (int) $payload['rating'],
                'status' => $status,
                'is_featured' => (bool) ($payload['is_featured'] ?? false),
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => $reviewedAt,
                'payload' => $company !== '' ? ['company' => $company] : null,
            ]);
        }

        activity('content_comments')
            ->performedOn($comment)
            ->causedBy(auth()->user())
            ->event($isEditing ? 'updated' : 'created')
            ->withProperties([
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id,
                'locale' => $comment->locale,
                'status' => $comment->status,
                'is_featured' => $comment->is_featured,
            ])
            ->log($isEditing ? __('Comment updated') : __('Comment created'));

        $this->search = '';
        $this->locale = (string) ($payload['locale'] ?? $this->adminLocale());
        $this->status = $comment->status;
        $this->target = 'detached';
        $this->showCreateForm = false;
        $this->resetForm();
        $this->resetPage();

        $this->dispatch('notify', type: 'success', message: $isEditing ? __('Comment updated.') : __('Comment created.'));
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;

        if (! $this->showCreateForm) {
            $this->resetForm();
        }
    }

    public function edit(int $commentId): void
    {
        $comment = Comment::query()->find($commentId);

        if (! $comment) {
            $this->dispatch('notify', type: 'warning', message: __('Comment not found.'));

            return;
        }

        $this->editingCommentId = $comment->id;
        $this->showCreateForm = true;
        $this->form = [
            'locale' => strtolower((string) ($comment->locale ?: $this->adminLocale())),
            'author_name' => (string) $comment->author_name,
            'company' => trim((string) ($comment->payload['company'] ?? '')),
            'body' => (string) $comment->body,
            'rating' => (int) ($comment->rating ?: 5),
            'is_featured' => (bool) $comment->is_featured,
        ];
    }

    public function approve(int $commentId): void
    {
        $this->setStatus($commentId, Comment::STATUS_APPROVED);
    }

    public function reject(int $commentId): void
    {
        $this->setStatus($commentId, Comment::STATUS_REJECTED);
    }

    public function spam(int $commentId): void
    {
        $this->setStatus($commentId, Comment::STATUS_SPAM);
    }

    public function delete(int $commentId): void
    {
        $comment = Comment::query()->find($commentId);
        if (! $comment) {
            $this->dispatch('notify', type: 'warning', message: __('Comment not found.'));
            return;
        }

        $comment->delete();

        activity('content_comments')
            ->performedOn($comment)
            ->causedBy(auth()->user())
            ->event('deleted')
            ->log(__('Comment moved to trash'));

        $this->dispatch('notify', type: 'info', message: __('Comment moved to trash.'));
    }

    public function restore(int $commentId): void
    {
        $comment = Comment::query()->withTrashed()->find($commentId);
        if (! $comment || ! $comment->trashed()) {
            $this->dispatch('notify', type: 'warning', message: __('Comment not in trash.'));
            return;
        }

        $comment->restore();

        activity('content_comments')
            ->performedOn($comment)
            ->causedBy(auth()->user())
            ->event('restored')
            ->log(__('Comment restored'));

        $this->dispatch('notify', type: 'success', message: __('Comment restored.'));
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );
        $displayLocale = $this->locale !== 'all'
            ? $this->locale
            : $this->adminLocale();

        $query = Comment::query()
            ->with([
                'user:id,name,email',
                'reviewer:id,name',
                'commentable' => function (MorphTo $morphTo): void {
                    $locale = $this->locale !== 'all'
                        ? $this->locale
                        : $this->adminLocale();
                    $localeOptions = array_values(array_unique([$locale, strtolower((string) config('app.locale', 'en'))]));

                    $morphTo->morphWith([
                        BlogPost::class => ['translations' => fn ($q) => $q->whereIn('locale', $localeOptions)],
                        InfoPage::class => ['translations' => fn ($q) => $q->whereIn('locale', $localeOptions)],
                        Faq::class => ['translations' => fn ($q) => $q->whereIn('locale', $localeOptions)],
                    ]);
                },
            ]);

        if ($this->status === 'deleted') {
            $query->onlyTrashed();
        } else {
            if ($this->status === 'all') {
                $query->withTrashed();
            } else {
                $query->where('status', $this->status);
            }
        }

        if ($this->target !== 'all') {
            if ($this->target === 'detached') {
                $query->whereNull('commentable_type');
            } else {
                $targetClass = $this->targetClass($this->target);
            }

            if (isset($targetClass) && $targetClass) {
                $query->where('commentable_type', $targetClass);
            }
        }

        if ($this->locale !== 'all') {
            $query->where('locale', $this->locale);
        }

        if ($this->search !== '') {
            $query->where(function (Builder $q): void {
                $q->where('body', 'like', '%'.$this->search.'%')
                    ->orWhere('author_name', 'like', '%'.$this->search.'%')
                    ->orWhere('payload', 'like', '%'.$this->search.'%');
            });
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        $rows->getCollection()->transform(function (Comment $comment) use ($displayLocale): Comment {
            $comment->setAttribute('target_label', $this->resolveCommentTargetLabel($comment, $displayLocale));
            $comment->setAttribute('company_label', trim((string) ($comment->payload['company'] ?? '')));

            return $comment;
        });

        return view('livewire.admin.content.comment.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'statusOptions' => $this->statusOptions(),
            'targetOptions' => $this->targetOptions(),
        ]);
    }

    private function setStatus(int $commentId, string $status): void
    {
        if (! in_array($status, Comment::statuses(), true)) {
            return;
        }

        $comment = Comment::query()->find($commentId);
        if (! $comment) {
            $this->dispatch('notify', type: 'warning', message: __('Comment not found.'));
            return;
        }

        $comment->update([
            'status' => $status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        activity('content_comments')
            ->performedOn($comment)
            ->causedBy(auth()->user())
            ->event('moderated')
            ->withProperties(['status' => $status])
            ->log(__('Comment status changed'));

        $this->dispatch('notify', type: 'success', message: __('Comment status updated.'));
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return $this->commentStatusLabels() + [
            'all' => __('All'),
            'deleted' => __('Trash'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function commentStatusLabels(): array
    {
        return [
            Comment::STATUS_PENDING => __('Pending'),
            Comment::STATUS_APPROVED => __('Approved'),
            Comment::STATUS_REJECTED => __('Rejected'),
            Comment::STATUS_SPAM => __('Spam'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function targetOptions(): array
    {
        return [
            'all' => __('All Targets'),
            'detached' => __('Homepage Testimonials'),
            'blog' => __('Blog Posts'),
            'page' => __('Info Pages'),
            'faq' => __('FAQs'),
        ];
    }

    private function targetClass(string $key): ?string
    {
        return match ($key) {
            'blog' => BlogPost::class,
            'page' => InfoPage::class,
            'faq' => Faq::class,
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function creationRules(): array
    {
        return [
            'form.locale' => ['required', 'string', 'max:12'],
            'form.author_name' => ['required', 'string', 'max:120'],
            'form.company' => ['required', 'string', 'max:160'],
            'form.body' => ['required', 'string', 'max:5000'],
            'form.rating' => ['required', 'integer', 'min:1', 'max:5'],
            'form.is_featured' => ['boolean'],
        ];
    }

    private function resolveCommentTargetLabel(Comment $comment, string $locale): string
    {
        if ($comment->commentable instanceof Model) {
            return $this->describeTargetModel($comment->commentable, $locale);
        }

        $commentableType = (string) ($comment->commentable_type ?? '');
        $commentableId = (int) ($comment->commentable_id ?? 0);

        if ($commentableType !== '' && $commentableId > 0) {
            return class_basename($commentableType).' (#'.$commentableId.')';
        }

        return __('Homepage testimonial');
    }

    private function describeTargetModel(Model $model, string $locale, bool $withPrefix = true): string
    {
        $prefix = __('Item');
        $title = null;

        if ($model instanceof BlogPost) {
            $prefix = __('Blog');
            $title = $model->translations->firstWhere('locale', $locale)?->title
                ?? $model->translations->first()?->title
                ?? $model->code;
        } elseif ($model instanceof InfoPage) {
            $prefix = __('Page');
            $title = $model->translations->firstWhere('locale', $locale)?->title
                ?? $model->translations->first()?->title
                ?? $model->code;
        } elseif ($model instanceof Faq) {
            $prefix = __('FAQ');
            $title = $model->translations->firstWhere('locale', $locale)?->question
                ?? $model->translations->first()?->question
                ?? $model->code;
        }

        $parts = ['#'.$model->getKey()];
        $code = trim((string) ($model->getAttribute('code') ?? ''));

        if ($code !== '') {
            $parts[] = $code;
        }

        if (array_key_exists('is_active', $model->getAttributes()) && ! (bool) $model->getAttribute('is_active')) {
            $parts[] = __('inactive');
        }

        $label = $title ?: __('Untitled');

        if ($withPrefix) {
            $label = $prefix.': '.$label;
        }

        return $label.' ('.implode(' · ', $parts).')';
    }

    private function resetForm(): void
    {
        $locale = strtolower(trim((string) ($this->locale !== 'all' ? $this->locale : $this->form['locale'])));

        if ($locale === '') {
            $locale = $this->adminLocale();
        }

        $this->editingCommentId = null;
        $this->form = [
            'locale' => $locale,
            'author_name' => '',
            'company' => '',
            'body' => '',
            'rating' => 5,
            'is_featured' => true,
        ];
    }

    private function adminLocale(): string
    {
        return strtolower((string) app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
    }
}
