{{-- Push predefined stacks
    --}}
@foreach($laradmin->contentManager->getStacks() as $stack)
    @push($stack)
        {!!$laradmin->contentManager->pullStackContents($stack)!!}
    @endpush
@endforeach