<a style="font-size:90%"  title="Sort ascending" href="{{request()->fullUrlWithQuery(['order_by'=>$orderBy,'order_by_dir'=> 'asc'])}}" class=" {{!strcmp($currentOrder,$orderBy.':asc')?'active': 'text-muted' }}">
    <small class='glyphicon glyphicon-chevron-up sort-arrow small'></small>
</a><a style="font-size:90%" title="Sort descending"  href="{{request()->fullUrlWithQuery(['order_by'=>$orderBy,'order_by_dir'=> 'desc'])}}" class=" {{!strcmp($currentOrder,$orderBy.':desc')?'active': 'text-muted' }}"> 
    <small class='glyphicon glyphicon-chevron-down sort-arrow small'></small>
</a>
