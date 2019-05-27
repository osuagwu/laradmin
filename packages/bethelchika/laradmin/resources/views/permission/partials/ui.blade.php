{{--
    Permission ui partial
    
    INPUTS
    $source_type string The source of the source 
    $source_id string The identifier of the source
    --}}


<h2>Permissions <a href="{{route('cp-help')}}#permission"><i class="far fa-question-circle"></i></a></h2> 
<form id="permission-form" onsubmit="updatePermissionString()" class="form-horizontal bg-info"  role="form" method="post" action="{{route('cp-source-permission-update')}}">
    {{ method_field('PUT') }}
    {{ csrf_field() }}
    <input id="permission-str" name="permission_str" type="hidden" value="0000"/>
    <div class="form-group">
        <label  class="col-xs-12" for="permissions-selector" >Users or group names: </label>
        <div class="col-md-8 col-xs-10">
            <select class="form-control" name="permission_id"  id="permissions-selector" data-source_type="{{$source_type}}" data-source_id="{{$source_id}}" size="5" onchange="showPermission()">
                @foreach($laradmin->permission->uiSourcePermissions($source_type,$source_id) as $perm)
                    <option value="{{$perm->id}}" data-id="{{$perm->data_id}}" data-isgroup="{{$perm->isGroup}}" data-permissions="{{$perm->create.$perm->read.$perm->update.$perm->delete}}"  @if(!strcmp(old('permission_id'),$perm->id)) selected="selected" @endif> {{$perm->name.'<'.$perm->email.'>'}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-xs-2">
            <button title="Add users and groups" type="button" class="btn btn-default" data-toggle="modal" data-target="#user-and-group-search-add"><span class="glyphicon glyphicon-plus-sign"></span></button>
            <br /><br />
            <button title="Remove selected user or group" type="button" class="btn btn-default" onclick="removeUserOrGroup()"><span class="glyphicon glyphicon-minus-sign"></span></button>
        </div>
    </div>

    <div class="form-group">
    
        <h3 class="col-md-4 ">Permision for <span id="permission-for"></span></h3>
        <div class="col-md-2 ">   
            <div class=" checkbox">
                <label ><input class="  permission-checkboxes" name="permission_create" id="permission-create" type="checkbox" >Create </label>
                <br />
                <label><input class=" permission-checkboxes" name="permission_read" id="permission-read" type="checkbox" >Read </label>
                <br />
                <label><input class="permission-checkboxes" name="permission_update" id="permission-update" type="checkbox" >Update </label>
                <br />
                <label><input class="permission-checkboxes" name="permission_delete" id="permission-delete" type="checkbox" >Delete </label>
                <br />
            </div>
        </div>
        <div class="col-md-3">
            <br />
            
            <input type="submit" class="btn btn-primary"  value="Apply permission" />
        </div>
            
        
    </div>                 
</form>

<!-- Modal -->
<div class="modal fade" id="user-and-group-search-add" role="dialog">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add user or group</h4>
            </div>
            <div class="modal-body">
                <div id="permission-search-add-msg" class="alert alert-info">
                    <span class="glyphicon "></span>
                    <span class="msg"> Search and add users and groups.</span>
                </div>
                <form id="user_and _group_search_form" onsubmit="userAndGroupSearch(this);return false;" class="form-horizontal" role="form" action="{{route('cp-source-permission-search-users')}}">
                    <div class="form-group" >
                        
                        <div class="col-md-8">
                            <input class="form-control" placeholder="User or group name" type="text" id="user_or_group_search" name="user_or_group_search" />
                        </div>
                        <div class="col-md-2">
                            <input type="submit" class="btn btn-primary"  value="Search" />
                        </div>
                        
                    </div>
                    <div class="form-group" >    
                        <div class="col-md-8">
                            <select class="form-control" id="user-or-group-results" size="3" ><option>Search results</option></select>
                        </div>
                        <div class="col-md-2">
                        <input type="button" class="btn btn-primary"  value="Add selected" onclick="addSelectedToPermissions()" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@push('footer-scripts')
<script>
function showPermission(){
    selected=jQuery('#permissions-selector');

    //first clear all
    jQuery('#permission-for').html('');
    jQuery('#permission-create').prop({disabled:true,checked:false});
    jQuery('#permission-read').prop({disabled:true,checked:false});
    jQuery('#permission-update').prop({disabled:true,checked:false}); 
    jQuery('#permission-delete').prop({disabled:true,checked:false});

    //now look ahead if there is any permision present
    if(!selected.find('option').length || !selected.find(':selected').length){
        return;
        ///alert(selected.find('option').length)
    }

    //now start getting the putting text
    var txt=jQuery(selected).find(':selected').text();
    isGroup=jQuery(selected).find(':selected').data('isgroup');
    htm=txt+' <span class="glyphicon glyphicon-user small">';
    //console.log(isGroup);
    if(isGroup){
        htm=txt+' <span class="glyphicon glyphicon-user small"></span><span class="glyphicon glyphicon-user small"></span>';
    }
    jQuery('#permission-for').html(htm);
    
    perms=jQuery(selected).find(':selected').data('permissions'); 
    
    perms=perms.toString().split('');
    jQuery('#permission-create').prop({disabled:false,checked:parseInt(perms[0])});
    jQuery('#permission-read').prop({disabled:false,checked:parseInt(perms[1])});
    jQuery('#permission-update').prop({disabled:false,checked:parseInt(perms[2])});
    jQuery('#permission-delete').prop({disabled:false,checked:parseInt(perms[3])});
}  



jQuery(document).ready(function(){
    showPermission();
}); 
function updatePermissionString(){
    isChecked=[];
    
    if(jQuery('#permission-create').prop('checked')){
        isChecked.push(1);
    }else{isChecked.push(0);}

    
    if(jQuery('#permission-read').prop('checked')){
        isChecked.push(1);
    }else{isChecked.push(0);}

    
    if(jQuery('#permission-update').prop('checked')){
        isChecked.push(1);
    }else{isChecked.push(0);}

    
    if(jQuery('#permission-delete').prop('checked')){
        isChecked.push(1);
    }else{isChecked.push(0);}
    
    //console.log(isChecked)
    $('#permission-str').val(isChecked.join('')); 
}

function userAndGroupSearch(form){
    //console.log(form);
    //return false;
    var $form=$(form);
    var term = $form.find("input[name='user_or_group_search']" ).val();
    var url = $form.attr( "action" );
   
    $('#user-or-group-results').find('option').remove();
    $('#user-or-group-results').append($('<option>',{text:'Please wait . . .'}));

    //update user info
    $('#permission-search-add-msg').removeClass('alert-danger').addClass('alert-info');
    $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
    $('#permission-search-add-msg .msg').html('Searching ...');

    var jqxhr = $.get(url,{s:term})
        .done(function(data) {
            
            $('#user-or-group-results').find('option').remove();

            //update info for user
            $('#permission-search-add-msg').removeClass('alert-danger').addClass('alert-info');
            $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-warning-sign').removeClass('glyphicon-ok');
            $('#permission-search-add-msg .msg').html(data.length + ' results');

            $.each(data, function (i, data) {
                $('#user-or-group-results').append($('<option>', { 
                    value: 0,
                    text : data.name+'<'+data.email+'>',
                    'data-isgroup':data.isgroup,
                    'data-permissions':'0000',
                    'data-id':data.id,
                    
                }));
            });
        })
        .fail(function(data) {
            var d=data.responseJSON;
            //update user info
            $('#permission-search-add-msg').removeClass('alert-info').addClass('alert-danger');
            $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-warning-sign');
            $('#permission-search-add-msg .msg').html(d.msg);
            //console.log(data.responseJSON);
        })
        .always(function(data) {
            //alert( "finished" );
            //console.log(data);
        })
}

function addSelectedToPermissions(){
    var $toAdd=jQuery('#user-or-group-results').find(':selected');
    var userOrGroupId=$toAdd.data('id');
    var isGroup=$toAdd.data('isgroup');
    var isPresent=false;// We will use this to prevent adding duplicate from client side although server can take care of it

    jQuery('#permissions-selector').find('option').each(function(){
        if($(this).data('id')==userOrGroupId && $(this).data('isgroup')==isGroup){
            isPresent=true;
        }
        //alert($(this).data('id') +'=='+userOrGroupId+'::'+$(this).data('isgroup')+'=='+isGroup)
    });

    if (isPresent==false){


        //update usre info
        $('#permission-search-add-msg').removeClass('alert-danger').addClass('alert-info');
        $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
        $('#permission-search-add-msg .msg').html('Adding user/group ...');


        var url='{{route('cp-source-permission-store')}}';
        var jqxhr = $.post(url,{isgroup:isGroup,
                                data_id:userOrGroupId,
                                source_type:$('#permissions-selector').data('source_type'),
                                source_id:$('#permissions-selector').data('source_id'),
                                '_token': "{{ csrf_token() }}",
                                
            })
            .done(function(data) {
                //now update page
                switch(data.id){
                    case -1:
                        
                        $('#permission-search-add-msg').removeClass('alert-info').addClass('alert-danger');
                        $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-warning-sign');
                        $('#permission-search-add-msg .msg').html('Not permitted or user/group already exists');
                        break;
                    default:
                        $toAdd.val(data.id);
                        jQuery('#permissions-selector').append($toAdd);
                        showPermission();

                        //update info for user
                        $('#permission-search-add-msg').removeClass('alert-danger').addClass('alert-info');
                        $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-warning-sign').addClass('glyphicon-ok');
                        $('#permission-search-add-msg .msg').html('User/Group added successfully');
                }
                                           
            })
            .fail(function(data) {
                var d=data.responseJSON;
                //update user info
                $('#permission-search-add-msg').removeClass('alert-info').addClass('alert-danger');
                $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-warning-sign');
                $('#permission-search-add-msg .msg').html(d.msg);
            })
            .always(function(data) {
                //alert( "finished" );
                //console.log(data);
            })
       
            
    }else{
        
        //update usre info
        $('#permission-search-add-msg').removeClass('alert-info').addClass('alert-danger');
        $('#permission-search-add-msg .glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-warning-sign');
        $('#permission-search-add-msg .msg').html('User/group already added');
    }

}

function removeUserOrGroup(){
    if(!confirm('Are you sure you want to delete this permission:')){
        return;
    }
    url='{{route('cp-source-permission-delete')}}';
    var $option=jQuery('#permissions-selector').find(':selected');
    var id=$option.val();
    var jqxhr = $.post(url,{id:id,
                               '_token': '{{ csrf_token() }}',
                                '_method':'DELETE',
                                
            })
            .done(function(data) {
                //now update page
                if(data.id){
                    $option.remove();
                    showPermission();
                }
                
            })
            .fail(function(data) {
                alert( "Error with removing permission" );
                //console.log(data);
            })
            .always(function(data) {
                //alert( "finished" );
                //console.log(data);
            })
}

</script>
@endpush