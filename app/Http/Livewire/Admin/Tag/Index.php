<?php

namespace App\Http\Livewire\Admin\Tag;

use App\Http\Livewire\Admin\Tag\Trait\Reactive;
use App\Interface\Repository\ModelRepositoryInterface;
use App\Interface\Entities\TagInterface;
use App\Models\Tag;
use App\Models\User;
use App\Support\InteractsWithBanner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

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
     * @param ModelRepositoryInterface $tagRepository
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
        //$this->initialize();
        $this->tags = null;
        $this->archivedTags = null;
        $this->selectedIds = [];
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        if ($this->filterOn !== true) {
            $this->tags = $this->tagRepository->paginateEntities('Tag', TagInterface::RECORDS_PER_PAGE, 'page');
        }

        return view('admin.livewire.tag.index')->with([
            'tags' => $this->tags,
        ]);
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        $this->tags = $this->tagRepository->paginateEntities('Tag', TagInterface::RECORDS_PER_PAGE, 'page');
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function listUpdated(): void
    {
        $this->resetFilters();
        $this->emitSelf('$refresh');
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
        $this->resetPage('page');
        $this->filterOn = false;
        $this->filterKeyword = '';
        $this->initialize();
    }


    /**
     * @throws \Exception
     */
    public function filterTags(): void
    {
        $this->resetPage('page');

        $this->tags = $this->getFilteredPaginatedTags($this->filterKeyword);
        $this->selectedIds = [];
        $this->filterOn = true;
    }


    /**
     * @param string $keyword
     * @return LengthAwarePaginator
     */
    public function getFilteredPaginatedTags(string $keyword): LengthAwarePaginator
    {
        /* has the search keyword in name */
        if ($keyword !== '') {
            $q = Tag::where('name', 'LIKE', '%' . $keyword . '%');
        } else {
            $q = Tag::all();
        }

        return $q->orderBy('id', 'desc')
            ->paginate(TagInterface::RECORDS_PER_PAGE);
    }


    /**
     * Archive selected tags
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archiveTags(): void
    {
        $count = sizeof($this->selectedIds);
        $ids = $this->selectedIds;
        $tag = Tag::first();
        $this->authorize('restore', [Tag::class, $tag]);

        $this->tagRepository->deleteSelectedEntities('Tag', $ids);
        $this->toggleArchiveModal();
        $this->banner(__($count . ' tags archived.'));
        $this->initialize();
        $this->triggerOnAlert();

        // Notify archive to receive new archived tag(s)
        $this->emit('archivedTagsAdded');
        $this->selectedIds = [];
    }


    /**
     * Open/close archive tags modal
     *
     * @return void
     */
    public function toggleArchiveModal(): void
    {
        $this->isArchiveConfirmOpen = !$this->isArchiveConfirmOpen;
    }

}