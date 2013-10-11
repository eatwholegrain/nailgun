/*!
 * NailGun v0.9
 */
$(document).ready(function() {

    $(function() {

        main();
        settingsPanel();
        infoBox();
        downloadBox();
        header();
        footer();
        search();
    });

    function main(){

        $(".ng-logo").click(function() {
            window.location.href = "index.php";
            return false;
        });

        $("a[href='#']").click(function(e) {
            e.preventDefault();
        });

        $(".tip").tipTip({maxWidth: "500px", edgeOffset: 5});

        $("#to-top").click(function(e) {
            $("html, body").animate({ scrollTop: 0 }, 1200);
            e.preventDefault();
        });

        setInterval(function() { 
            $.ajax({
                type: "GET",
                url: "lib/api/activity-timer.php",
                data: "",    
                success: function(response) {
                    //console.log(response);
                }
            });
        }, 100000);

    }
    
    function settingsPanel() {

        $("#settings").css("top", "-"+(parseInt(($("#settings-menu").css("height")), 10)+32)+"px");
        
        $("#settings-button").click(function() {

            if ($("#settings").css("top") != "0px") {
                $("#add-project").css("z-index", "9998");
                $("#settings").css("z-index", "9999");
                $("#settings").animate({
                    top: "0px",
                  }, 400, 'swing', function() {
                    $(this).addClass("open");
                });

            } else {

                $("#settings").animate({
                    top: "-"+(parseInt(($("#settings-menu").css("height")), 10)+32)+"px",
                  }, 500, 'easeOutBounce', function() {
                    $("#add-project").css("z-index", "9999");
                    $("#settings").css("z-index", "9998");
                    $(this).removeClass("open");
                });
            }

            return false;
        });
    }

    function infoBox() {

        var initialHeight = $("#project-meta").css("height");
        $("#project-meta").animate({"height":"19px"});
        $("#project-meta").addClass("collapsed");

        $("#project-meta").click(function() {

            if ($(this).css("height") == "19px") {
                $(this).removeClass("collapsed");
                $(this).addClass("expanded");
                $(this).animate({"height":initialHeight});
            } else {
                $(this).removeClass("expanded");
                $(this).addClass("collapsed");
                $(this).animate({"height":"19px"});
            }
        });
    }

    function downloadBox() {

        $(".update-file").animate({"height":"21px"});
        $(".update-file").addClass("collapsed");

        $(".update-file").click(function() {

            if ($(this).css("height") == "21px") {
                $(this).removeClass("collapsed");
                $(this).addClass("expanded");
                $(this).animate({"height":"105px"});
                $(this).find(".image-preview img").show("fast");
            } else {
                $(this).removeClass("expanded");
                $(this).addClass("collapsed");
                $(this).animate({"height":"21px"});
                $(this).find(".image-preview img").hide("fast");
            }
        });
    }

    function search() {

        $("#search-trigger").click(function(e) {
            $("#search-bar").animate({"top":"0px"});
            $("#search-field").focus();
            e.preventDefault();
        });

        $("#close-button").click(function(e) {
            $("#search-bar").animate({"top":"-120px"});
            e.preventDefault();
        });

        $("#search-button").click(function(e) {
            if($("#search-field").val().length > 2) {
                window.location.href = "search-tasks.php?s="+ encodeURIComponent($("#search-field").val());
            } else {
                $.achtung({message: 'Enter search term', timeout: 7});
            }
            e.preventDefault();
        });

        $("#search-field").keydown(function(e) {
            if (e.keyCode == 13) {
                if($("#search-field").val().length > 2) {
                    $("#search-form").submit();
                } else {
                    $.achtung({message: 'Enter search term', timeout: 7});
                }
            }
        });
    }

    function header() {
        var stickyPanelOptions = {
                topPadding: 0,
                afterDetachCSSClass: "blue-panel",
                savePanelSpace: true
            };

        $("#header-title").stickyPanel(stickyPanelOptions);
    }

    function footer() {

        var footerHeight = 75; 
        var footerTop = 0; 
        var footer = $("footer"); 

        footerTop = ($(window).scrollTop() + ($(window).height()-10) - footerHeight) + "px";

        if (($(document.body).height() + footerHeight) < $(window).height()) {
            footer.css({position: "absolute"}).css({top: footerTop});           
        } else {
            footer.css({position: "static"});
        }
    }

    $("#loader").fadeOut();

});