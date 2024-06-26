<?php

namespace App\Livewire\Admin\Clean\Tag;

use App\Livewire\Admin\Clean\Tag\Trait\Reactive;
use App\Trait\Clean\InteractsWithBanner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Auth\Models\User;
use Modules\Clean\Interfaces\Entities\TagInterface;
use Modules\Clean\Interfaces\Repositories\ModelRepositoryInterface;
use Modules\Clean\Models\Tag;

class Index extends Component
{
    use InteractsWithBanner;
    use AuthorizesRequests;
    use WithPagination;
    use Reactive;


    /**
     * @var ModelRepositoryInterface
     */
    private ModelRepositoryInterface $tagRepository;


    /**
     * @var
     */
    protected $tags;


    /**
     * @var
     */
    protected $archivedTags = null;


    /**
     * Custom pagination pageName parameter
     * @var string
     */
    public string $pageName = 'page';


    /**
     * @var User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected User|\Illuminate\Contracts\Auth\Authenticatable|null $user;


    /**
     * @var string
     */
    public string $filterKeyword = '';


    /**
     * @var bool
     */
    private bool $filterOn = false;


    /**
     * @var string[]
     */
    protected $listeners = [
        'listUpdated',
        'restoredTagsAdded',
    ];


    /**
     * @var array
     */
    public array $selectedIds = [];


    /**
     * State of the archive tags modal
     * @var bool
     */
    public bool $isArchiveConfirmOpen = false;


    /**
     * @param  ModelRepositoryInterface  $tagRepository
     *
     * @return void
     */
    public function boot(ModelRepositoryInterface $tagRepository): void
    {
        $this->tagRepository = $tagRepository;
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function mount(): void
    {
        $this->tags         = null;
        $this->archivedTags = null;
        $this->selectedIds  = [];
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        if ($this->filterOn !== true) {
            $this->tags = $this->tagRepository->paginateEntities('Clean\Tag', TagInterface::RECORDS_PER_PAGE, 'page');
        }

        return view('admin.livewire.clean.tag.index')->with([
            'tags' => $this->tags,
        ]);
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
//        $this->tags = $this->tagRepository->paginateEntities('Clean\Tag', TagInterface::RECORDS_PER_PAGE, 'page');
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function listUpdated(): void
    {
        $this->resetFilters();
        $this->dispatch('$refresh')->self();
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function restoredTagsAdded(): void
    {
        $this->initialize();
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->filterOn      = false;
        $this->filterKeyword = '';
        $this->selectedIds  = [];
        $this->initialize();
    }


    /**
     * @throws \Exception
     */
    public function filterTags(): void
    {
        $this->resetPage();

        $this->tags        = $this->getFilteredPaginatedTags($this->filterKeyword);
        $this->selectedIds = [];
        $this->filterOn    = true;
    }


    /**
     * @param  string  $keyword
     *
     * @return LengthAwarePaginator
     */
    public function getFilteredPaginatedTags(string $keyword): LengthAwarePaginator
    {
        /* has the search keyword in name */
        if ($keyword !== '') {
            $q = Tag::where('name', 'LIKE', '%'.$keyword.'%');
        } else {
            return Tag::paginate(TagInterface::RECORDS_PER_PAGE);
        }

        return $q->orderBy('id', 'desc')
                 ->paginate(TagInterface::RECORDS_PER_PAGE);
    }


    /**
     * Archive selected tags
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function archiveTags(): void
    {
        $count = sizeof($this->selectedIds);
        $ids   = $this->selectedIds;
        $tag   = Tag::first();
        $this->authorize('restore', [Tag::class, $tag]);

        $this->tagRepository->deleteSelectedEntities('Clean\Tag', $ids);
        $this->toggleArchiveModal();
        $this->banner(__($count.' tags archived.'));
        $this->initialize();
        $this->triggerOnAlert();

        // Notify archive to receive new archived tag(s)
        $this->dispatch('archivedTagsAdded');
        $this->selectedIds = [];
    }


    /**
     * Open/close archive tags modal
     *
     * @return void
     */
    public function toggleArchiveModal(): void
    {
        $this->isArchiveConfirmOpen = ! $this->isArchiveConfirmOpen;
    }

}
