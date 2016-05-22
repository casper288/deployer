<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Contracts\Repositories\UserRepositoryInterface;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreUserRequest;

/**
 * User management controller.
 */
class UserController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  UserRepositoryInterface $repository
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the users.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.users.listing', [
            'title' => Lang::get('users.manage'),
            'users' => $this->repository->getAll()->toJson(), // PresentableInterface toJson() is not working in view
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  StoreUserRequest $request
     * @return Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->repository->create($request->only(
            'name',
            'email',
            'password'
        ));

        event(new UserWasCreated($user, $request->get('password')));

        return $user;
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int              $user_id
     * @param  StoreUserRequest $request
     * @return Response
     */
    public function update($user_id, StoreUserRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'email',
            'password'
        ), $user_id);
    }
}
