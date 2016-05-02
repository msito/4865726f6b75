<?php
header("Content-type: text/javascript");
?>
$(document).ready(function () {
    closePopup();
    clearAll();
    $(".hidedesc").hide();
    $("#pager").hide();
    $("#pageinfo").hide();

    /*
     * When a rover is chosen, it changes the data based on the choice.
     */
    $("#exploreform #rover_list").change(function () {
        rover_id = $(this).val();
        camera_name = '';
        $('#exploreform #roverdependent').show();
        fetchData(rover_id);
        $("#pager").show();
        $("#pageinfo").show();
    });

    /**
     * When a camera is chosen, it changes the data based on the choice.
     */
    $("#exploreform #camera_list").change(function () {
        camera_name = $(this).val();
        if (camera_name === 'ALL') camera_name = '';
        page_num = 1;
        fetchData(rover_id);
    });

    /**
     * Clears all the values to start from scratch.
     */
    $("#clear").click(function () {
        $("#exploreform #rover_list").val("");
        clearAll();
        return false;
    });

    /**
     * When results area is clicked, it determines what area of results been clicked.
     * 
     * When Thumbnail image is clicked, it calculates image size and initiate a modal.
     * 
     * When header link is clicked, it filters the results based on camera choice.
     */
    $("#results").on('click', function (e) {

        if (e.target.className === 'thumbimage') {
            var current_image_location = e.target.currentSrc;
            var popup_width = $(window).width() - 100;
            var popup_height = $(window).height() - 100;
            var max_width = '';
            var max_height = '';

            $('<img src="' + current_image_location + '" />').load(function () {
                var image_width = this.width;
                var image_height = this.height;

                if (image_width > popup_width) max_width = popup_width;
                else max_width = image_width;

                if (image_height > popup_height) max_height = popup_height;
                else max_height = image_height;

                $("#forpopup").css('height', $(window).height());
                $("#popup").css('width', max_width + 'px');
                $("#popup").css('height', max_height + 'px');
                $("#popup").css('margin-left', ((($(window).width() - max_width) / 2) - 20) + 'px');
                $("#popup").css('margin-top', ((($(window).height() - max_height) / 2) - 20) + 'px');
                $("#popup img").css('max-width', max_width + 'px');
                $("#popup img").css('max-height', max_height + 'px');
            });

            showPopup();

            $("#popup").html('<img src="' + current_image_location + '" alt="">');
            return false;
        } else if (e.target.className === 'cameraheaderlink') {
            camera_name = e.target.id;
            page_num = 1;
            fetchData(rover_id);
            return false;
        } 


    });

    $("#pager").on('click', function (e) {
        if( e.target.className === 'nextresults' ){
          page_num  = ++page_num;
          fetchData(rover_id);
          return false;
        } else if( e.target.className === 'prevresults' ){
            page_num = --page_num;
            if( page_num < 1 ) page_num = 1;
            fetchData( rover_id );
            return false;
        }

        return false;
    });

    /**
     * For Earth Date entry
     * keyCode 13 is enter key.  Once key is pressed, it is blured to call
     * focsout action.
     **/
   $("#exploreform #earthdate input").on('keypress', function (e) {
        if (e.type === 'keypress' && e.keyCode === 13){
            $(this).blur();
            return false;
        }
    });

    /**
     * For Earth Date entry
     * When the earth date is entered, it caculates the date that is within the appropriate dates
     * and produce page results accordingly.
     */
    $("#exploreform #earthdate input").on('focusout', function (e) {                    
        earth_date = $(this).val();
        if( e.keyCode === 13 ) $(this).blur();
        if (earth_date !== "") {
            var valid_earth_date = function (earth_date) {
                if (earth_date.length !== 10)
                    return false;
            };
            if (valid_earth_date !== false) {
                if( earth_date < rover_object.landing_date ){
                    earth_date = rover_object.landing_date;
                } else if( earth_date > rover_object.max_date ){
                    earth_date = rover_object.max_date;
                }
                $("#earthdate input").val( earth_date );
                sol = '';
                $("#earthdate input").attr("required", true);
                $("#soldate input").removeAttr("required");
                $("#soldate input").val("");
                page_num = 1;

                fetchData(rover_id);
            }
        } else {
            if (sol === '') {
                $("#earthdate input").attr("required", true);
            }
        }
        return false;
    });

    /**
     * For Sol entry
     * keyCode 13 is enter key.  Once key is pressed, it is blured to call
     * focsout action.
     **/
    $("#exploreform #soldate input").on('keypress', function (e) {
        if (e.type === 'keypress' && e.keyCode === 13){
            $(this).blur();
            return false;
        }
    });

    /**
     * For Sol entry
     * When the sol is entered, it caculates the date that is within the appropriate sol
     * and produce page results accordingly.
     */
    $("#exploreform #soldate input").on('focusout', function () {

        sol = $(this).val();
        if (sol !== "") {
            if( sol < 0 ) {
                sol = 0;
            } else if( sol > rover_object.max_sol ){
                sol = rover_object.max_sol;
            }
            $("#soldate input").val(sol);
            earth_date = '';
            $("#soldate input").attr("required", true);
            $("#earthdate input").removeAttr("required");
            $("#earthdate input").val("");
            page_num = 1;
            fetchData(rover_id);
        } else {
            if ($earth_date === '') {
                $("#soldate input").attr("required", true);
            }
        }
        return false;
    }); 

    /**
     * To close the modal when user click on image itself.
     */
    $("#popup").on('click', function () {
        closePopup();
        return false;
    });

    /**
     * To close the modal when user click on outside of image itself.
     */
    $("#forpopup").on('click', function () {
        closePopup();
        return false;
    });

    /**
     * To close the modal when user presses escape key.
     */
    $(document).on('keydown', function (e) {
        if (e.keyCode === 27) {
            closePopup();
            return false;
        }
    }); 
});
