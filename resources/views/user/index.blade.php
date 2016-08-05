@extends('layouts.app')

@section('content')
  <div class="container" >
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="row">
              <div class="col-sm-3">
                @if (isset($search))
                  @lang('web.user.search-header')
                @else
                  @lang('web.user.list-header')
                @endif
              </div>
              <div class="btn-group col-sm-4">
                <form action="{{ route('user.search') }}" method="GET" class="input-group input-group-sm">
                  {!! csrf_field() !!}
                  <input type="text" value="{{ $search or '' }}" placeholder="Search.." class="form-control input-sm" name="search"
                         pattern=".{3,}" required title="@lang('validation.min.string', ['attribute' => 'search string', 'min'=> '3'])"/>
                  <span class="input-group-btn"><button type="submit" class="btn btn-link btn-xs" ><span class="fa fa-search"></span></button></span>
                </form>
              </div>
              <div class="btn-group col-sm-2">
                @if (isset($search))
                  <a href="{{ route('user.index') }}" class="btn btn-link btn-sm btn-primary"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'user'])</a>
                @endif
              </div>
              <div class="btn-group col-sm-2">
                <button id="activate_users" data-msg="@lang('web.msg.action-confirm', ['action' => 'activate', 'items' => 'users'])"
                        data-active="1" data-action="activate" disabled
                        class="btn btn-sm btn-default" title="@lang('web.btn.activate')"><span class="fa fa-thumbs-o-up"></span></button>
                <button id="deactivate_users" data-msg="@lang('web.msg.action-confirm', ['action' => 'block', 'items' => 'users'])"
                        data-active="0" data-action="deactivate" disabled
                        class="btn btn-sm btn-default" title="@lang('web.btn.block')"><span class="fa fa-thumbs-down"></span></button>
              </div>
              <div class="btn-group col-sm-1">
                <a class="btn btn-default pull-right btn-sm" href="{{ route('user.create') }}"><i class="fa fa-plus small"></i> @lang('web.btn.add', ['item' => 'user'])</a>
              </div>
            </div>
          </div>
          @if (!count($users))
            <div class="panel-body">
              @if (!isset($search))
                @lang('web.msg.empty-list', ['item' => 'user'])
              @else
                @lang('web.msg.empty-search', ['item' => 'user'])
              @endif
            </div>
          @else
            <table class="table table-hover" id="users-table">
              <tr>
                <td style="width:20px"></td>
                <td>@lang('web.user.name')</td>
                <td>@lang('web.user.email')</td>
                <td class="text-center">@lang('web.user.birthday')</td>
                <td class="text-center">@lang('web.user.active')</td>
                <td class="text-center">@lang('web.user.created') <span class="fa fa-chevron-down small"></span></td>
                <td class="text-center">@lang('web.user.last-login')</td>
              </tr>
              @foreach($users as $user)
                <tr data-filter>
                  <td style="width:20px"><input type="checkbox" data-user-id="{{$user->id}}"/></td>
                  <td><a href="{{ route('user.edit', ['user' => $user->id]) }}">{{ $user->profile->first_name }} {{ $user->profile->last_name }}</a></td>
                  <td>{{$user->email}}</td>
                  <td class="text-center">{{$user->profile->birthday}}</td>
                  <td class="user_active text-center">{{$user->active}}</td>
                  <td class="text-center">{{$user->created_at}}</td>
                  <td class="text-center">{{$user->last_login }}</td>
                </tr>
              @endforeach
            </table>
            @if (isset($search))
              {{ $users->appends(['search' => $search])->links() }}
            @else
              {{ $users->links() }}
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(function(){
  $('body').on('click', '#deactivate_users, #activate_users', function() {
    var btn = $(this);
    if (!confirm(btn.data('msg'))) return;

    $.post("/user/" + btn.data('action'), {ids: getCheckedUsers()}, function( data ) {
      data.ids.forEach(function(id) {
        findRowById(id).find('.user_active').text(btn.data('active'));
      });
    });
  });

  $('#users-table').on('change', 'input[type="checkbox"]', function () {
    if ($('#users-table').find('input[type="checkbox"]:checked').length > 0) {
      $('#activate_users, #deactivate_users').prop("disabled", false);
    } else {
      $('#activate_users, #deactivate_users').prop("disabled", true);
    }
  });
});

function findRowById(id){
  return $('#users-table').find('input[data-user-id='+id+']').closest('tr');
}

function getCheckedUsers() {
  var ids = [];
  $('#users-table').find('input[type="checkbox"]:checked').each(function( i, v){
    ids.push(v.dataset.userId);
  });
  return ids;
}
</script>
@endpush

