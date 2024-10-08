<?php

namespace Modules\Livewire\Admin\Job\Client;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use Modules\Clean\Traits\InteractsWithBanner;
use Modules\Job\Actions\Client\DeleteClient;
use Modules\Job\Models\Client;

class Delete extends Component
{
    use InteractsWithBanner, AuthorizesRequests;

    /**
     * @var string
     */
    public string $modalId;


    /**
     * @var bool
     */
    public bool $isModalOpen;


    /**
     * @var int
     */
    public int $clientId;


    /**
     * @var Client
     */
    private Client $client;


    /**
     * @var string
     */
    public string $name;


    /**
     * @var array|string[]
     */
    protected array $rules = [
        'clientId' => 'required|int|min:1',
    ];


    /**
     * @param  string  $modalId
     * @param  Client  $client
     * @return void
     */
    public function mount(string $modalId, Client $client): void
    {
        $this->modalId = $modalId;
        $this->isModalOpen = false;
        $this->client = $client;
        $this->clientId = intval($this->client->id);
        $this->name = strip_tags($client->name);
    }


    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function render(): Factory|View|\Illuminate\Foundation\Application|Application
    {
        return view('admin.livewire.job.client.delete');
    }


    /**
     * @param  DeleteClient  $deleteClient
     * @return Redirector
     * @throws AuthorizationException
     */
    public function deleteClient(DeleteClient $deleteClient): Redirector
    {
        $this->client = Client::findOrFail($this->clientId);

        $this->authorize('delete', [Client::class, $this->client]);

        // validate user input
        $this->validate();

        // save category, rollback transaction if fails
        DB::transaction(
            function () use ($deleteClient) {
                $deleteClient($this->client);
            }
        );

        $this->banner(__('The client with the name ":name" was successfully deleted.',
            ['name' => strip_tags($this->name)]));
        return redirect()->route('client.manage');
    }
}
