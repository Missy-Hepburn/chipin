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
                  @lang('web.category.search-header')
                @else
                  @lang('web.category.list-header')
                @endif
              </div>
              <div class="btn-group col-sm-4">
                <form action="{{ route('category.search') }}" method="GET" class="input-group input-group-sm">
                  {!! csrf_field() !!}
                  <input type="text" value="{{ $search or '' }}" placeholder="Search.." class="form-control input-sm" name="search"
                         pattern=".{3,}" required title="@lang('validation.min.string', ['attribute' => 'search string', 'min'=> '3'])"/>
                  <span class="input-group-btn"><button type="submit" class="btn btn-link btn-xs" ><span class="fa fa-search"></span></button></span>
                </form>
              </div>
              <div class="btn-group col-sm-2">
                @if (isset($search))
                  <a href="{{ route('category.index') }}" class="btn btn-link btn-sm btn-primary"><i class="fa fa-angle-left"></i> @lang('web.btn.back-to-list', ['item' => 'category'])</a>
                @endif
              </div>
              <div class="btn-group col-sm-2">
                <button id="delete-btn" class="btn btn-sm btn-default" disabled title="@lang('web.btn.delete')"
                        data-msg="@lang('web.msg.action-confirm', ['action' => 'delete','items' => 'categories'])">
                  <span class="fa fa-trash"></span></button>
              </div>
              <div class="btn-group col-sm-1">
                <a class="btn btn-default pull-right btn-sm" href="{{ route('category.create') }}"><i class="fa fa-plus small"></i> @lang('web.btn.add', ['item' => 'category'])</a>
              </div>
            </div>
          </div>
          @if (!count($categories))
            <div class="panel-body">
              @if (!isset($search))
                @lang('web.msg.empty-list', ['item' => 'category'])
              @else
                @lang('web.msg.empty-search', ['item' => 'category'])
              @endif

            </div>
          @else
            <table class="table table-hover" id="list-table">
              <tr>
                <td style="width:20px"></td>
                <td>@lang('web.category.name')</td>
                <td class="text-center">@lang('web.category.image')</td>
                <td class="text-center">@lang('web.category.count')</td>
              </tr>
              @foreach($categories as $category)
                <tr data-filter>
                  <td style="width:20px">@if ($category->countGoals() == 0)<input type="checkbox" data-id="{{$category->id}}"/>@endif</td>
                  <td>
                    <a href="{{ route('category.edit', ['category' => $category->id]) }}">{{ $category->name }}</a>
                  </td>
                  <td style="width: 20px;" class="text-center">
                    @if ($category->image)
                      <a href="{{ asset($category->image->getPath()) }}" target="_blank">
                        <span class="fa fa-file-image-o text-muted"></span>
                      </a>
                    @endif
                  </td>
                  <td class="text-center">{{$category->countGoals()}}</td>
                </tr>
              @endforeach
            </table>
            @if (isset($search))
              {{ $categories->appends(['search' => $search])->links() }}
            @else
              {{ $categories->links() }}
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
  $('#delete-btn').on('click', function() {
    if (!confirm($(this).data('msg'))) return;

    $.ajax({
      type: "DELETE",
      url: "{{ route('category.delete') }}",
      data: {ids:getChecked()},
      success: function(data) {
        data.ids.forEach(function (id) {
          findRowById(id).remove();
        })
      }
    });
  });

  $('#list-table').on('change', 'input[type="checkbox"]', function () {
    if ($('#list-table').find('input[type="checkbox"]:checked').length > 0) {
      $("#delete-btn").prop("disabled", false);
    } else {
      $("#delete-btn").prop("disabled", true);
    }
  });
});

function findRowById(id){
  return $('#list-table').find('input[data-id='+id+']').closest('tr');
}

function getChecked(){
  var ids = [];
  $('#list-table').find('input[type="checkbox"]:checked').each(function(i, v){
    ids.push(v.dataset.id);
  });
  return ids;
}
</script>
@endpush

