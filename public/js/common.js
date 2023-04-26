jQuery(document).ready(function(){
    // side menu selected
    var subMenuClass = ".m-menu__subnav li a.m-menu__link";
    var subMenuLiClass = ".m-menu__subnav li";

    var currentRouteName = jQuery("#current-route").val();
    
    if(currentRouteName){
        activeSideMenu(currentRouteName);
        expandActiveMenu();
    }    

    jQuery(document).on("click",subMenuClass, function(){
        removeActiveClass();
        var currentRouteName = jQuery(this).attr("id");
        activeSideMenu(currentRouteName);
 
    });

    
    function activeSideMenu(currentRouteName){ 
        var routeId = "#"+currentRouteName;
        jQuery(routeId).closest("li").addClass("m-menu__item--active");
        jQuery(routeId).closest("li.sub-menu").addClass("m-menu__item--active");
        jQuery(routeId).closest("li.parent-menu").addClass("m-menu__item--active");
    }

    function removeActiveClass(){
        jQuery(".m-menu__item--active").removeClass("m-menu__item--active");
    }

    function expandActiveMenu(){
        jQuery(".m-menu__item--active").addClass("m-menu__item--open");
    }
    // init select2
    commonLib.select2(".select2");
    
    $('.modal').on('hidden.bs.modal', function() {
        setTimeout(function(){ 
            $("body").removeAttr("style");
        }, 500); 
    });
});

