<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model  {

    /* Constants */
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_PENDING = 'pending';

    static public function getStatuses() {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_DECLINED,
            self::STATUS_PENDING
        ];
    }

    static public function getUpdatableStatuses() {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_DECLINED
        ];
    }

    /* Defaults */
    protected $attributes = [
        'status' => self::STATUS_PENDING
    ];

    /* Relations */
    public function goal() {
        return $this->belongsTo(Goal::class, 'goal_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reference() {
        return $this->hasOne(Goal::class, 'id', 'reference_id');
    }

    static public function lookup(Goal $goal, User $user) {
        return Invite::where('user_id', $user->id)->where('goal_id', $goal->id)->first();
    }
}