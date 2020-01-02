//Globals

var BS_VIEWPORT_SIZE_NAMES = ['xs', 'sm', 'md', 'lg'];
/**
 * Detect and return Bootstrap 3 viewport environment
 * Author: https://stackoverflow.com/questions/14441456/how-to-detect-which-device-view-youre-on-using-twitter-bootstrap-api
 */
function findBootstrapEnvironment() {
    var envs = BS_VIEWPORT_SIZE_NAMES;

    var $el = $('<div>');
    $el.appendTo($('body'));

    for (var i = envs.length - 1; i >= 0; i--) {
        var env = envs[i];

        $el.addClass('hidden-'+env);
        if ($el.is(':hidden')) {
        
            $el.remove();
            return env;
        }
    }
}

/**
 * Checks if viewport has changed and returns the old and the new one in an array [old,new]. It returns false if viewport has not changed
 */
function viewPortChanged(){
    var vp=findBootstrapEnvironment();
    var vp_old=sessionStorage.getItem('bs_viewport_state');
    
    if(vp===vp_old){
        return false;    
    }else{
        sessionStorage.setItem('bs_viewport_state', vp); 
        return [vp_old,vp];
    }
}

/**
 * Checks if viewport has switched
 * The viewport is defined as switched when it moves from [<= sm to >= md] boostrape 3 sizes
 * @return mixed {0=>not switch; 1=>if vp switch from big to small; 2=>if vp switch from small to big;}
 */
function viewPortSwitched(){
    var envs = BS_VIEWPORT_SIZE_NAMES;
    var vps=viewPortChanged();
    var vp=vps[1];
    var vp_old=vps[0];
    
    
    var ch=0;
    var i;
    var vp_idx=0;
    var vp_old_idx=0;

    //Converts the vp and vp_old to their dear index in the array of envs
    for (i = 0; i < envs.length; ++i) {
        if(vp===envs[i]){
            vp_idx=i;
        }
        if(vp_old===envs[i]){
            vp_old_idx=i;
        }
    }
    
    
    // Find direction of change
    if(vp_old_idx>=2 && vp_idx<=1){// from big to small
        ch=1;
    }else if(vp_old_idx<=1 && vp_idx>=2){//from small to big
        ch=2;
    }
    
    return ch;
}


/**
 * break bootstrape sizes into two and return which group the current size is in
 * @return int {0=>failed; 1=>smaller screen; 2=>bigger screen} 
 */
function viewportSizeGroup(){
    var envs = BS_VIEWPORT_SIZE_NAMES;
    var vp=findBootstrapEnvironment();
    var g=0;
    if(vp===envs[0] || vp===envs[1]){
        g=1; //smaller
    }else if(vp===envs[2] || vp===envs[3]){
        g=2; //bigger
    }
    return g;
}







// //Vue will have the object Ajaxifier as one of its data. each time vue makes an ajax call, the backend must send data that with corresponding fields to Vue fileds 

// class Ajaxifier{
//     /**
//      * Create a new Errors instance.
//      */
//     constructor() {
//         this.errors = {};
//     }


//     /**
//      * Write data to html
//      *
//      * @param {string} selector jquery compactible selector including '#' and '.'
//      * @param {mixed} data
//      * @param {string} template Mustash compatible template
//      */
//     toHtml(selector,data,template) {
//         $(selector).html(Mustash.render($template,data));
//     }
// }


// class Errors {
//     /**
//      * Create a new Errors instance.
//      */
//     constructor() {
//         this.errors = {};
//     }


//     /**
//      * Determine if an errors exists for the given field.
//      *
//      * @param {string} field
//      */
//     has(field) {
//         return this.errors.hasOwnProperty(field);
//     }


//     /**
//      * Determine if we have any errors.
//      */
//     any() {
//         return Object.keys(this.errors).length > 0;
//     }


//     /**
//      * Retrieve the error message for a field.
//      *
//      * @param {string} field
//      */
//     get(field) {
//         if (this.errors[field]) {
//             return this.errors[field][0];
//         }
//     }


//     /**
//      * Record the new errors.
//      *
//      * @param {object} errors
//      */
//     record(errors) {
//         this.errors = errors;
//     }


//     /**
//      * Clear one or all error fields.
//      *
//      * @param {string|null} field
//      */
//     clear(field) {
//         if (field) {
//             delete this.errors[field];

//             return;
//         }

//         this.errors = {};
//     }
// }


// class Form {
//     /**
//      * Create a new Form instance.
//      *
//      * @param {object} data
//      */
//     constructor(data) {
//         this.originalData = data;

//         for (let field in data) {
//             this[field] = data[field];
//         }

//         this.errors = new Errors();
//     }


//     /**
//      * Fetch all relevant data for the form.
//      */
//     data() {
//         let data = {};

//         for (let property in this.originalData) {
//             data[property] = this[property];
//         }

//         return data;
//     }


//     /**
//      * Reset the form fields.
//      */
//     reset() {
//         for (let field in this.originalData) {
//             this[field] = '';
//         }

//         this.errors.clear();
//     }

//     /**
//      * Send a POST request to the given URL.
//      * .
//      * @param {string} url
//      */
//     get(url) {
//         return this.submit('get', url);
//     }


//     /**
//      * Send a POST request to the given URL.
//      * .
//      * @param {string} url
//      */
//     post(url) {
//         return this.submit('post', url);
//     }

    


//     /**
//      * Send a PUT request to the given URL.
//      * .
//      * @param {string} url
//      */
//     put(url) {
//         return this.submit('put', url);
//     }


//     /**
//      * Send a PATCH request to the given URL.
//      * .
//      * @param {string} url
//      */
//     patch(url) {
//         return this.submit('patch', url);
//     }


//     /**
//      * Send a DELETE request to the given URL.
//      * .
//      * @param {string} url
//      */
//     delete(url) {
//         return this.submit('delete', url);
//     }


//     /**
//      * Submit the form.
//      *
//      * @param {string} requestType
//      * @param {string} url
//      */
//     submit(requestType, url) {
//         return $.ajax({
//             url:url,
//             data:this.data(),
//             dataType:'json',
//             type: requestType
//         })
//     }


//     /**
//      * Handle a successful form submission.
//      *
//      * @param {object} data
//      */
//     onSuccess(data) {
//         alert(data.message); // temporary

//         this.reset();
//     }


//     /**
//      * Handle a failed form submission.
//      *
//      * @param {object} errors
//      */
//     onFail(errors) {
//         this.errors.record(errors);
//     }
// }
