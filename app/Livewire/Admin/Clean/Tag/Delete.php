<?php

namespace App\Livewire\Admin\Clean\Tag;

use App\Livewire\Admin\Clean\Tag\Trait\Reactive;
use App\Trait\Clean\InteractsWithBanner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Clean\Interfaces\Repositories\ModelRepositoryInterface;
use Modules\Clean\Models\Tag;

class Delete extends Component
{
    use InteractsWithBanner;
    use AuthorizesRequests;
    use Reactive;


    // used by blade / alpinejs
    /**
     * @var string
     */
    public string $modalId;


    /**
     * @var bool
     */
    public bool $isModalOpen;


    /**
     * @var bool
     */
    public bool $hasSmallButton;


    // inputs
    /**
     * @var int
     */
    public int $tagId;


    /**
     * @var string
     */
    public string $name;


    /**
     * @var Tag
     */
    public Tag $tag;


    /**
     * @var array|string[]
     */
    protected array $rules = [
        'tagId' => 'required|int|min:1',
    ];


    /**
     * @var ModelRepositoryInterface
     */
    private ModelRepositoryInterface $tagRepository;


    /**
     * @param  ModelRepositoryInterface  $tagRepository
     */
    public function boot(ModelRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }


    /**
     * @param  string  $modalId
     * @param  Tag  $tag
     * @param  bool  $hasSmallButton
     * @return void
     */
    public function mount(string $modalId, Tag $tag, bool $hasSmallButton = false)
    {
        $this->modalId = $modalId;
        $this->isModalOpen = false;
        $this->hasSmallButton = $hasSmallButton;
        $this->tag = $tag;
        $this->tagId = $tag->id;
        $this->name = $tag->name;
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        return view('admin.livewire.clean.tag.delete');
    }


    /**
     * Soft-deletes (archives) one tag
     * Maybe rename the method to archiveTag?
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteTag(): void
    {
        $this->authorize('delete', [Tag::class, $this->tag]);

        // validate user input
        $this->validate();

        // save category, rollback transaction if fails
        DB::transaction(
            function () {
                $this->tagRepository->deleteEntity($this->tag);
            },
            2
        );

        $this->banner(__('Tag successfully archived'));
        $this->closeModal();
        $this->rerenderList();
        $this->triggerOnAlert();

        // Notify archive to receive new archived tag(s)
        $this->dispatch('archivedTagsAdded');

    }
}
