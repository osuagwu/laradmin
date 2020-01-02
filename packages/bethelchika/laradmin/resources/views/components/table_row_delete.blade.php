<form method="post" style="display:inline" action="{{$formAction}}"  class="{{$class??''}}">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}
    <button title="Delete" onclick="return confirm('Are you sure you want to delete this item?')" type="submit" style="background-color:transparent;border:none;">
        <span >&times;</span>
    </button>

</form>