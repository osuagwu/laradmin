
  Dropzone.options.file={ 
    maxFiles:50,
    addRemoveLinks:true,
    init: function() {
        this.on("success", function(file,response) { 
            console.log(response);
            window.location.replace(response.redirectTo);
        });

        this.on("error", function(file,response) { 
            console.log(response.error);
            alert(response.error)
        });
    }
  };