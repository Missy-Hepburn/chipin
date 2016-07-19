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
                  <a href="{{ route('user.index') }}" class="btn btn-link btn-sm btn-primary"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list')</a>
                @endif
              </div>
              <div class="btn-group col-sm-2">
                <button id = "deactivate_users" class="btn btn-sm"><span class="fa fa-thumbs-down"></span></button>
                <button id = "activate_users" class="btn btn-sm"><span class="fa fa-thumbs-o-up"></span></button>
              </div>
              <div class="btn-group col-sm-1">
                <a class="btn btn-default pull-right btn-sm" href="{{ route('user.create') }}"><i class="fa fa-plus small"></i> @lang('web.btn.add-user')</a>
              </div>
            </div>
          </div>
          @if (!count($users))
            <div class="panel-body">
              @if (!isset($search))
                @lang('web.msg.empty-user-list')
              @else
                @lang('web.msg.empty-user-search')
              @endif
            </div>
          @else
            <table class="table table-hover" id="users-table">
              <tr>
                <td style="width:20px"></td>
                <td>@lang('web.user.name')</td>
                <td>@lang('web.user.email')</td>
                <td>@lang('web.user.birthday')</td>
                <td>@lang('web.user.country')</td>
                <td>@lang('web.user.created')</td>
                  <td>@lang('web.user.active')</td>
              </tr>
              @foreach($users as $user)
                <tr data-filter>
                  <td style="width:20px"><input type="checkbox" data-user-id="{{$user->id}}"/></td>
                  <td><a href="{{ route('user.edit', ['user' => $user->id]) }}">{{ $user->profile->first_name }} {{ $user->profile->last_name }}</a></td>
                  <td>{{$user->email}}</td>
                  <td>{{$user->profile->birthday}}</td>
                  <td>{{$user->profile->country}}</td>
                  <td>{{$user->created_at}}</td>
                    <td class="user_active">{{$user->active}}</td>
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
<script type="text/javascript" src="{{ URL::asset('js/user.js') }}"></script>
@endpush

