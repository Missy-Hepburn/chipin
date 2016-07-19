@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <div class="panel panel-default">

          <div class="panel-heading bg-info">
            <div class="row">
              <div class="col-xs-8">@lang('web.user.edit-header')</div>
              <div class="col-xs-4">
                <a href="{{ route('user.index') }}" class="btn btn-link btn-xs pull-right"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list')</a>
              </div>
            </div>
          </div>

          <div class="panel-body">
            <!-- Display Validation Errors -->
            @include('common.errors')

            {{-- Edit user form --}}
            <form action="{{ route('user.update', ['user' => $user->id]) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
              {{ method_field('PUT') }}
              {!! csrf_field() !!}

              <div class="col-sm-6">
                <h5>@lang('web.user.general-header')</h5>
                <div class="form-group required">
                  <label for="user-first-name" class="col-sm-3 control-label">@lang('web.user.first-name')</label>

                  <div class="col-sm-8">
                    <input type="text" name="first_name" id="user-first-name" class="form-control" value="{{ old('first_name', $user->profile->first_name) }}" required>
                  </div>
                </div>

                <div class="form-group required">
                  <label for="user-last-name" class="col-sm-3 control-label">@lang('web.user.last-name')</label>

                  <div class="col-sm-8">
                    <input type="text" name="last_name" id="user-last-name" class="form-control" value="{{ old('last_name', $user->profile->last_name) }}" required>
                  </div>
                </div>

                <div class="form-group required">
                  <label for="user-email" class="col-sm-3 control-label">@lang('web.user.email')</label>

                  <div class="col-sm-8">
                    <input type="email" name="email" id="user-email" class="form-control" value="{{ old('email', $user->email) }}" required>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-8 col-sm-offset-3">
                    <input type="checkbox" name="active" id="user-active" class="checkbox-inline"
                           @if ($user->active) checked @endif>
                    @lang('web.user.active')
                  </label>
                </div>

                <h5>@lang('web.user.password-header')</h5>
                <div class="form-group">
                  <label for="user-password" class="col-sm-3 control-label">@lang('web.user.password')</label>

                  <div class="col-sm-8 required">
                    <input type="password" name="password" id="user-password" class="form-control" value=""
                           pattern=".{6,}"
                           title="@lang('web.err.pwd-len')"
                           placeholder="@lang('web.msg.password-change')"
                           onchange="
                           this.setCustomValidity(this.validity.patternMismatch ? this.title : '');
                           form.passwordcheck.pattern = this.value;
                         ">
                  </div>
                </div>
                <div class="form-group">
                  <label for="user-password-check" class="col-sm-3 control-label">@lang('web.user.password-check')</label>

                  <div class="col-sm-8">
                    <input type="password" name="passwordcheck" id="user-password-check" class="form-control"
                           value="" title="@lang('web.err.pwd-check')"
                           onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '');">

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
                                @if ((old('nationality') && old('nationality') == $code)
                                || ($user->profile->nationality && $user->profile->nationality == $code)) selected @endif
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
                                @if ((old('country') && old('country') == $code)
                                || ($user->profile->country && $user->profile->country == $code)) selected @endif
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
                    <input type="text" name="birthday" id="user-birthday" class="form-control" value="{{ old('birthday', $user->profile->birthday) }}"
                           placeholder="YYYY-MM-DD"
                           required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="user-address" class="col-sm-3 control-label">@lang('web.user.address')</label>

                  <div class="col-sm-8">
                    <input type="text" name="address" id="user-address" class="form-control" value="{{ old('address', $user->profile->address) }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="user-occupation" class="col-sm-3 control-label">@lang('web.user.occupation')</label>

                  <div class="col-sm-8">
                    <input type="text" name="occupation" id="user-occupation" class="form-control" value="{{ old('occupation', $user->profile->occupation) }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="user-income" class="col-sm-3 control-label">@lang('web.user.income')</label>

                  <div class="col-sm-8">
                    <input type="text" name="income" id="user-income" class="form-control" value="{{ old('income', $user->profile->income) }}">
                  </div>
                </div>
              </div>

              <div class="col-sm-12">
                <h5>@lang('web.user.photo-header')</h5>

                @if ($user->profile->photo)
                  <div id="existing-photo-block">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="photo" class="control-label">@lang('web.user.photo-current')</label>
                        <div>
                          <a href="{{ asset($user->profile->photo->getPath()) }}" target="_blank"><img src="{{ asset($user->profile->photo->getPath()) }}" style="max-width: 200px; max-height: 200px;"/></a>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="update-user-photo" class="control-label">@lang('web.user.photo-upload-replace')</label>
                        <div><input type="file" name="photo" id="update-user-photo" class="form-control" value="{{ old('photo') }}"></div>
                      </div>
                      <div class="form-group">
                        <input type="hidden" name="delete-photo" value="0" />
                        <input type="button" class="btn btn-default pull-right" data-action="delete-image" value="@lang('web.user.photo-delete')">
                      </div>
                    </div>
                  </div>
                @endif

                <div class="col-sm-6" id="new-photo-block" @if ($user->profile->photo) style="display: none;" @endif >
                  <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">@lang('web.user.photo-upload-new')</label>

                    <div class="col-sm-8">
                      <input type="file" name="new-photo" id="photo" class="form-control" value="{{ old('photo') }}">
                    </div>
                  </div>
                </div>


              </div>
              <!-- Add Button -->
              <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                  <button type="submit" class="btn btn-default">
                    <i class="fa fa-floppy-o"></i> @lang('web.btn.save-user')
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

    $("input[data-action=delete-image]").on('click', function () {
      $("#existing-photo-block").hide();
      $("#new-photo-block").show();
      $("input[name=delete-photo]").val(1);
    });
  });
</script>
@endpush
