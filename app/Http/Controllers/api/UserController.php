<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * v0.1.4
     * @param Request $request
     * @return Response
     */
    public function users(Request $request): Response
    {
        $users_query = User::query();

        $validated = $request->validate([
            'search' => ['nullable', 'string'],
            'roles' => ['nullable', 'array'],
        ]);

        if ($request->has('search')) {
            $search = $request->search;
            $users_query = $users_query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('roles')) {
            $users_query = $users_query->whereHas('roles', function ($q) use ($request) {
                $q->whereIn('name', $request->roles);
            });
        }

        $users = $users_query->orderBy('email')->get();

        return response([
            'users' => UserResource::collection($users),
            'total_count' => User::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(User::rules());
        $validated['password'] = bcrypt(Str::random()); // zufälliges Passwort setzen

        $user = User::create($validated);
        $user->syncRoles($validated['role']);

        return response([
            'user' => UserResource::make($user)
        ]);
    }
}
