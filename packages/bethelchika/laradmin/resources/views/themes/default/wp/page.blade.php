{{-- This is just an entry to load the templates
    
    INPUTS [i.e the ones used here]
    $tpl string The correct template to load.
    $tpl_default The default template incase the correct one is not found.
--}}
{{--TODO: NOTE that the str_replace is likely to lead to a file that does not exists if the $tpl happens to contain the theme name more than once--}}
@includeFirst([
                $tpl, 
                $tpl_default,
                str_replace($laradmin->theme->from,$laradmin->theme->defaultFrom().'',$tpl),
                str_replace($laradmin->theme->from,$laradmin->theme->defaultFrom().'',$tpl_default)
                ]){{-- of course the View variables will be made avaailable to the included view by Laravel--}}
