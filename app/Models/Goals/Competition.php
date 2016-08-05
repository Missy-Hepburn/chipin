<?php
namespace App\Models\Goals;

use App\Models\Goal;
use App\Models\Invite;
use App\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class Competition extends Goal  {
    protected static $singleTableType = Goal::TYPE_COMPETITION;

    protected $_with = [];

    protected $clonedValues = [
        'name', 'category_id', 'category',
        'start_date', 'due_date', 'timer',
        'picture'
    ];

    protected static function boot() {
        parent::boot();

        static::updating(function(Goal $goal) {
            if ($goal->isStarted())
                throw new PreconditionFailedHttpException(trans('api.err.started-competition'));
        });
    }

    /* Relations */
    public function connectedGoals() {
        return $this->hasMany(Goal::class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(Goal::class, 'parent_id', 'id');
    }

    /* Mutators. Well.. almost them */
    public function getAttributeValue($key) {
        if (in_array($key, $this->clonedValues)) {
            if ($this->parent) {
               return $this->parent->{$key};
            }

            return parent::getAttributeValue($key);
        }

        return parent::getAttributeValue($key);
    }

    /* Restrictions */
    public function isInvitable() {
        if ($this->isStarted()) {
            throw new PreconditionFailedHttpException(trans('api.err.started-competition'));
        }

        if ($this->parent) {
            throw new PreconditionFailedHttpException(trans('api.err.cant-update'));
        }

        return parent::isInvitable();
    }

    /* Some processing */
    public function processInvite(Invite $invite, $attributes) {
        $status = $attributes['status'];
        $amount = $attributes['amount'];

        if (empty($amount)) {
            throw new PreconditionFailedHttpException(trans('app.err.competition-amount-empty'));
        }

        $goal = $invite->reference;
        if ($status == Invite::STATUS_ACCEPTED
            && $invite->status != Invite::STATUS_ACCEPTED) {

            /* Creating sub-goal for competitors */
            $goal = Competition::create([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'type' => $this->type,
                'start_date' => $this->start_date,
                'due_date' => $this->due_date,
                'timer' => $this->timer,
                'picture' => $this->picture,
                'amount' => $amount,
            ]);
            $goal->user()->associate($invite->user);

            $invite->reference_id = $goal->id;

            $goal->parent()->associate($this);
            $goal->push();
        }

        parent::processInvite($invite, $attributes);

        return $goal;
    }

    public function convertToPersonal() {
        /*TODO: Check me when payments will be done */
        $this->parent = null;
        $this->type = Goal::TYPE_PERSONAL;
        $this->save();
    }

    public function update(array $attributes = [], array $options = [])
    {
        $update = parent::update($attributes, $options);
        if ($update && $this->connectedGoals->count()) {
            foreach ($this->connectedGoals as $item) {
                $item->update([
                    'name' => $this->name,
                    'category_id' => $this->category_id,
                    'type' => $this->type,
                    'start_date' => $this->start_date,
                    'due_date' => $this->due_date,
                    'timer' => $this->timer,
                    'picture' => $this->picture
                ]);
            }
        }

        return $update;
    }

    public function toArray() {
        $array = parent::toArray();

        if ($this->connectedGoals->count()) {
            return array_merge($array, [
                'connectedGoals' => $this->connectedGoals
            ]);
        }
        if ($this->parent) {
            return array_merge($array, [
                'parent_id' => $this->parent->id
            ]);
        }

        return $array;
    }

}