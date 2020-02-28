$( document ).ready(function () {
        /*Make search*/
        $("[data-type='accordion-search']").keyup(function () {
            console.log('dentro de search');
            var expression = false;
            var value = $(this).val();
            
            if(value.length<=2){
	    
            $(".dencuali").each(function () {
	    
	    			$(this).show();
	    
	    			});
	    			}
            if(value.length<3) return;

	    var finder = "";
	    if (value.indexOf("\"") > -1 && value.lastIndexOf("\"") > 0) {
                finder = value.substring(eval(value.indexOf("\"")) + 1, value.lastIndexOf("\""));
                expression = true;
            }
            $(".dencuali").each(function () {
                var title = $(this).find("[data-type='titulo']").text();
                if (expression) {
                    if ($(this).text().toLowerCase().search(finder.toLowerCase()) == -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                } else {
                    if (title.toLowerCase().indexOf(value.toLowerCase()) < 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });

        /**/
        $("[data-type='accordion-ordering']").each(function () {
            console.log('dentro de orde');
	    var ordering="";
		$(this).click(function () {
	    	if(ordering=="") {console.log("no existe var"); ordering="asc";}
                var sections = $("[data-type='dencuali']");
                for (i = 0; i < sections.length; i++) {
                    for (x = 0; x < sections.length; x++) {
                        var str1 = sections.eq(x).find("[data-type='titulo']").text();
                        var str2 = sections.eq(x + 1).find("[data-type='titulo']").text();
                        if (ordering == "desc") {
                            if (str2 > str1) {
                                sections.eq(x).before(sections.eq(x + 1));
			    }
                        } else {
                            if (str2 < str1) {
                                sections.eq(x).before(sections.eq(x + 1));
                            }
                        }
                        sections = $("[data-type='dencuali']");
                    }
                }

                        if (ordering == "desc") ordering="asc";
			else ordering="desc";

            });
        });
        $("[data-type='accordion-filter']").change(function () {
            var selected = $(this).select().val();
            $("[data-type='accordion-section']").each(function () {
                $(this).show();
                if (selected != "default") {
                    if ($(this).attr("data-filter") != selected) {
                        if ($(this).css("display") == "block")
                            $(this).hide();
                    }
                }
            });
        });
        return this;
    }
);
