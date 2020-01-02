@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')

<section role="banner" class="section section-default  section-title "  >
        <div class="section-overlay">
            <div class="container">
                <h1 class="text-center">{{$pageTitle}}</h1>
                <a class="strong" href="{{route('user-profile')}}">&leftarrow; Done</a>
                <div class="">
                    <div class=" ">
                        
                        <div class="text-center">
                            <div class="">
                                
                            </div>
                            <div class="">
                                
                                <laradmin-avatar 
                                    source-url="{{route('user-avatar-json')}}"
                                    upload-url="{{route('user-avatar-json')}}"
                                    remove-url="{{route('user-avatar-json')}}"
                                    v-bind:image-width="{{config('laradmin.avatar.width')}}"
                                    v-bind:image-height="{{config('laradmin.avatar.height')}}"
                                >
                                </laradmin-avatar>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@push('head-styles')
<style>
.user-avatar-editor .edit-screen,
.user-avatar-editor .add-screen {
    min-height:{{config('laradmin.avatar.height')}}px;
    max-height:{{config('laradmin.avatar.height')}}px;
    min-width:{{config('laradmin.avatar.width')}}px;
    max-width:{{config('laradmin.avatar.width')}}px;
    display:inline-block;
    border:3px dashed #ddd;
    margin-bottom:8px;
    background-color:#eee;
    background-image:url(https://via.placeholder.com/{{config('laradmin.avatar.width')}}x{{config('laradmin.avatar.height')}});
}

.user-avatar-editor .edit-screen img.preview-thumb,
.user-avatar-editor .add-screen .preview-box img.user-avatar-add-img{
    
    width:100%;
    max-width:100%;
    min-width:100%;
    
}
.user-avatar-editor img.placeholder{
    cursor:pointer;
    
}


</style>
@endpush

@push('footer-scripts-after-library')
<script>
    Vue.component('laradmin-avatar', {
    props:{
      sourceUrl:String,
      uploadUrl:String,
      removeUrl:String,
      imageWidth:Number,
      imageHeight:Number,
    },
    data: function () {
      return {
        image:null,
        showAddScreen:false,
      }
    },
    computed: {
        imagePlaceholder:function(){
            return 'https://via.placeholder.com/'+this.imageWidth+'x'+this.imageHeight;
        }
    },
    methods: {
        refresh(){
            $.get(this.sourceUrl)
            .done((data)=>{ 
                if(!$.isEmptyObject(data)){
                    this.image=data;
                }                   
                
            })
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

      
    },
    mounted: function () {
        this.refresh();
    },
    template: '\
    <div class="user-avatar-editor">\
        \
        <div v-if="!showAddScreen" class="edit-screen">\
            <div class="preview-box">\
                <img v-if="!image" v-on:click="selectImage()"  v-bind:src="imagePlaceholder"  class="preview-thumb placeholder">\
                <img v-if="image"  v-bind:src="image.url"  class="preview-thumb">\
            </div>\
        </div>\
        <laradmin-avatar-add v-if="showAddScreen" v-on:image-added="onImageAdded($event)" v-on:image-add-cancel="onImageAddCancel($event)" v-bind:source-url="sourceUrl" v-bind:upload-url="uploadUrl" v-bind:image-width="imageWidth" v-bind:image-height="imageHeight"></laradmin-avatar-add>\
        <div>\
            <button v-if="!showAddScreen" class="btn btn-primary btn-sm" v-on:click="selectImage()"><i class="fas fa-plus"> </i>  Replace </button>\
            <button v-if="!showAddScreen && image"  class="btn btn-danger btn-sm" v-on:click="deleteImage()"><i class="fas fa-minus"> </i>  Delete </button>\
        </div>\
        \
    </div>',

  });

//Upload and crop profile avatar
  Vue.component('laradmin-avatar-add', {
    props:{
      sourceUrl:String,
      uploadUrl:String,
      imageWidth:Number,
      imageHeight:Number,
    },
    data: function () {
      return {
        imageFile:null,
        error:'',
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
       
        upload(){
            let fd= new FormData();
            fd.enctype="multipart/form-data";

            let i=0;
            fd.append('file', this.imageFile);
            fd.append('_token',$('meta[name="csrf-token"]').attr('content'));
            
           
            var oReq = new XMLHttpRequest();
            oReq.open("POST", this.uploadUrl, true);
            oReq.onload = (oEvent) => {
                if (oReq.status == 200) {
                    this.uploaded();
                    //alert("Uploaded!");
                } else {
                    alert("Error " + oReq.status + " occurred when trying to upload your file.");
                    
                }
            };

            oReq.send(fd);
        },

        uploaded(){
            this.imageFile=null;
            this.$emit('image-added',{});
        },
        cancel(){
            this.imageFile=null;
            this.$emit('image-add-cancel',{});
        },   
      
    },
    watch:{
        imageFile:function(){
            const $img = $('#user-avatar-add-img-id');

            $img.attr('src', URL.createObjectURL(this.imageFile));
        }
    },
    mounted: function () {
        this.selectImage();
    },
    template: '\
    <div class="add-screen-box" >\
        <div  class="add-screen">\
            <div class="preview-box">\
                <img v-if="!imageFile" v-on:click="selectImage()"  v-bind:src="imagePlaceholder"   class="user-avatar-add-img placeholder">\
                <img v-show="imageFile" class="user-avatar-add-img" id="user-avatar-add-img-id">\
            </div>\
        </div>\
        <div>\
            <input id="file-input" @change="onFileChange" type="file" name="name" style="display: none;" />\
            <button class="btn btn-primary btn-sm"  v-on:click="selectImage()"><i class="fas fa-plus"> </i>  Browse </button>\
            <button class="btn btn-primary btn-sm" v-on:click="upload()" :disabled="imageFile?false:true"><i class="fas fa-save"> </i>  Try this one </button>\
            <button class="btn btn-primary btn-sm" v-on:click="cancel()"><i class="fas fa-times"> </i>  Cancel </button>\
        </div>\
    </div>',
  });
</script>
@endpush



@endsection
