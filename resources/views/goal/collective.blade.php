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
