@foreach($laradmin->assetManager->getStacks() as $stack)
    @push($stack)
        {!!$laradmin->assetManager->getAssetsString($stack)!!}
    @endpush
@endforeach