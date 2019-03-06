// Define a new component for feeds
Vue.component('laradmin-feeds', {
    props:{
      initialFeeds:Array,
      sourceUrl:String,
      initialPageNumber:Number,//set to 0 if unsure
      initialHasMorePages:Boolean,//
      realtimeInterval:Number,//How often to fetch realtime data in milliseconds
      boxClass:String,// Css class for the feeds block. You can specify any and number of classes you want . You can include 'flat-design' to auto remove rounded coners etc
      allowFetchOnScroll:{type:Boolean,default:true},//Must be set to true to allow infinite scroll. The default is true
    },
    data: function () {
      return {
        feeds:this.initialFeeds,
        currentPageNumber:this.initialPageNumber,
        hasMorePages:this.initialHasMorePages,
        blockMultiAjaxCall:false,
        isRealtimeFetch:false,//set this to true when fetching a latest data
        isLoading:false,//This is set to true when loading data with ajax
        
        error:'',
      }
    },
    computed: {
     
    },
    methods:{
      /**
       * Returns the timestamp of the latest static feed
       */
      getLatestTime(){
        for(let i=this.feeds.length-1;i>=0;i--){
          if(!this.feeds[i].isDynamic){
            return this.feeds[i].createdAt.timestamp;
          }
        }
        return 0;
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
       * Fetch next data. This function tries to avoide loading duplicates and will not
       * add any of the returned feeds if they already exist. When new data is being 
       * added to the feed database, this can cuause shift in offset of pagination 
       * leading to possible return of dupplicate data which will be ignored by 
       * this function. So it may wrongly acts as if feeds has all finished.
       */
      fetchNext(){
        if (!this.blockMultiAjaxCall){
          if(this.hasMorePages || this.isRealtimeFetch){
            this.blockMultiAjaxCall=true;
            url=this.sourceUrl+'?page='+(this.currentPageNumber+1);
            if(this.isRealtimeFetch){
              url=this.sourceUrl+'?page=0&latest_timestamp='+this.getLatestTime()//
            }
            this.isLoading=true;
            $.ajax({
              url: url,
            }).done(data => {
                data.feeds.forEach(feed=> {
                  feed.key=feed.id +'-'+Date.now();//This is to make sure the key is unique in case for a any reason dupplicate occures
                  //lets go further a avoid duplicate at all
                  isDuplicate=false;
                  for (let i=0; i<this.feeds.length; i++) {
                    if(this.feeds[i].id==feed.id){//make sure the new feed has not been previously loaded
                      isDuplicate=true;
                      break;
                    }
                  }
                  if(!isDuplicate){
                    if(this.isRealtimeFetch){
                      this.feeds.unshift(feed);
                    }else{
                      this.feeds.push(feed);
                    }
                  }
                });
                if(!this.isRealtimeFetch){
                  this.currentPageNumber=data.currentPage;
                  this.hasMorePages=data.hasMorePages;
                }
                this.error='';
            }).fail((jqXHR, textStatus,err) => {
              this.error='Error loading feeds! ('+err+')';
            }).always((jqXHR, textStatus) => {
              this.blockMultiAjaxCall=false;
              this.isLoading=false;
            });
          }
        }
        
      },
      /**
       * Fetches new feeds
       */
      realtimeFetch(){
        this.isRealtimeFetch=true;
        this.fetchNext();
      },
      /**
       * Fetches more older feeds
       */
      fetchMoreFeeds(){
        this.isRealtimeFetch=false;
        this.fetchNext();
      }
    },
    mounted(){
      this.fetchOnScroll();
    },
    template: "<div :class='boxClass'>\
      <div class=\"laradmin-feeds\">\
        <laradmin-feed v-for='feed in feeds'  v-bind:feed='feed' :key='feed.key' >\
        </laradmin-feed>\
        <p class='text-center'>\
          <span v-show='isLoading' class='is-loading btn btn-primary text-center'>Loading...</span>\
          <button class='btn btn-primary' v-show='!isLoading && hasMorePages' v-on:click='fetchMoreFeeds'>More feeds <i class='fas fa-angle-double-down'> </i></button>\
          <button disabled='disabled' class='btn btn-primary' v-show='!isLoading && !hasMorePages' >More feeds <i class='fas fa-angle-double-down'> </i></button>\
        </p>\
        <p class='alert alert-danger' v-show='error.length'>\
          <i class='fas fa-exclamation-triangle'> </i> \
          {{error}}\
        </p>\
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
  })

  //For feed
  Vue.component('laradmin-feed', {
    props:{
      feed:Object,
    },
    data: function () {
      return {
        showSharePanel:false,
        hasParsedFacebookShare:false,//This is set to true after parsing facebook share
        hasParsedTwitterShare:false,//This is set to true after parsing twitt widgit  for this feed
      }
    },
    computed: {
      /**
       * Return date for display
       */
      date:function(){
        if(this.feed.isDynamic){
          return '';
        }
        if(this.feed.createdAt.timestamp){
          d=new Date(this.feed.createdAt.timestamp*1000);
          return d.toLocaleDateString()+' '+d.getHours()+':'+d.getMinutes();
        }else{
          return '';
        }
      },
      /**
       * Return css class for the feed box
       */
      boxClass:function(){
        return 'laradmin-feed '+this.feed.cssClass+' '+this.feed.typeString;
      },
      /**
       * Return the feed box id
       */
      boxId:function(){
        return 'laradmin-feed-'+this.feed.key;
      }
    },
    methods: {
      /**
       * Hides the share panel
       */
      offSharePanel: function(value) {
        this.showSharePanel=false;
      },
      /**
       * Show the srae panel
       */
      onSharePanel: function(value) {
        this.parseFacebookShare();
        this.parseTwitterShare();
        this.showSharePanel=true;
      },

      /**
       * Parse facebook share
       */
      parseFacebookShare:function(){
        if(!this.hasParsedFacebookShare){
          if(typeof FB==='undefined'){
            this.hasParsedFacebookShare=false;
          }else{
            FB.XFBML.parse(document.getElementById(this.boxId),r=>{
              this.hasParsedFacebookShare=true;
            });
            
          }
        }
      },
      /**
       * Parse twitter share
       */
      parseTwitterShare:function(){
        if(!this.hasParsedTwitterShare && this.feed.shareUrl){
          if(typeof twttr==='undefined'){
            this.hasParsedTwitterShare=false;
          }else{
            let ele=$('#'+this.boxId+' .twitter-share')[0];
            if(ele){
              twttr.widgets.createShareButton(
                this.feed.shareUrl,
                ele,
                {
                  count: 'none',
                  text: this.feed.title,
                  hashtags:this.feed.twitterHashtags,
                  related:this.feed.twitterScreenNames,
                  via:this.feed.twitterVia,
                  lang:this.feed.lang,
                  align:'right',
                }).then(el=> {
                  this.hasParsedTwitterShare=true;
                });
            }
          }
        }
      },
      /**
       * Animate the feed box
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
        

      }
    },
    mounted: function () {
      this.parseFacebookShare();
      this.parseTwitterShare();
      this.boxAnimation();
    },
    template: '\
    <div :class="boxClass" :id="boxId" v-on:mouseout="offSharePanel" v-on:mouseover="onSharePanel">\
      <div class="top">\
        <div class="source-icon pull-left">\
          <img v-if="feed.sourceIconType===\'image\'" :alt="feed.sourceName" :src="feed.sourceIcon" />\
          <span v-else-if="feed.sourceIconType===\'html\'" v-html="feed.sourceIcon"></span>\
          <span v-else><i class="far fa-meh-blank"></i></span>\
        </div>\
        <div class="source-info">\
          <a :href="feed.sourceUrl" class="source-name">{{feed.sourceName}}</a>\
          <br />\
          <span v-if="!feed.isDynamic" class="date">{{date}}</span>\
          <span v-else class="dynamic-feed-identifier">Dynamic feed</span>\
        </div>\
      </div>\
      <h4 class="title"><a :href="feed.url"> {{feed.title}} </a></h4>\
      <div class="before-html" v-if="feed.beforeHtml" v-html="feed.beforeHtml"></div>\
      <div class="summary" v-if="feed.summary">{{feed.summary}}</div>\
      <div class="content" v-else>{{feed.content}}</div>\
      <div class="image" v-if="feed.image"> <a :href="feed.url"> <img :alt="feed.title" :src="feed.image" /> </a>  </div>\
      <div class="bottom" >\
          <div class="share-btn">\
            <button class="btn btn-primary btn-xs" v-if="((showSharePanel==false) && !feed.isDynamic)" v-on:click="showSharePanel=true">Share <i class="fas fa-share-alt"></i></button>\
          </div>\
        <laradmin-feed-share-panel v-if="feed.shareUrl" v-show="showSharePanel"  :url="feed.shareUrl"></laradmin-feed-share-panel>\
        <div class="after-html" v-if="feed.afterHtml" v-html="feed.afterHtml"></div>\
      </div>\
    </div>'
  })

  //For share panel
  Vue.component('laradmin-feed-share-panel', {
    props:{
      url:String,
    },
    data: function () {
      return {
        
      }
    },
    template:'\
      <div class="share-box row clearfix">\
        <div class=" col-xs-4 twitter-share">\
        </div>\
        <div class="col-xs-8 facebook-share">\
          <div class=" fb-like " :data-href="url" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>\
        </div>\
      </div>'
  })
  