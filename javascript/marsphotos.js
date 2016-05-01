/** 
 * This section sould be stored in separate file. 
 * Example: /javascript/marsphotos.js
 */
    var sol = '0';
    var earth_date = '';
    var camera_name = '';
    var api_key = 'LeATHlsoxSJo6JGxQrNQwpA71ANMDATfLHoF0wjB';
    var page_num = 1;
    var rover = '';
    var rovers = ['curiosity', 'opportunity', 'spirit'];
    var rover_object = {};
    var rover_sprit_object = {};
    var rover_opportunity_object = {};
    var rover_curiosity_object = {};

    /**
     * Fetching data and determines if the call was successful or not. 
     */
    function fetchData(rover_id) {
        var url = getApiUrl(rover_id);
        //url = '';
        $("#pageinfo").html('Api Call: ' + url);
        $("#results").text("");

        var data = $.getJSON(url, function () {

        })
                .done(function (data) {
                    successfulCall(data);
                    showDescriptions();
                    printCameras();
                    initializeOverlay();
                })
                .fail(function () {

                    $("#pager").hide();
                    printCameras();
                    if (typeof (data) !== 'undefined') {
                        $("#results").html('<div class="">' + (JSON.parse(data.responseText)).errors + '.</div>');
                        return false;
                    } else {
                        $("#results").html('<div class="error">' + friendlyErrorMessage(rover_id) + '</div>');
                        return false;
                    }
                });
    }

    /** 
     * 
     * This function is called when API returns without an error.  It also assignes
     * rover objects for the first successful call.
     * @param {object} takes object that was returned by calling API url. 
     */
    function successfulCall(data) {

        var current_camera = '';
        $.each(data, function (index, photos) {
            if( jQuery.isEmptyObject( photos )  === true ){
                $("#results").text("No Photos Found");
                $(".nextresults").text("");
                $("#pagenum").text("Page " + page_num);
                return null;
            }
            $.each(photos, function (photo_index, photo_obj) {

                var image_object = imageObject(photo_obj);
                if (photo_index === 0) {
                    switch (rover_id) {
                        case 'curiosity':
                            if (jQuery.isEmptyObject(rover_curiosity_object) === true)
                                rover_curiosity_object = image_object.rover_info;
                                rover_object = rover_curiosity_object;
                            break;
                        case 'opportunity':
                            if (jQuery.isEmptyObject(rover_opportunity_object) === true)
                                rover_opportunity_object = image_object.rover_info;
                                rover_object = rover_opportunity_object;
                            break;
                        case 'spirit':
                            if (jQuery.isEmptyObject(rover_sprit_object) === true)
                                rover_sprit_object = image_object.rover_info;
                                rover_object = rover_sprit_object;
                            break;
                    }
                } 
                // Display camera name
                if (current_camera !== image_object.camera_id) {
                    $("#results").append('<h2 class="cameraheader"><a href="#" class="cameraheaderlink" id="' + image_object.camera_name + '">' + image_object.camera_fullname + '</a></h2>');
                    current_camera = image_object.camera_id;
                }
                // Prepare results
                $("#results").append(image_object.printImageList());
                if( photo_index === 24 ){
                    html_text = '';
                    if( page_num > 1 ){
                        html_text += ' <span class="pager"><a href="#" class="prevresults">Previous Results</a></span> ' ;
                    }

                    html_text += ' <span class="page"><a href="#" class="nextresults">Next Results</a></span> ' ;
                    html_text += '<span id=pagenum">Page ' + page_num + '</span>';
                    $("#pager").html( html_text );

                }
            });

        });

    }

    /**
     * If information is available, this function will show information to users
     * to assist in exploring the data.
     */
    function showDescriptions() {
        if (jQuery.isEmptyObject(rover_object) === false) {
            $("#rovermaxsol").text(rover_object.max_sol);
            $("#soldate input").attr("max", rover_object.max_sol);
            $("#soldesc").show();

            $("#rovermaxdate").text(rover_object.max_date);
            $("#earthdate input").attr("max", rover_object.max_date);
            $("#roverlandingdate").text(rover_object.landing_date);
            $("#earthdate input").attr("min", rover_object.landing_date);
            $("#eddesc").show();
        }
    }

    /**
     * This function generates new camera list based on Rover ID, which was part of
     * JSON data. rover_object is assigned at time of fetching data.
     */
    function printCameras() {
        if (jQuery.isEmptyObject(rover_object) === false) {
            var cameralist = rover_object.cameras;
            if (jQuery.isEmptyObject(cameralist) === false) {
                $("#camera_list").text("");
                $("#camera_list").append('<option value="">All ' + rover_object.name + ' Cameras</option>');
                $.each(cameralist, function (index, cameraobject) {
                    $("#camera_list").append('<option value="' + cameraobject.name + '" ' + (cameraobject.name === camera_name ? 'selected' : '') + '>' + cameraobject.full_name + '</option>');
                });
            }
        }
    }


    /** 
     * Construct URL to retrieve JSON Data.  
     * @param {string} takes rover_id that is one of rovers array.
     * @returns {String} returns URL to call NASA Mars Photo API.
     */
    function getApiUrl(rover_id) {
        if (rover_id === null || rover_id === "" || typeof (rover_id) === 'undefined')
            return '';
        if (jQuery.inArray(rover_id, rovers) < 0)
            return '';

        var temp_text = 'https://api.nasa.gov/mars-photos/api/v1/rovers/' + rover_id + '/photos';


        if (sol !== '' && sol !== null && typeof (sol) !== 'undefined') {
            temp_text += '?sol=' + sol;
        } else {
            temp_text += '?earth_date=' + earth_date;
        }

        if (camera_name !== '' && camera_name !== null && typeof (camera_name) !== 'undefined') {
            temp_text += '&camera=' + camera_name;
        }

        if (page_num !== '' && page_num !== null && typeof (page_num) !== 'undefined') {
            if( page_num < 1 ) page_num = 1;
            temp_text += '&page=' + page_num;
        }

        if (api_key === '' || api_key === null || typeof (api_key) === 'undefined') {
            api_key = 'DEMO_KEY';
        }

        temp_text += '&api_key=' + api_key;
        return temp_text;
    }

    /** 
     * Creates new object for Mars Photo with information.
     * @param {type} object
     * @returns {_L211}
     */
    function imageObject(object) {
        if (object === null || object === "" || typeof (object) === 'undefined')
            return null;

        return new function () {
            this.id = object.id;
            this.sol = object.sol;
            this.img_src = object.img_src;
            this.thum_img = object.img_src;
            this.earth_date = object.earth_date;

            this.camera_info = object.camera;
            this.camera_id = this.camera_info.id;
            this.camera_name = this.camera_info.name;
            this.camera_fullname = this.camera_info.full_name;

            this.rover_info = object.rover;
            this.rover_id = this.rover_info.name.toLowerCase();
            this.rover_name = this.rover_info.name;
            this.rover_landing_date = this.rover_info.landing_date;
            this.rover_max_sol = this.rover_info.max_sol;
            this.rover_max_date = this.rover_info.max_date;
            this.rover_total_photos = this.rover_info.total_photos;
            this.rover_cameras = this.rover_info.cameras;

            this.printImageList = function () {
                var line_sep = "\n";
                var text = '<div id="' + this.rover_id + '_' + this.camera_id + '_' + this.id + '" class="imageobjectinfo">';
                text += '<div class="row">' + line_sep;
                text += '  <div class="roverimage">' + line_sep;
                text += '    <a href="' + this.img_src + '">' + line_sep;
                text += '      <img class="thumbimage" src="' + this.thum_img + '" alt="Image from ' + this.camera_fullname + '"/>' + line_sep;
                text += '    </a>' + line_sep;
                text += '    <div class="camera_name">' + line_sep;
                text += '        Camera: ' + this.camera_fullname + '<br />' + line_sep;
                text += '        Sol: ' + this.sol + '<br />' + line_sep;
                text += '        Earth Date: ' + this.earth_date + '<br />' + line_sep;
                text += '    </div>' + line_sep;
                text += '  </div>' + line_sep;
                text += '</div>' + line_sep;
                text += '</div>' + line_sep;
                return text;
            }
        }
    }

    function initializeOverlay() {
        var popupwidth = $(window).width() - 100;
        var popupheight = $(window).height() - 100;

        $("#forpopup").css("height", $(window).height());
        $("#popup").css("background-size", popupwidth + 'px ' + popupheight + 'px');
    }


    function clearAll() {
        $("#rover_list").val("");
        $("#camear_list").val("");
        $("#earthdate input").val("");
        $("#soldate input").val("0");
        $("#results").text("Please select Rover of your choice to begin.");
        $('#roverdependent').hide();
        page_num = 1;
    }

    function closePopup() {
        $("#forpopup").hide();
        $("#popup").hide();
        $("#popup").html("");
    }

    function showPopup() {
        $("#forpopup").show();
        $("#popup").show();
    }

    function friendlyErrorMessage(rover_id) {
        if (rover_id === '')
            return 'Please select Rover of your choice to begin.';

        if (sol === '' && earth_date === '') {
            return 'Please select either Sol or Earth Date.';
        }

        return "Houston, we have a problem! and Huston has been notified.";
    }

