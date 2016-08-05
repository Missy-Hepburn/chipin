<?php

namespace App\Models;

use App\Models\Goals\Competition;
use App\Models\Goals\Collective;
use App\Models\Goals\Personal;
use App\Models\Images\GoalImage;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Goal extends Model {
    use ImageTrait, SingleTableInheritanceTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'category_id', 'start_date',
        'due_date', 'amount', 'timer', 'type'
    ];

    protected $with = ['category', 'image'];
    protected $_with = [];

    protected $visible = [
        'id', 'name', 'category', 'wallet', 'user',
        'start_date', 'due_date', 'amount', 'image', 'type',
        'created_at', 'updated_at'];

    protected $appends = ['progress'];

    /* Defaults */
    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
        'timer' => self::DEFAULT_TIMER,
        'type' => self::DEFAULT_TYPE
    ];

    /* Constants */
    const TYPE_PERSONAL = 'personal';
    const TYPE_COMPETITION = 'competition';
    const TYPE_COLLECTIVE = 'collective';

    static public function getTypes() {
        return [
            self::TYPE_PERSONAL,
            self::TYPE_COMPETITION,
            self::TYPE_COLLECTIVE,
        ];
    }

    const TIMER_DAILY = 'daily';
    const TIMER_WEEKLY = 'weekly';
    const TIMER_TWO_WEEKS = 'two-weeks';
    const TIMER_MONTHLY = 'monthly';

    static public function getTimers() {
        return [
            self::TIMER_DAILY,
            self::TIMER_WEEKLY,
            self::TIMER_TWO_WEEKS,
            self::TIMER_MONTHLY,
        ];
    }

    const STATUS_ACTIVE = 'active';
    const STATUS_CASHBACK = 'cashback';

    static public function getStatuses() {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_CASHBACK
        ];
    }

    const DEFAULT_TIMER = self::TIMER_WEEKLY;
    const DEFAULT_TYPE = self::TYPE_PERSONAL;

    /* Image Trait */
    protected $imageClass = GoalImage::class;

    /* Single Table Inheritance Trait*/
    protected $table = "goals";
    protected static $singleTableTypeField = 'type';
    protected static $singleTableSubclasses = [
        Personal::class,
        Competition::class,
        Collective::class,
    ];
    protected static $singleTableSubclassesMap = [
        self::TYPE_PERSONAL => Personal::class,
        self::TYPE_COMPETITION=> Competition::class,
        self::TYPE_COLLECTIVE => Collective::class,
    ];

    public function __construct(array $attributes = []) {
        $this->with = array_merge($this->with, $this->_with);

        parent::__construct($attributes);
    }

    protected static function boot() {
        parent::boot();

        static::updating(function(Goal $goal) {
            if (($goal->user && Auth::user() != $goal->user)
                && (!$goal->parent || $goal->parent->user != Auth::user())) {
                throw new UnauthorizedHttpException(trans('api.err.deny-goal-update'));
            }

            if ($goal->status == self::STATUS_CASHBACK) {
                throw new PreconditionFailedHttpException(trans('api.err.edit-cashback'));
            }

            if ($goal->isFinished()) {
                throw new PreconditionFailedHttpException(trans('api.err.finished-competition'));
            }
        }, 1);
    }

    /* Calculated & Mutators */
    public function getMoneySaved() {
        return 0;
    }

    public function isGoalReached() {
        return ($this->amount <= $this->getMoneySaved());
    }

    public function getProgressAttribute() {
        $this->attributes['progress'] = round(($this->money_saved * 100) / $this->amount, 2);
    }

    public function getLastPaymentAttribute() {
        $this->attributes['last_payment'] = 0;
    }

    /* Relations */
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function invites() {
        return $this->belongsToMany(User::class, with(new Invite())->getTable(), 'goal_id', 'user_id')
            ->withPivot('status', 'created_at', 'updated_at');
    }

    /* Scopes */
    public function scopeAuth($query) {
        return $query->where('user_id', Auth::user()->id);
    }

    /* Restrictions */
    public function isStarted() {
        return (Carbon::parse($this->start_date) <= Carbon::now());
    }

    public function isFinished() {
        return (Carbon::parse($this->due_date) <= Carbon::now());
    }

    public function isInvitable() {
        if ($this->isFinished()) {
            throw new PreconditionFailedHttpException(trans('api.err.finished-goal'));
        }

        return true;
    }

    /* Stuff */
    public function addWith($attributes) {
        if (!is_array($attributes)) $attributes = [$attributes];
        $this->with = array_merge($this->with, $attributes);

        return $this;
    }

    public function processInvite(Invite $invite, $attributes) {
        $invite->status = $attributes['status'];
        $invite->save();

        return $this;
    }

    /* Making some kind of factory method */
    static public function create(array $attributes = []) {
        if (empty($attributes[self::$singleTableTypeField])) {
            $attributes[self::$singleTableTypeField] = self::DEFAULT_TYPE;
        }

        $className = self::$singleTableSubclassesMap[$attributes[self::$singleTableTypeField]];

        $model = new $className($attributes);
        $model->save();

        return $model;
    }

    public function toArray()
    {
        $retArr = parent::toArray();

        if ($this->pivot && $this->pivot->status) {
            $retArr = array_merge($retArr, ['invite_status' => $this->pivot->status]);
        }
        if (in_array('invites', $this->with)) {
            $retArr = array_merge($retArr, ['invites' => $this->invites]);
        }

        if (Auth::user()->id != $this->user->id) {
            $retArr['amount'] = null;
            $retArr['wallet'] = null;
        }

        $retArr['progress'] = $this->progress;

        return $retArr;
    }

}