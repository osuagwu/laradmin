{{-- Push predefined stacks
    --}}
@foreach($laradmin->contentManager->getStacks() as $stack)
    @push($stack)
        {!!$laradmin->contentManager->getStackContents($stack)!!}
    @endpush
@endforeach