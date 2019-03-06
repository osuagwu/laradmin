@foreach(app('laradmin')->assetManager->getStacks() as $stack)
    @push($stack)
        {!!app('laradmin')->assetManager->getAssetsString($stack)!!}
    @endpush
@endforeach