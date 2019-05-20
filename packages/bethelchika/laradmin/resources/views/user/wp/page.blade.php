{{-- This is just an entry to load the templates
    
    INPUTS [i.e the ones used here]
    $tpl string The correct template to load.
    $tpl_default The default template incase the correct one is not found.
--}}
@includeFirst([$tpl, $tpl_default]){{-- of course the View variables will be made avaailable to the included view by Laravel--}}