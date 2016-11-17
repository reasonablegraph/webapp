@section('content')


<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  
  @if (! empty($item['user_update']))
  	Record Updated: <strong>{{{$item_id}}}</strong> 
  @else
  	New Record Created: <strong>{{{$item_id}}}</strong>
  @endif
</div>


@include('admin.edit_item_step3')

@stop