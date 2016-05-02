<?php
header("Content-type: text/css");
?>

body{
    font-family: "Source Sans Pro",sans-serif, "Lucida Grande", "Lucida Sans","Lucida Sans Unicode ",arial,helvetica;
}

.error{
    color: #C74427;
    font-weight:bold;
}

label{
    font-weight:bold;
    display:block;
}

#contents{
    position:relative;
    display:block;
    margin:auto;
}

#contents #forpopup{
    width:100%;
    height:100%;
    display:block;
    z-index:1000;
    position:fixed;
    background-color: rgba(255, 255, 255, 0.8);
    display:none;
    z-index: 1000;
}

#contents #popup{
    display:block;
    position:fixed;
    margin:0 auto;    
    top:0px;
    z-index: 1010;
    text-align:center;
    vertical-align: middle;
    width:80%;
    height:80%;
    padding:10px;            
}

#contents #popup img{
    margin:auto;
    padding:10px;
    border:1px solid #cccccc;
    background-color:#ffffff;

}


#exploreform{
    border:1px solid #cccccc;
    background-color:#eeeeee;
    display:block;
    padding:10px;
}

#exploreform #roverdependent{ display:none; }

#exploreform .wrapper{
    padding:10px;
    display:block;
    float:left;
}        

#exploreform .requireddates{
    display:block;
    position:relative;
    max-width:700px;
    float:left;
}            

#results{
    clear:both;
}


#results .cameraheader{
    clear:both;
}

#results .imageobjectinfo{
    margin:auto;
}

#results .imageobjectinfo .roverimage{
    border:1px solid #cccccc;
    padding:10px;
    margin:10px;
    float:left;
}

#results .imageobjectinfo .roverimage .thumbimage{
    width:100px;
    float:left;
    height:100px;
    padding-right:10px;
}        

#results .imageobjectinfo .roverimage .camera_name{
    width:100px;
    float:right;
    height:100px;
    padding-right:10px;
    font-size:10px;
}        


#pager{
    border:1px solid #cccccc;
    background-color:#eeeeee;
    display:block;
    padding:10px;
    min-height:20px;
    text-align:center;
    position:relative;
}

#pager .nextresults{
    display:block;
    float:right;
}

#pager .prevresults{
    display:block;
    float:left;
}

