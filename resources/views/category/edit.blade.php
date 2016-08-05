@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <div class="panel panel-default">

          <div class="panel-heading bg-info">
            <div class="row">
              <div class="col-xs-8">@lang('web.category.edit-header')</div>
              <div class="col-xs-4">
                <a href="{{ route('category.index') }}" class="btn btn-link btn-xs pull-right"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'category'])</a>
              </div>
            </div>
          </div>

          <div class="panel-body">
            <!-- Display Validation Errors -->
            @include('common.errors')

            {{-- Edit Ca form --}}
            <form action="{{ route('category.update', ['category' => $category->id]) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
              {{ method_field('PUT') }}
              {!! csrf_field() !!}

                <div class="col-sm-6">
                <h5>@lang('web.category.general-header')</h5>

                  <div class="form-group required">
                    <label for="category-name" class="col-sm-3 control-label">@lang('web.category.name')</label>

                    <div class="col-sm-8">
                      <input type="text" name="name" id="category-name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    </div>
                  </div>
  
                  <div class="form-group">
                    <label for="category-count" class="col-sm-3 control-label">@lang('web.category.count')</label>
    
                    <div class="col-sm-8 checkbox">
                      <a href="{{ route('goal.search', ['category' => $category->id]) }}">{{$category->countGoals()}}</a>
                    </div>
                  </div>
  
                  
                </div>

                <div class="@if (!$category->image) col-sm-6 @else col-sm-12 @endif" id="image-block">
                  <h5>@lang('web.category.image-header')</h5>

                  @if ($category->image)
                    <input type="hidden" name="delete-image" value="0" />
                    <div class="col-sm-6" id="current-image-block">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <a href="{{ asset($category->image->getPath()) }}" target="_blank"><img src="{{ asset($category->image->getPath()) }}" style="max-width: 200px; max-height: 200px;"/></a>
                        </div>
                        <div class="col-sm-3">
                          <input type="button" class="btn btn-default " data-action="delete-image" value="@lang('web.image.delete')">
                        </div>
                      </div>
                    </div>
                  @endif
                  <div class="@if (!$category->image) col-sm-12 @else col-sm-6 @endif" id="upload-image-block">
                    <div class="form-group">
                      <label for="category-image" class="col-sm-8 " id="update-lbl" @if (!$category->image)style="display: none"@endif>@lang('web.image.upload-replace')</label>
                      <label for="category-image" class="col-sm-3 control-label" id="upload-lbl" @if ($category->image)style="display: none"@endif>@lang('web.image.upload-new')</label>

                      <div class="col-sm-8">
                        <input type="file" name="image" id="category-image" class="form-control" value="">
                      </div>
                    </div>
                  </div>

                </div>

              <div class="row">
                <!-- Add Button -->
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-default">
                      <i class="fa fa-plus"></i> @lang('web.btn.save', ['item' => 'category'])
                    </button>
                  </div>
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
  $("input[data-action=delete-image]").on('click', function () {
    $("#update-lbl").hide();
    $("#upload-lbl").show();
    $("#current-image-block").remove();
    $("#image-block").removeClass("col-sm-12").addClass("col-sm-6");
    $("#upload-image-block").removeClass("col-sm-6").addClass("col-sm-12");
    $("input[name=delete-image]").val(1);
  });
</script>
@endpush