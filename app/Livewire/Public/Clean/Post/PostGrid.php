<?php

namespace App\Livewire\Public\Clean\Post;

use App\Trait\Clean\HasLocalization;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Clean\Interfaces\Repositories\PostRepositoryInterface;

class PostGrid extends Component
{
    use WithPagination, HasLocalization;


    /**
     * Event list collection
     * @var LengthAwarePaginator|null
     */
    protected ?LengthAwarePaginator $posts;


    /**
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;


    /**
     * @param  PostRepositoryInterface  $postRepository
     */
    public function boot(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        $this->posts = $this->postRepository->getPaginatedPublishedPosts();

        $this->resetPage();

        return view('public.livewire.post.post-grid')->with([
            'posts'    => $this->posts,
            'dtFormat' => $this->getLocaleDateTimeFormat(),
        ]);
    }
}
