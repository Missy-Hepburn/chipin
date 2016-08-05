@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        
        <div class="panel panel-default">
          
          <div class="panel-heading bg-info">
            <div class="row">
              <div class="col-xs-8">
                @lang('web.goal.view-header', ['type' => $goal->type])
                &nbsp;
                (@if($goal->status == \App\Models\Goal::STATUS_ACTIVE)
                  <span class="text-success">@lang('web.goal.status-active')</span>
                @elseif($goal->status == \App\Models\Goal::STATUS_CASHBACK)
                  <span class="text-warning">@lang('web.goal.status-cashback')</span>
                @else
                  <span class="text-danger">@lang('web.goal.status-unknown')</span>
                @endif)
              </div>
              <div class="col-xs-4">
                <a href="{{ route('goal.index') }}" class="btn btn-link btn-xs pull-right"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list',['item' => 'goal'])</a>
              </div>
            </div>
          </div>
          
          <div class="panel-body form-horizontal">
            <div class="col-sm-6">
              <h5 class="text-center">@lang('web.goal.general-header')</h5>
              <div class="form-group">
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.name')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static">{{ $goal->name }}</p>
                  </div>
                </div>
                
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.user-name')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static"><a href="{{ route('user.edit', ['user' => $goal->user]) }}">{{ $goal->user->profile->name }}</a></p>
                  </div>
                </div>
                
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.category')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static"><a href="{{ route('category.edit', ['category' => $goal->category]) }}">{{ $goal->category->name }}</a></p>
                  </div>
                </div>
              </div>
              
              <h5 class="text-center">@lang('web.goal.payment-header')</h5>
              <div class="form-group">
  
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.amount')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static">{{ $goal->amount }}</p>
                  </div>
                </div>
  
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.progress')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static">{{ $goal->progress or 0 }} %</p>
                  </div>
                </div>
                
                <div>
                  <label class="col-sm-3 control-label">@lang('web.goal.last-payment')</label>
                  <div class="col-sm-9">
                    <p class="form-control-static">
                    @if ($goal->last_payment)
                      {{ $goal->last_payment }}
                      @else
                        @lang('web.goal.no-payments')
                      @endif
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-sm-6">
              <h5 class="text-center">@lang('web.goal.time-header')</h5>
              <div class="form-group">
                <div>
                  <label class="col-sm-4 control-label">@lang('web.goal.created')</label>
                  <div class="col-sm-8">
                    <p class="form-control-static">{{ $goal->created_at }}</p>
                  </div>
                </div>
                <div>
                  <label class="col-sm-4 control-label">@lang('web.goal.start-date')</label>
                  <div class="col-sm-8">
                    <p class="form-control-static">{{ $goal->start_date }}</p>
                  </div>
                </div>
                <div>
                  <label class="col-sm-4 control-label">@lang('web.goal.due-date')</label>
                  <div class="col-sm-8">
                    <p class="form-control-static">{{ $goal->due_date }}</p>
                  </div>
                </div>
              </div>
              @if($goal->image)
                <h5 class="text-center">@lang('web.goal.image-header')</h5>
                <div class="form-group">
                <div>
                    <div class="col-sm-12 text-center">
                      <a href="{{ asset($goal->image->getPath()) }}" target="_blank">
                        <img src="{{ asset($goal->image->getPath()) }}" style="max-width: 200px; max-height: 200px;"/>
                      </a>
                    </div>
                  </div>
                </div>
              @endif
            </div>
            
            <div class="col-sm-12">
              @if ($goal->type == \App\Models\Goal::TYPE_COMPETITION)
                @include('goal.competition', ['goal' => $goal])
              @elseif($goal->type == \App\Models\Goal::TYPE_COLLECTIVE)
                @include('goal.collective', ['goal' => $goal])
              @endif
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
