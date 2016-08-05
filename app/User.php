<?php

namespace App;

use App\Models\Friend;
use App\Models\Goal;
use App\Models\Invite;
use Carbon\Carbon;
use Hootlex\Friendships\Models\Friendship;
use Hootlex\Friendships\Status;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'fb_token'
    ];

    protected $with = [
        'profile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'fb_token', 'profile_id'
    ];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function goals() {
        return $this->hasMany(Goal::class);
    }

    public function getFriendInvites() {
        return $this->belongsToMany(User::class, with(new Friendship())->getTable(), 'recipient_id', 'sender_id')
            ->withPivot('status')
            ->wherePivot('status', Status::PENDING);
    }

    public function getFriendRequests() {
        return $this->belongsToMany(User::class, with(new Friendship())->getTable(), 'sender_id', 'recipient_id')
            ->withPivot('status')
            ->wherePivot('status', Status::PENDING);
    }

    public function invites() {
        return $this->belongsToMany(Goal::class, with(new Invite())->getTable(), 'user_id', 'goal_id')
            ->withPivot('status')
            ->where('due_date', '>', Carbon::now()->toDateTimeString());
    }

    public function toArray() {
        $array = array_merge(
            ['email' => $this->email],
            $this->profile->toArray(),
            ['updated_at' => max($this->updated_at, $this->profile->updated_at)
                ->toDateTimeString(),
            'id' => $this->id]
        );

        if ($this->pivot && $this->pivot->status) {
            return array_merge($array, ['invite_status' => $this->pivot->status]);
        }

        return $array;
    }

}
