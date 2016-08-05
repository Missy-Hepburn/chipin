@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <div class="panel panel-default">

          <div class="panel-heading bg-info">
            <div class="row">
              <div class="col-xs-8">@lang('web.category.create-header')</div>
              <div class="col-xs-4">
                <a href="{{ route('category.index') }}" class="btn btn-link btn-xs pull-right"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'category'])</a>
              </div>
            </div>
          </div>

          <div class="panel-body">
            <!-- Display Validation Errors -->
            @include('common.errors')

            {{-- New user form --}}
            <form action="{{ route('category.store') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
              {!! csrf_field() !!}

              <div class="col-sm-6">
                <h5>@lang('web.category.general-header')</h5>

                <div class="form-group required">
                  <label for="category-name" class="col-sm-3 control-label">@lang('web.category.name')</label>

                  <div class="col-sm-8">
                    <input type="text" name="name" id="category-name" class="form-control" value="{{ old('name') }}" required>
                  </div>
                </div>

              </div>
              <div class="col-sm-6">
                <h5>@lang('web.category.image-header')</h5>

                <div class="form-group">
                  <label for="category-image" class="col-sm-3 control-label">@lang('web.image.upload-new')</label>

                  <div class="col-sm-8">
                    <input type="file" name="image" id="category-image" class="form-control" value="">
                  </div>
                </div>

              </div>
              <!-- Add Button -->
              <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                  <button type="submit" class="btn btn-default">
                    <i class="fa fa-plus"></i> @lang('web.btn.add', ['item' => 'category'])
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