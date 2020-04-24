{{-- Single image upload. 
    This view uses CropperJs and will includes it through cdn.

[INPUTS]
$recommended_height int The recommended image height.  Default=300.
$recommended_width int The recommended image width.  Default=400.

Usage Example:
@include('laradmin::user.partials.image.upload_single')
<laradmin-image-upload-single
    source-url="{{route('...')}}"
    upload-url="{{route('...')}}"
    update-url="{{route('...')}}"
    remove-url="{{route('...')}}"
    v-bind:image-width="400"
    v-bind:image-height="300"
>
</laradmin-image-upload-single>

--}}

{{--Push cropper stuff--}}
@push('head-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.css" integrity="sha256-cZDeXQ7c9XipzTtDgc7DML5txS3AkSj0sjGvWcdhfns=" crossorigin="anonymous" />
@endpush
@push('footer-scripts-library')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.js" integrity="sha256-EuV9YMxdV2Es4m9Q11L6t42ajVDj1x+6NZH4U1F+Jvw=" crossorigin="anonymous"></script>
@endpush
{{--end cropper push--}}
@push('head-styles')
<style>
.upload-editor .edit-screen,
.upload-editor .add-screen {
    /*min-height:{{$recommended_height??300}}px;*/
    max-height:{{$recommended_height??300}}px;
    /*min-width:{{$recommended_width??400}}px;*/
    max-width:{{$recommended_width??400}}px;
    display:inline-block;
    border:3px dashed #ddd;
    margin-bottom:8px;
    background-color:#eee;
    background-image:url(https://via.placeholder.com/{{$recommended_height??300}}x{{$recommended_width??400}});
}

.upload-editor .edit-screen img.preview-thumb,
.upload-editor .add-screen .preview-box img.upload-add-img{
    
    width:100%;
    max-width:100%;/*Am inportant rule for cropper*/
    min-width:100%;
    
}
.upload-editor img.placeholder{
    cursor:pointer;
    
}


</style>
@endpush

@push('footer-scripts-after-library')
<script>
    Vue.component('laradmin-image-upload-single', {
    props:{
      sourceUrl:String,
      uploadUrl:String,
      updateUrl:String,
      removeUrl:String,
      imageWidth:Number,
      imageHeight:Number,
    },
    data: function () {
      return {
        image:null,
        showAddScreen:false,
        showErrorDlg:false,
        isCropping:false,
        cropper:null,
        imageGeometry:{
            x:0,
            y:0,
            width:0,
            height:0,
            rotate:0,
            scaleX:1,
            scaleY:1,
        },
        errors:{},
      }
    },
    computed: {
        imagePlaceholder:function(){
            return 'https://via.placeholder.com/'+this.imageWidth+'x'+this.imageHeight;
        }
    },
    methods: {
        /*
        * Refresh image from server
        */
        refresh(){
            $.get(this.sourceUrl)
            .done((data)=>{ 
                if(!$.isEmptyObject(data)){
                    this.image=data;
                    this.image.url=this.image.url+'?'+Date.now();// Make sure the image is not catched                   
                } 
                this.stopCrop();                                
                this.imageGeometry={};
            });
            
        },
        selectImage(){
            this.showAddScreen=true;
        },
        onImageAdded(data){
            this.showAddScreen=false;
            this.refresh();
        },
        onImageAddCancel(data){
            this.showAddScreen=false;
        },
        deleteImage(){
            let outData={
                image:this.image.media_id,
                '_token':$('meta[name="csrf-token"]').attr('content'),
                '_method':'delete'

            };

            $.post(this.removeUrl,outData)
            .done((data)=>{
                this.image=null;
                
            });
        },
        updateImage(){
            let outData={
                image:this.image.media_id,
                '_token':$('meta[name="csrf-token"]').attr('content'),
                '_method':'put',
                'image_geometry_x':this.imageGeometry.x,
                'image_geometry_y':this.imageGeometry.y,
                'image_geometry_width':this.imageGeometry.width,
                'image_geometry_height':this.imageGeometry.height,
                'image_geometry_rotate':this.imageGeometry.rotate,
                'image_geometry_scale_x':this.imageGeometry.scaleX,
                'image_geometry_scale_y':this.imageGeometry.scaleY,

            };

            $.post(this.updateUrl,outData)
            .done((data)=>{
                this.image=null;
                this.refresh();
            });
        },
        cancelUpdateImage(){
            this.stopCrop();
        },
        initCrop(){
            const image = document.getElementById('image-preview');
            this.cropper = new Cropper(image, {
            aspectRatio: this.imageWidth / this.imageHeight,
            crop:(event)=> {
                this.imageGeometry.x=event.detail.x;
                this.imageGeometry.y=event.detail.y;
                this.imageGeometry.width=event.detail.width;
                this.imageGeometry.height=event.detail.height;
                this.imageGeometry.rotate=event.detail.rotate;
                this.imageGeometry.scaleX=event.detail.scaleX;
                this.imageGeometry.scaleY=event.detail.scaleY;
            },
            });
            this.isCropping=true;
        },
        stopCrop(){
            if(this.cropper){
                this.cropper.destroy();
            }
            this.isCropping=false; 
        },
        /**
        * @param Object errors  
        */
        showErrors(errors){
            $.each(errors,(key,val)=>{
                
                if(!this.errors.hasOwnProperty(key)){
                    this.errors[key]=[];
                }
                this.errors[key]=this.errors[key].concat(val)
            });
            this.showErrorDlg=true;
            //console.log(errors);
        },
        clearErrors(){
            this.errors={};
            this.showErrorDlg=false;

        },      
    },
    mounted: function () {
        this.refresh();        
    },
    template: '\
    <div class="upload-editor">\
        \
        <div v-if="!showAddScreen" class="edit-screen">\
            <div class="preview-box">\
                <img v-if="!image" v-on:click="selectImage()"  v-bind:src="imagePlaceholder"  class="preview-thumb placeholder">\
                <img id="image-preview" v-if="image"  v-bind:src="image.url"  class="preview-thumb">\
            </div>\
        </div>\
        <laradmin-image-upload-single-add v-if="showAddScreen" v-on:image-added="onImageAdded($event)" v-on:image-add-cancel="onImageAddCancel($event)" v-on:has-error="showErrors($event)" v-bind:source-url="sourceUrl" v-bind:upload-url="uploadUrl"  v-bind:image-width="imageWidth" v-bind:image-height="imageHeight"></laradmin-image-upload-single-add>\
        <br>\
        <div class="btn-group btn-group-sm ">\
            <a v-if="!showAddScreen && !isCropping" class="btn btn-success btn-sm" href="{{route('user-profile')}}"><i class="fas fa-thumbs-up"></i> Done</a>\
            <button v-if="!showAddScreen && !isCropping" class="btn btn-default btn-sm" v-on:click="selectImage()"><i class="fas fa-plus"> </i>  Replace </button>\
            <button id="image-crop-init" v-if="!showAddScreen && image && !isCropping" class="btn btn-default btn-sm" v-on:click="initCrop()"   ><i class="fas fa-crop"></i> Crop</button>\
            <button v-if="!showAddScreen && image && !isCropping"  class="btn btn-danger btn-sm" v-on:click="deleteImage()"><i class="fas fa-thumbs-down"> </i>  Delete </button>\
            \
            <template v-if="isCropping">\
                <button  class="btn btn-success btn-sm" v-on:click="updateImage()"   ><i class="fas fa-save"></i> Apply changes</button>\
                <button  class="btn btn-danger btn-sm" v-on:click="cancelUpdateImage()"   > <i class="fas fa-times"></i> Cancel</button>\
            </template>\
        </div>\
        <br><br>\
        <p  v-if="isCropping" class="alert alert-info" style="display:inline-block"><i class="fas fa-info-circle"></i> You can use your mouse wheel to resize.</p>\
        <br><br>\
        <!-- Start error dlg-->\
        <div style="">\
            <div style="" v-if="showErrorDlg"  class="alert alert-danger alert-dismissible " role="alert">\
            <div class="text-left">\
                <a href="#" class="close" data-dismiss="alert" aria-label="close" v-on:click="clearErrors()">&times;</a>\
                <strong> <i class="fas fa-exclamation-triangle"></i> Error</strong> \
            </div>\
                <template v-for="error in errors">\
                    <ul class="text-left">\
                        <li v-for="msg in error">\
                            @verbatim{{ msg }}@endverbatim\
                        </li>\
                    </ul>\
                </template>\
            </div>\
        </div>\
        <!-- End error dlg -->\
    </div>',

  });

//Upload and crop 
  Vue.component('laradmin-image-upload-single-add', {
    props:{
      sourceUrl:String,
      uploadUrl:String,
      updateUrl:String,
      imageWidth:Number,
      imageHeight:Number,
    },
    data: function () {
      return {
        imageFile:null,
        imageGeometry:{
            x:0,
            y:0,
            width:0,
            height:0,
            rotate:0,
            scaleX:1,
            scaleY:1,
        },
        percentCompleted:0,
        showProgressBar:false,
    
      }
    },
    computed: {
        imagePlaceholder:function(){
            return 'https://via.placeholder.com/'+this.imageWidth+'x'+this.imageHeight;
        }
    },
    methods: {
        selectImage(){
            let input_image=document.getElementById('file-input');
            input_image.click();   
        },
        onFileChange(e) {
            const file = e.target.files;
            this.imageFile=file[0]; 
        },
        updateProgressBar(ev) {
            if (ev.lengthComputable) {
                var temp=(ev.loaded / ev.total) * 100;
                temp=temp-5;//A trick to keep the bar incomplete until it vanishes
                if(temp<0){
                    temp=0;
                }
                    
                this.percentCompleted=temp;
            }

        },
        upload(){
            let fd= new FormData();
            fd.enctype="multipart/form-data";

            let i=0;
            fd.append('file', this.imageFile);
            fd.append('_token',$('meta[name="csrf-token"]').attr('content'));
            fd.append('image_geometry_x',this.imageGeometry.x);
            fd.append('image_geometry_y',this.imageGeometry.y);
            fd.append('image_geometry_width',this.imageGeometry.width);
            fd.append('image_geometry_height',this.imageGeometry.height);
            fd.append('image_geometry_rotate',this.imageGeometry.rotate);
            fd.append('image_geometry_scale_x',this.imageGeometry.scaleX);
            fd.append('image_geometry_scale_y',this.imageGeometry.scaleY);
            
           
            var oReq = new XMLHttpRequest();
            oReq.open("POST", this.uploadUrl, true);
            oReq.onload = (oEvent) => {
                if (oEvent.target.status == 200) {
                    this.showProgressBar=false;
                    this.uploaded();
                    
                } 
                else if(oEvent.target.status == 422){//validation error
                    var data=JSON.parse(oEvent.target.responseText);
                    this.showErrors(data.errors);
                }
                else if(oEvent.target.status == 420){//re-auth error
                    window.location.reload();
                }
                else {
                    var error={"auth" : "Error " + oEvent.target.status + " occurred when trying to upload.",};
                    this.showErrors(error);
                }
            };

            this.showProgressBar=true;
            
            //oReq.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            oReq.setRequestHeader('Accept', 'application/json'); 
            oReq.send(fd);
            
        },
        showErrors(errors){
            this.$emit('has-error',errors)
        },
        uploaded(){
            this.imageFile=null;
            this.$emit('image-added',{});
        },
        cancel(){
            this.imageFile=null;
            this.$emit('image-add-cancel',{});
        },  
        
        initCrop(){
            const image = document.getElementById('upload-add-img-id');
            const cropper = new Cropper(image, {
            aspectRatio: this.imageWidth / this.imageHeight,
            crop:(event)=> {
                
                this.imageGeometry.x=event.detail.x;
                this.imageGeometry.y=event.detail.y;
                this.imageGeometry.width=event.detail.width;
                this.imageGeometry.height=event.detail.height;
                this.imageGeometry.rotate=event.detail.rotate;
                this.imageGeometry.scaleX=event.detail.scaleX;
                this.imageGeometry.scaleY=event.detail.scaleY;
                
            },
            });
        },
      
    },
    watch:{
        imageFile:function(){
            const $img = $('#upload-add-img-id');

            $img.attr('src', URL.createObjectURL(this.imageFile));

            this.initCrop();//CAUTION: it may be an issue it the image above does not load quick enough. If thats the case then we should provide a button for the user to init cropping.
        }
    },
    mounted: function () {
        this.selectImage();
    },
    template: '\
    <div class="add-screen-box" >\
        <div  class="add-screen">\
            <div class="preview-box">\
                <img v-if="!imageFile" v-on:click="selectImage()"  v-bind:src="imagePlaceholder"   class="upload-add-img placeholder">\
                <img v-show="imageFile" class="upload-add-img" id="upload-add-img-id">\
            </div>\
        </div>\
        <div>\
            <input id="file-input" @change="onFileChange" type="file" name="name" style="display: none;" />\
            <div v-if="showProgressBar">\
                <br>\
                <div class="progress ">\
                    <div  class="progress-bar progress-bar-striped active progress-bar-animated" role="progressbar" v-bind:aria-valuenow="percentCompleted" aria-valuemin="0" aria-valuemax="100" v-bind:style="{width: percentCompleted + \'%\' }">@{{ parseInt(percentCompleted)}}%</div>\
                </div>\
            </div>\
            <div class="btn-group btn-group-sm ">\
                <button class="btn btn-default btn-sm"  v-on:click="selectImage()"><i class="fas fa-plus"> </i>  Browse </button>\
                <button class="btn btn-success btn-sm" v-on:click="upload()" :disabled="imageFile?false:true"><i class="fas fa-save"> </i>  Save </button>\
                <button class="btn btn-danger btn-sm" v-on:click="cancel()"><i class="fas fa-times"> </i>  Cancel </button>\
                </div>\
        </div>\
    </div>',
  });
</script>
@endpush

