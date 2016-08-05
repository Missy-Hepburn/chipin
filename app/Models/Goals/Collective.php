<?php
namespace App\Models\Goals;

use App\Models\Goal;
use App\Models\Invite;
use App\User;

class Collective extends Goal  {
    protected static $singleTableType = Goal::TYPE_COLLECTIVE;
    protected $_with = ['participants'];

    protected static function boot() {
        parent::boot();

        static::updating(function(Goal $goal) {

        });
    }

    public function participants() {
        return $this->belongsToMany(User::class, with(new Invite())->getTable(), 'goal_id', 'user_id')
            ->wherePivot('status', 'accepted');
    }

    public function processInvite(Invite $invite, $attributes)
    {
        $status = $attributes['status'];

        $goal = $invite->reference;
        if ($status == Invite::STATUS_ACCEPTED
            && $invite->status != Invite::STATUS_ACCEPTED) {

            $goal = $invite->goal;
            $invite->reference_id = $goal->id;
        }

        parent::processInvite($invite, $attributes);

        return $goal;
    }

    public function toArray() {
        $array = parent::toArray();

        if ($this->participants->count()) {

            return array_merge($array, [
                'participants' => $this->participants
            ]);
        }

        return $array;
    }

}