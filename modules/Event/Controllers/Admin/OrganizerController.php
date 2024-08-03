<?php

namespace Modules\Event\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Auth\Traits\UserPermissions;
use Modules\Clean\Interfaces\ModelServiceInterface;
use Modules\Event\Models\Organizer;

class OrganizerController extends Controller
{
    use UserPermissions;

    /**
     * @var ModelServiceInterface
     */
    private ModelServiceInterface $modelRepository;


    /**
     * @param  ModelServiceInterface  $modelRepository
     */
    public function __construct(ModelServiceInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }


    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Organizer::class);

        $organizers = $this->modelRepository->paginateEntities('Event\Models\Organizer', Organizer::RECORDS_PER_PAGE);

        return view('event::admin.organizer.manage')->with([
            'organizers' => $organizers,
            'userPermissions' => $this->getUserPermissions()
        ]);
    }
}
