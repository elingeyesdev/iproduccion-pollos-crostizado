<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'estado'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasRoles,HasApiTokens;

    public function hasVerifiedEmail(): bool
    {
        return ! config('auth.email_verification_required', true) || parent::hasVerifiedEmail();
    }

    public function sendEmailVerificationNotification(): void
    {
        if (config('auth.email_verification_required', true)) {
            parent::sendEmailVerificationNotification();
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function roleOptions(): Collection
    {
        return Role::orderBy('name')->get();
    }

    public function selectedRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function syncAssignedRoles(array $roles): void
    {
        $this->syncRoles($roles);
    }

    public static function registerFromData(array $data): self
    {
        return self::create($data);
    }

    public static function createWithRoles(array $data): self
    {
        $roles = $data['roles'];
        unset($data['roles']);

        $user = self::create($data);
        $user->syncRoles($roles);

        return $user;
    }

    public function updateWithRoles(array $data): void
    {
        $roles = $data['roles'];
        unset($data['roles']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $this->update($data);
        $this->syncRoles($roles);
    }

    public function deleteWithRoles(): void
    {
        $this->roles()->detach();
        $this->delete();
    }

    public function updateProfileData(array $data): void
    {
        $this->fill($data);

        if ($this->isDirty('email')) {
            $this->email_verified_at = null;
        }

        $this->save();
    }

    public function updatePassword(string $password): void
    {
        $this->update([
            'password' => $password,
        ]);
    }

    public function deleteAccount(): void
    {
        $this->deleteWithRoles();
    }
}
