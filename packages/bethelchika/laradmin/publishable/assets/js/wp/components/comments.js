// Define a new component for wp comments
Vue.component('laradmin-wp-comments', {
    props:{
      postId:Number,
      initialComments:Array,
      sourceUrl:String,
      initialPageNumber:Number,//set to 0 if unsure
      initialHasMorePages:Boolean,//
      realtimeInterval:Number,//How often to fetch realtime data in milliseconds
      boxClass:String,// Css class for the feeds block. You can specify any and number of classes you want . You can include 'flat-design' to auto remove rounded coners etc
      allowFetchOnScroll:{type:Boolean,default:true},//Must be set to true to allow infinite scroll. The default is true
    },
    data: function () {
      return {
        comments:this.initialComments,
        commentIdCursor:0,//The id of a comment that is the current global focus. All ajax call will set this as parent comment id.

        //TODO:/OR/CAUTION: With the current implementation, the current page number could become incorrect 
        //as new data is added which lead duplicate data being returned during a non-real-time 
        //fetch => However this is not actually a biggy since we have mechanism to prevent 
        //duplicate data being displayed. Although this this could lead to non-real-time fetch 
        //returning all duplicate data which will not be added; which means the fetch 
        //would need to be repeated before a non-duplicate data could be returned.
        currentPageNumber:this.initialPageNumber,
        hasMorePages:this.initialHasMorePages,
        blockMultiAjaxCall:false,// Used to block ajax call when another is in progress
        isRealtimeFetch:false,//set this to true when fetching a latest data
        isLoading:false,//This is set to true when loading data with ajax
        familyTreePosition:0,//The descendant depth
        error:'',
        latestTime:0,
      }
    },
    computed: {
     
    },
    methods:{
      /**
       * Returns the timestamp of the latest comment
       */
      getLatestTime(){
        return this.latestTime;
      },
      
      /**
       * Fetch data on srcoll
       */
      fetchOnScroll(){
        if(this.allowFetchOnScroll){
          $(window).scroll(()=> { 
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 100) {
              this.isRealtimeFetch=false;
              this.fetchNext()
            }
          });
        }
      },
      /**
       * Fetch next data. This function tries to avoid loading duplicates and will not
       * add any of the returned comments if they already exist. 
       */
      fetchNext(){
        if (!this.blockMultiAjaxCall){

          let comment=null;
          if(this.commentIdCursor){
            comment=this.getCommentById(this.commentIdCursor);
          }

          if((this.hasMorePages && !comment)|| (comment && comment.hasMorePages) || this.isRealtimeFetch){
            this.blockMultiAjaxCall=true;
            
            page_number=this.currentPageNumber+1;
            if(comment){
              page_number=comment.currentPageNumber+1;              
            }

            url=this.sourceUrl+'?post_id='+this.postId+'&page='+(page_number+'&parent_id='+this.commentIdCursor);
            if(this.isRealtimeFetch){
              url=this.sourceUrl+'?post_id='+this.postId+'&page=0&latest_timestamp='+this.getLatestTime();//
            }
            this.isLoading=true;
            $.ajax({
              url: url,
            }).done(data => {
              
                data.comments.forEach(data_comment=> {
                  data_comment.key=data_comment.id;
                  this.insertComment(data_comment);
                  
                  // Update the latest time for realtime fetch
                  if(this.isRealtimeFetch || this.latestTime==0){
                    if(this.latestTime<data_comment.timestamp){
                      this.latestTime=data_comment.timestamp;
                    }
                  }
                  
                  
                });
                if(!this.isRealtimeFetch){
                  if(comment){//We are fetching replies for a particular comment
                    comment.currentPageNumber=data.currentPageNumber;
                    comment.hasMorePages=data.hasMorePages;
                    //console.log(comment);
                  }
                  else{//these vars only relate to main top-level comments which do not have parents, i.e parent id =0. 
                    this.currentPageNumber=data.currentPageNumber;
                    this.hasMorePages=data.hasMorePages;
                  }
                }
                this.error='';
            }).fail((jqXHR, textStatus,err) => {
              this.error='Error loading! ('+err+')';
            }).always((jqXHR, textStatus) => {
              this.blockMultiAjaxCall=false;
              this.commentIdCursor=0;
              this.isLoading=false;
            });
          }
        }
        
      },
      /**
       * Checks if a comment has children
       * @param comment{} comment 
       * @return boolean
       */
      hasChildren(comment){
        return !!comment.children.length;
      },
     
      /**
       * Push in a new comment
       * @param Object comment
       */
      insertComment(comment){
        // first avoid duplicates
        comment_prev=this.getCommentById(comment.id);
        if(comment_prev){
          return
        }

        // Now see if it has a parent
        parent=this.getCommentById(comment.parent_id);
        if(parent){
          
          if(this.isRealtimeFetch){
            parent.children.unshift(comment);
          }else{
            parent.children.push(comment);
          }
        }else{
          if(this.isRealtimeFetch){
            this.comments.unshift(comment);
          }else{
            this.comments.push(comment);
          }
        }
         
      },

      

      /**
       * Get a comment with a given id
       * @param int id 
       * @return comment{}|null
       */
      getCommentById(id){
        for (let i=0; i<this.comments.length; i++) {
          if(this.comments[i].id==id){
            return this.comments[i];
          }
          else if(this.hasChildren(this.comments[i])){
            child=this.getCommentByIdIn(this.comments[i].children,id);
            if(child){return child;}
          }          
        }
        return null;
      },

      /**
       * Searches the given comment for a child comment with a given id.
       * @param comments[object Array] comment 
       * @param integer id 
       * @return comment{}|null
       */
      getCommentByIdIn(comments,id){
        for (let i=0; i<comments.length; i++) {
          if(comments[i].id==id){
            return comments[i];
          }else if(this.hasChildren(comments[i])){
            child=this.getCommentByIdIn(comments[i].children,id);
            if(child){return child;}
          }
        }
        return null;
      },

      /**
       * Called on an event of comment creation
       * @param {*} data Event data 
       */
      onCommentCreated(data){
        this.blockMultiAjaxCall=false;
        this.isLoading=false;
        this.realtimeFetch();
      },

      /**
       * Listener function for when comment creation is in progress
       * @param {*} data Event data
       */
      onCommentCreating(data){
        this.blockMultiAjaxCall=true;
        this.isLoading=true;
      },

      /**
       * Listener function to initiate fetching replies for a given comment specified by comment id.
       * @param {*} data Event data
       */
      onFetchReplies(data){
        this.commentIdCursor=data.commentId;
        this.fetchMoreComments();
      },

      /**
       * Fetch the top level comments
       */
      fetchParentComments(){
        this.commentIdCursor=0;
        this.fetchMoreComments();
      },

      /**
       * Fetches new 
       */
      realtimeFetch(){
        this.isRealtimeFetch=true;
        this.fetchNext();
      },
      /**
       * Fetches more older comments
       */
      fetchMoreComments(){
        this.isRealtimeFetch=false;
        this.fetchNext();
      },

  
    },
    mounted(){
      this.fetchOnScroll();
    },
    template: "\
    <div :class='boxClass' class='row laradmin-wp-comments-box'>\
      <div class='col-sm-12'>\
        <div class='comment-wrapper'>\
          <div class=''>\
            <div class=''>\
                Comments\
            </div>\
            <div class=' '>\
              <laradmin-wp-comment-create v-on:comment_created='onCommentCreated($event)' v-on:comment_creating='onCommentCreating($event)' v-bind:source-url='sourceUrl' v-bind:post-id='postId' v-bind:parent-id='0'></laradmin-wp-comment-create>\
              <br>\
              <div class=\"laradmin-wp-comments media-list\">\
                \
                  \
                    <laradmin-wp-comment  v-for='comment in comments'   v-bind:comment='comment' v-bind:family-tree-position='familyTreePosition+1' v-bind:source-url='sourceUrl'  :key='comment.id' v-on:fetch_replies='onFetchReplies($event)' v-on:comment_created='onCommentCreated($event)' v-on:comment_creating='onCommentCreating($event)'>\
                    </laradmin-wp-comment>\
                  \
                \
                <div class='text-center padding-top-x2 padding-bottom-x5 btn-more-comments'>\
                  <span v-show='isLoading' class='is-loading btn btn-primary text-center'>Loading...</span>\
                  <button class='btn btn-primary' v-show='!isLoading && hasMorePages' v-on:click='fetchMoreComments'>More comments <i class='fas fa-angle-double-down'> </i></button>\
                  <button disabled='disabled' class='btn btn-primary' v-show='!isLoading && !hasMorePages' >More comments <i class='fas fa-angle-double-down'> </i></button>\
                </div>\
                <p class='alert alert-danger' v-show='error.length'>\
                  <i class='fas fa-exclamation-triangle'> </i> \
                  {{error}}\
                </p>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div>\
    </div>",
    created: function () {
      this.isRealtimeFetch=false;
      this.fetchNext();
      
      //Initialise fetching data in real time
      setInterval(()=>{
        this.realtimeFetch();
      },this.realtimeInterval);
    }
  });




  //For a comment
  Vue.component('laradmin-wp-comment', {
    props:{
      comment:Object,
      sourceUrl:String,
      familyTreePosition:Number,
    },
    data: function () {
      return {
        showCreateForm:false,
        showReplies:false,
        toggleRepliesText: 'Show replies', 
      }
    },
    computed: {
      /**
       * Return date for display
       */
      date:function(){
          d=new Date(this.comment.timestamp*1000);
          return d.toLocaleDateString()+' '+d.getHours()+':'+d.getMinutes();

      },

      userAvatar:function(){
        if(this.comment.user.avatar){
          return this.comment.user.avatar;
        } 
        return 'https://via.placeholder.com/64x64.png?text='+this.comment.user.username.charAt(0).toUpperCase();
      },
      /**
       * Return css class for the feed box
       */
      boxClass:function(){
        return 'laradmin-wp-comment-'+this.comment.id;
      },
      /**
       * Return the feed box id
       */
      boxId:function(){
        return 'laradmin-wp-comment-'+this.comment.id;
      }
    },
    methods: {
      /**
       * Animate the comment box
       * TODO: make sure this function is not too resource intensive, if it is , disable it
       */
      boxAnimation:function(){
        let ele=$('#'+this.boxId);
        let h0=ele.height();//start height

        ele.height('auto');
        let h=ele.height();//final height

        ele.height(h0);
        ele.height(h);
        ele.css({opacity:1});
        setInterval(()=>{
          ele.height('auto');
          
        },2000);
        

      },

      toggleReplies(){
        this.showReplies=!this.showReplies;
        this.toggleRepliesText ='Show replies';
        if(this.showReplies){
          this.toggleRepliesText='Hide replies'; 
          
          // If load replies of there is none loaded already
          if(this.comment.children.length==0){
            this.fetchReplies();
          }
        }
      },

      /**
       * Initiates fetching of replies for this comment
       */
      fetchReplies(){
        this.$emit('fetch_replies',{commentId:this.comment.id});

      },
      /**
       * Called on an event of comment creation. A comment cannot process the this event and 
       * so will just re-emit it. In case of several replies, i.e multi hierarchy of 
       * comments the event should continue to be re-emitted until it riches the 
       * super components.
       * @param {*} data Event data 
       */
      onCommentCreated(data){
        this.$emit('comment_created',data); 
        this.showCreateForm=false;
      },

      /**
       * Listener function for when comment creation is in progress. A comment cannot process the this event and 
       * so will just re-emit it. In case of several replies, i.e multi hierarchy of 
       * comments the event should continue to be re-emitted until it riches the 
       * super components.
       * @param {*} data Event data
       */
      onCommentCreating(data){
        this.$emit('comment_creating',data); 
      },

      /**
       * The corresponding event is for when a descendant comment wants to fetch its replies.
       * This method helps the descendant to propagates the event to the top.
       * @param {*} data Event data
       */
      onFetchReplies(data){
        this.$emit('fetch_replies',data);
      },

      onHideCreateForm(data){
        this.showCreateForm=false;
      },
    },
    mounted: function () {
      this.boxAnimation();
    },
    template: "\
    <div :class='boxClass' :id='boxId' class='laradmin-wp-comment media' >\
      <a href='#' class='media-left'>\
        <img :src='userAvatar' :alt='comment.user.username' class='img-circle'>\
      </a>\
      <div class='media-body'>\
        <span class='text-muted media-left'>\
            <small class='text-muted date'>{{date}}</small>\
        </span>\
        <strong class='text-reset'>{{comment.user.username}}</strong>\
        <p class='text-content' >\
            <span>{{comment.content}}</span>\
        </p>\
        <button class='reply-btn'  v-show='!showCreateForm && familyTreePosition<=5 ' v-on:click='showCreateForm=true'>Reply</button>\
        <laradmin-wp-comment-create v-if='showCreateForm' v-on:comment_created='onCommentCreated($event)' v-on:comment_creating='onCommentCreating($event)' v-on:hide_create_form='onHideCreateForm($event)' v-bind:source-url='sourceUrl' v-bind:post-id='comment.post_id' v-bind:parent-id='comment.id'></laradmin-wp-comment-create>\
        <template  v-for='child in comment.children'>\
          <laradmin-wp-comment v-show='showReplies' v-bind:family-tree-position='familyTreePosition+1'  v-bind:comment='child' :key='child.id' v-on:fetch_replies='onFetchReplies($event)' v-on:comment_created='onCommentCreated($event)' v-on:comment_creating='onCommentCreating($event)' v-bind:source-url='sourceUrl'>\
          </laradmin-wp-comment>\
        </template>\
        <button class='replies-btn' v-if='comment.hasMorePages || comment.children.length'   v-on:click='toggleReplies()'>{{toggleRepliesText}}</button>\
        <button class='replies-btn' v-if='showReplies && comment.hasMorePages'   v-on:click='fetchReplies()'>Show more replies</button>\
      </div>\
    </div>"
  });

  

  //For creating a comment
  Vue.component('laradmin-wp-comment-create', {
    props:{
      parentId:Number,
      postId:Number,
      sourceUrl:String,
    },
    data: function () {
      return {
        commentContent:'',// The text content of the post
        error:'',
      }
    },
    computed: {
    },
    methods: {

      createComment(){ 
        this.$emit('comment_creating',{});       
        url=this.sourceUrl;
        data_out={
          comment_content:this.commentContent,
          post_id:this.postId,
          parent_id:this.parentId,
          _token:$('meta[name="csrf-token"]').attr('content')
        };

        $.post(url,data_out)
        .done(data => {
            this.commentContent='';
            this.$emit('comment_created', data);
            this.error='';
        }).fail((jqXHR, textStatus,err) => {
          this.error='Error creating comment! ('+err+')';
        }).always((jqXHR, textStatus) => {
          
        });
      },

      hideCreateForm(){
        this.commentContent='';
        this.$emit('hide_create_form',{});
      }
      
    },
    mounted: function () {
    },
    template: "\
    <div class='write-comment' >\
      <textarea class='form-control' v-model='commentContent' cols='35' rows='2' placeholder='Write a comment...'></textarea>\
      <br>\
      <button class='btn btn-subtle pull-left btn-xs' v-on:click='hideCreateForm()'>Cancel</button>\
      <button class='btn btn-info pull-right btn-xs' v-on:click='createComment()'>Comment </button>\
      <div class='clearfix'></div>\
      <p class='alert alert-danger' v-show='error.length'>\
        <i class='fas fa-exclamation-triangle'> </i> \
        {{error}}\
      </p>\
    </div>"
  });

 

