<h5 class="text-center">@lang('web.goal.participants-header')</h5>
<table class="table table-hover" >
  @if ($goal->connectedGoals->count())
      <tr>
      <td>@lang('web.goal.user')</td>
      <td class="text-center">@lang('web.goal.amount')</td>
      <td class="text-center">@lang('web.goal.progress')</td>
      <td class="text-center">@lang('web.goal.created')</td>
      <td class="text-center">@lang('web.goal.last-payment')</td>
      <td class="text-center">@lang('web.goal.status')</td>
    </tr>
    @foreach ($goal->connectedGoals as $item)
      <tr>
        <td>
          <a href="{{ route('user.edit', ['user' => $item->user]) }}">{{ $item->user->profile->name }}</a>
        </td>
        <td class="text-center">{{ $item->amount }}</td>
        <td class="text-center">{{ $item->progress or 0 }} %</td>
        <td class="text-center">{{ $item->created_at }}</td>
        <td class="text-center">@if ($item->last_payment) {{ $item->last_payment }} @else @lang('web.goal.no-payments') @endif</td>
        <td class="text-center">
          @if($goal->status == \App\Models\Goal::STATUS_ACTIVE)
            <span class="text-success">@lang('web.goal.status-active')</span>
          @elseif($goal->status == \App\Models\Goal::STATUS_CASHBACK)
            <span class="text-warning">@lang('web.goal.status-cashback')</span>
          @else
            <span class="text-danger">@lang('web.goal.status-unknown')</span>
          @endif
        </td>
      </tr>
    @endforeach
  @else
    <tr><td>@lang('web.goal.no-participants')</td></tr>
  @endif
</table>

<h5 class="text-center">@lang('web.goal.invites-header')</h5>
<table class="table table-hover" >
  @if($goal->invites->count())
    <tr>
      <td>@lang('web.goal.user')</td>
      <td class="text-center">@lang('web.goal.invite-status')</td>
      <td class="text-center">@lang('web.goal.invite-sent')</td>
      <td class="text-center">@lang('web.goal.invite-changed')</td>
      <td class="text-center">@lang('web.user.last-login')</td>
    </tr>
    @foreach ($goal->invites as $item)
      <tr>
        <td><a href="{{ route('user.edit', ['user' => $item]) }}">{{ $item->profile->name }}</a></td>
        <td class="text-center">{{ $item->pivot->status }}</td>
        <td class="text-center">{{ $item->pivot->created_at }}</td>
        <td class="text-center">{{ $item->pivot->updated_at }}</td>
        <td class="text-center">{{ $item->last_login }}</td>
      </tr>
    @endforeach
  @else
    <tr><td>@lang('web.goal.no-invites')</td></tr>
  @endif
</table>