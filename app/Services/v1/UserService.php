<?php

namespace App\Services\v1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param User|null $user
     */
    public function __construct(private ?User $user = null)
    {
    }

    /**
     * @param User|null $user
     * @return UserService
     */
    public function setUser(?User $user = null): static
    {
        $this->user = $user ?? new User();
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function assignData(array $data): static
    {
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $value = Hash::make($value);
            }
            $this->user->$key = $value;
        }

        $this->user->save();
        return $this;
    }

}
