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
                  @lang('web.goal.search-header')
                @else
                  @lang('web.goal.list-header')
                @endif
              </div>
              <form action="{{ route('goal.search') }}" method="GET">
                <div class="btn-group col-sm-3">
                  <div class="input-group input-group-sm">
                    <span class="input-group-btn"><button type="submit" class="btn btn-link btn-xs" ><span class="fa fa-search"></span></button></span>
                    {!! csrf_field() !!}
                    <input type="text" value="{{ $search or '' }}" placeholder="@lang('web.goal.search-placeholder')" class="form-control input-sm" name="search"
                           pattern=".{3,}" title="@lang('validation.min.string', ['attribute' => 'search string', 'min'=> '3'])"/>
                  </div>
                </div>
                <div class="btn-group col-sm-2">
                  <select class="form-control input-sm" name="type">
                    <option value="">@lang('web.goal.find-type')</option>
                    @foreach($types as $item)
                      <option value="{{ $item }}"
                              @if(!empty($type) && $item == $type) selected @endif>
                        @lang('web.goal.type-' . $item)</option>
                    @endforeach
                  </select>
                </div>
                <div class="btn-group col-sm-2">
                  <select class="form-control input-sm" name="category">
                    <option value="">@lang('web.goal.find-category')</option>
                    @foreach($categories as $item)
                      <option value="{{ $item->id }}"
                              @if(!empty($category) && $item->id == $category) selected @endif>
                        {{ $item->name }}</option>
                    @endforeach
                  </select>
                </div>
              </form>
              <div class="btn-group col-sm-2">
                @if (isset($search))
                  <a href="{{ route('goal.index') }}" class="btn btn-link btn-sm btn-primary"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'goal'])</a>
                @endif
              </div>
            </div>
          </div>
          @if (!count($goals))
            <div class="panel-body">
              @if (!isset($search))
                @lang('web.msg.empty-list', ['item' => 'goal'])
              @else
                @lang('web.msg.empty-search', ['item' => 'goal'])
              @endif
            
            </div>
          @else
            <table class="table table-hover" id="list-table">
              <tr>
                <td>@lang('web.goal.user-name') @if(!empty($user))<span class="text-muted">@lang('web.goal.filtered')</span>@endif</td>
                <td>@lang('web.goal.name')</td>
                <td class="text-center">@lang('web.goal.type')</td>
                <td class="text-center">@lang('web.goal.category')</td>
                <td class="text-center">@lang('web.goal.status')</td>
                <td class="text-center">@lang('web.goal.progress')</td>
                <td class="text-center">@lang('web.goal.last-payment')</td>
              </tr>
              @foreach($goals as $goal)
                <tr data-filter>
                  <td>
                    <a href="{{ route('user.edit', ['user' => $goal->user->id]) }}" class="text-black">
                      {{ $goal->user->profile->name }}
                    </a>
                  </td>
                  <td><a href="{{ route('goal.show', ['goal' => $goal->id]) }}">{{ $goal->name }}</a></td>
                  <td class="text-center">@lang('web.goal.type-' . $goal->type)</td>
                  <td class="text-center">
                    <a href="{{ route('category.edit', ['category' => $goal->category->id]) }}" class="text-black">
                    {{ $goal->category->name }}
                    </a>
                  </td>
                  <td class="text-center">@lang('web.goal.status-' . $goal->status )</td>
                  <td class="text-center">{{ $goal->progress or 0 }} %</td>
                  <td class="text-center">
                    @if ($goal->last_payment)
                      {{ $goal->last_payment }}
                    @else
                      @lang('web.goal.no-payments')
                    @endif
                  </td>
                </tr>
              @endforeach
            </table>
            @if (isset($search))
              {{ $goals->appends(['search' => $search])->links() }}
            @else
              {{ $goals->links() }}
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection


