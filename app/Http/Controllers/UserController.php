<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = (new User())->newQuery();
        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $users->orderBy($attribute, $sort_order);
        } else {
            $users->latest();
        }

        $users = $users->paginate(10)->onEachSide(2);

        return Inertia::render('Users/List', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'establishments' => LocalSale::all()
        ]);
    }
}
