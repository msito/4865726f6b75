<!DOCTYPE html>
<html>
    <head>
        <title>Mars Photos</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style rel="stylesheet" href="/marsphotos/css/marsphotos.css"></style>

        <script src="/marsphotos/javascript/jquery.min.js"></script>
	<script src="/marsphotos/javascript/marsphotos.js"></script>

        <script>
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
                 * When Thumbnail image is clicked, it caculates image size and initiate a modal.
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

        </script>


    </head>
    <body>
        <div id="contents">        
            <div id="popup"></div>
            <div id="forpopup"><a href="#">Close</a></div>
            <h1>Mars Photos</h1>
            <div id="exploreform">
                <form method="POST" action="#">
                    <div class="wrapper">
                        <label for="rover_list" class="required">* Rover Choice:</label>
                        <select id="rover_list" required >
                            <option value="">Please select rover of your choice</option>
                            <option value="curiosity">Curiosity</option>
                            <option value="opportunity">Opportunity</option>
                            <option value="spirit">Spirit</option>
                        </select>
                    </div>
                    <div id="roverdependent">
                        <div class="wrapper">
                            <div id="cameras">
                                <label for="camera_list">Cameras On Rover:</label>
                                <select id="camera_list">
                                    <option name="ALL" value="">All Cameras</option>
                                    <option name="FHAZ" value="FHAZ">Front Hazard Avoidance Camera</option>
                                    <option name="RHAZ" value="RHAZ">Rear Hazard Avoidance Camera</option>
                                    <option name="MAST" value="MAST">Mast Camera</option>
                                    <option name="CHEMCAM" value="CHEMCAM">Chemistry and Camera Complex</option>
                                    <option name="MAHLI" value="MAHLI">Mars Hand Lens Imager</option>
                                    <option name="MARDI" value="MARDI">Mars Descent Imager</option>
                                    <option name="NAVCAM" value="NAVCAM">Navigation Camera</option>
                                    <option name="PANCAM" value="PANCAM">Panoramic Camera</option>
                                    <option name="MINITES" value="MINITES">Miniature Thermal Emission Spectrometer (Mini-TES)</option>
                                </select>
                            </div>
                        </div>


                        <div class="requireddates">
                            <div class="required">* Please enter either Sol or Earth Date.</div>
                            <div class="wrapper">
                                <div id="soldate">                
                                    <label for="soldate">Sol (Martian Rotation):</label>
                                    <!-- type number is not supported for IE9 or earlier version -->
                                    <input type="number" name="soldate" min="0" max="1000" value="0" />
                                    <div id="soldesc" class="hidedesc">Available sol: between 0 to <span id="rovermaxsol"></span></div>
                                </div>
                            </div>
                            <div class="wrapper">
                                <div id="earthdate">
                                    <label for="earthdate">Earth Date:</label>
                                    <!-- type number is not supported for IE -->
                                    <input type="date" name="earthdate" min="2015-01-01" max="2016-01-01" />
                                    <div id="eddesc" class="hidedesc">Available earth date: between <span id="roverlandingdate"></span> and <span id="rovermaxdate"></span></div>
                                </div>
                            </div>

                        </div>
                        <div class="wrapper">
                            <div id="clear"><a href="#">Clear</a></div>
                        </div>

                    </div>
                    <div style="clear:both"></div>
                </form>
                <div style="clear:both"></div>
            </div>

            <div style="clear:both"></div>
           
            
            <div id="results">
                Please select Rover of your choice to begin.
            </div>
            <div style="clear:both"></div>


            <div id="pager"></div>
        </div>
    </body>
</html>
