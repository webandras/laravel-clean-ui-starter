<?php

namespace Modules\Event\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Auth\Traits\UserPermissions;
use Modules\Clean\Interfaces\Repositories\ModelRepositoryInterface;
use Modules\Event\Models\Location;

class LocationController extends Controller
{
    use UserPermissions;

    /**
     * @var ModelRepositoryInterface
     */
    private ModelRepositoryInterface $modelRepository;


    /**
     * @param  ModelRepositoryInterface  $modelRepository
     */
    public function __construct(ModelRepositoryInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }


    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Location::class);

        $locations = $this->modelRepository->paginateEntities('Event\Models\Location',
            Location::RECORDS_PER_PAGE);

        return view('event::admin.location.manage')->with([
            'locations' => $locations,
            'userPermissions' => $this->getUserPermissions()
        ]);
    }
}
