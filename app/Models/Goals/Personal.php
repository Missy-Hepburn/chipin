<?php
namespace App\Models\Goals;

use App\Models\Goal;
use App\Models\Invite;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class Personal extends Goal  {
    protected static $singleTableType = Goal::TYPE_PERSONAL;

    protected static function boot() {
        parent::boot();

        static::updating(function(Goal $goal) {
            return true;
        });
    }

    public function participants() {
        return [$this->user];
    }

    public function isInvitable() {
        throw new PreconditionFailedHttpException(trans('api.err.personal-invite'));
    }

    public function processInvite(Invite $invite, $attributes) {
        throw new PreconditionFailedHttpException(trans('api.err.personal-invite'));
    }
}