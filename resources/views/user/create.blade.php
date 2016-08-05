@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="panel panel-default">

        <div class="panel-heading bg-info">
          <div class="row">
            <div class="col-xs-8">@lang('web.user.create-header')</div>
            <div class="col-xs-4">
              <a href="{{ route('user.index') }}" class="btn btn-link btn-xs pull-right"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'user'])</a>
            </div>
          </div>
        </div>

        <div class="panel-body">
          <!-- Display Validation Errors -->
          @include('common.errors')

          {{-- New user form --}}
          <form action="{{ route('user.store') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
            {!! csrf_field() !!}

            <div class="col-sm-6">
              <h5>@lang('web.user.general-header')</h5>
              <div class="form-group required">
                <label for="user-first-name" class="col-sm-3 control-label">@lang('web.user.first-name')</label>

                <div class="col-sm-8">
                  <input type="text" name="first_name" id="user-first-name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
              </div>

              <div class="form-group required">
                <label for="user-last-name" class="col-sm-3 control-label">@lang('web.user.last-name')</label>

                <div class="col-sm-8">
                  <input type="text" name="last_name" id="user-last-name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
              </div>

              <div class="form-group required">
                <label for="user-email" class="col-sm-3 control-label">@lang('web.user.email')</label>

                <div class="col-sm-8">
                  <input type="email" name="email" id="user-email" class="form-control" value="{{ old('email') }}" required>
                </div>
              </div>

              <div class="form-group required">
                <label for="user-password" class="col-sm-3 control-label">@lang('web.user.password')</label>

                <div class="col-sm-8 required">
                  <input type="password" name="password" id="user-password" class="form-control" value="" required
                         pattern=".{6,}"
                         title="@lang('web.err.pwd-len')"
                         onchange="
                           this.setCustomValidity(this.validity.patternMismatch ? this.title : '');
                           form.passwordcheck.pattern = this.value;
                         ">
                </div>
              </div>
              <div class="form-group required">
                <label for="user-password-check" class="col-sm-3 control-label">@lang('web.user.password-check')</label>

                <div class="col-sm-8">
                  <input type="password" name="passwordcheck" id="user-password-check" class="form-control"
                         value="" required title="@lang('web.err.pwd-check')"
                         onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '');">

                </div>
              </div>
              <div class="form-group">
                <label for="image" class="col-sm-3 control-label">@lang('web.user.image')</label>

                <div class="col-sm-8">
                  <input type="file" name="image" id="user-image" class="form-control" value="{{ old('image') }}">
                </div>
              </div>

            </div>
            <div class="col-sm-6">
              <h5>@lang('web.user.extended-header')</h5>
              <div class="form-group required">
                <label for="user-nationality" class="col-sm-3 control-label">@lang('web.user.nationality')</label>
                <div class="col-sm-8">
                  <select id="user-nationality" class="form-control" name="nationality" required>
                    @foreach($countries as $country => $code)
                      <option value="{{ $code }}"
                      @if (old('nationality') && old('nationality') == $code) selected @endif
                      >
                        {{ $country }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label for="user-country" class="col-sm-3 control-label">@lang('web.user.country')</label>

                <div class="col-sm-8">
                  <select id="user-country" class="form-control" name="country" required>
                    @foreach($countries as $country => $code)
                      <option value="{{ $code }}"
                              @if (old('country') && old('country') == $code) selected @endif
                      >
                        {{ $country }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label for="user-birthday" class="col-sm-3 control-label">@lang('web.user.birthday')</label>

                <div class="col-sm-8">
                  <input type="text" name="birthday" id="user-birthday" class="form-control" value="{{ old('birthday') }}"
                         placeholder="YYYY-MM-DD"
                         required>
                </div>
              </div>
              <div class="form-group">
                <label for="user-address" class="col-sm-3 control-label">@lang('web.user.address')</label>

                <div class="col-sm-8">
                  <input type="text" name="address" id="user-address" class="form-control" value="{{ old('address') }}">
                </div>
              </div>
              <div class="form-group">
                <label for="user-occupation" class="col-sm-3 control-label">@lang('web.user.occupation')</label>

                <div class="col-sm-8">
                  <input type="text" name="occupation" id="user-occupation" class="form-control" value="{{ old('occupation') }}">
                </div>
              </div>
              <div class="form-group">
                <label for="user-income" class="col-sm-3 control-label">@lang('web.user.income')</label>

                <div class="col-sm-8">
                  <input type="text" name="income" id="user-income" class="form-control" value="{{ old('income') }}">
                </div>
              </div>
            </div>
            <!-- Add Button -->
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-default">
                  <i class="fa fa-plus"></i> @lang('web.btn.add', ['item' => 'user'])
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $().ready(function() {
    $('#user-nationality').select2();
    $('#user-country').select2();
  });
</script>
@endpush