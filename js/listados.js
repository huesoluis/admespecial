  $(".continua").click(function () {   
       if($(this).hasClass("btn-warning"))
       {
       		$(this).addClass("btn-danger");
       		$(this).removeClass("btn-warning");
       }
       else{
       		$(this).addClass("btn-warning");
       		$(this).removeClass("btn-danger");
       }
       $(this).text(function(i, v){
		   return v === 'PUSH ME' ? 'PULL ME' : 'PUSH ME'
		});
    });

    $(".pushme2").click(function(){
		$(this).text(function(i, v){
		   return v === 'PUSH ME' ? 'PULL ME' : 'PUSH ME'
		});
    });


