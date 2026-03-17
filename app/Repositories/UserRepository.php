<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = User::find($id);
        if ($user) {
           $user->update($data);
            return $user;
        }
        return null;
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function getStaffs()
    {
        return User::where('role','warehouse_staff')->get();
    }

}