<!doctype html>
<html lang="en">
[[$chunk_head]]
<body id="login">
<style>
    @charset "utf-8";
    /* CSS Document */

    /*# Web Fonts #*/
    /*# Web Fonts/Font Awesome Icons #*/
    @font-face {
        font-family:'FontAwesome';
        src:url('font/fontawesome-webfont.eot?v=4.3.0');
        src:url('font/fontawesome-webfont.eot?#iefix&v=4.3.0') format('embedded-opentype'), url('font/fontawesome-webfont.woff2?v=4.3.0') format('woff2'), url('font/fontawesome-webfont.woff?v=4.3.0') format('woff'), url('font/fontawesome-webfont.ttf?v=4.3.0') format('truetype'), url('font/fontawesome-webfont.svg?v=4.3.0#fontawesomeregular') format('svg');
        font-weight:normal;
        font-style:normal;
    }

    .font_icon {font-family:'FontAwesome';}
    .font_icon:before
    {
        display:inline-block;

        text-align:center;
    }

    .font_icon_address:before {content:'\f041';}
    .font_icon_external:before {content:'\f08e';}
    .font_icon_global:before {content:'\f0ac';}
    .font_icon_home:before {content:'\f015';}
    .font_icon_ie:before {content:'\f26b';}
    .font_icon_map:before {content:'\f278';}
    .font_icon_phone:before {content:'\f095';}
    .font_icon_plus:before {content:'\f067';}
    .font_icon_tags:before {content:'\f02c';}

/*# General #*/
*, *:before, *:after
    {
        box-sizing:border-box;
        -moz-box-sizing:border-box;
    }

    :focus {
        outline:0;
    }


    html
    {
        display:block;
        height:100%;

        color:#333333;
        font-size:62.5%;
        font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;
    }

    body
    {
        display:block;
        height:100%;
        padding:0;
        margin:0;

        background:#ededed;

        line-height:1.4;
    }

    h1, h2, h3, h4, h5, h6, p, ol, ul, li
    {
        padding:0;
        margin:0;
    }

    h1 {font-size:1.4em;}
    h2 {font-size:1.2em;}
    h3 {font-size:1.2em;}
    h4, h5, h6, p, ol, ul {font-size:1em;}

/* tables still need 'cellspacing="0"' in the markup */
table {
        border-collapse:collapse;
        border-spacing:0;
    }

    a, a:hover, a:visited {
        color:inherit;
        text-decoration:inherit;
    }

    img {display:block;max-width:100%;}

    .wrapper {width:100%;}
    .container {width:95%;margin:0 auto;}

    .column_container:after {display:block;height:1px;margin-top:-1px;clear:both;content:' ';}
    .column_container > .column {display:block;float:left;min-height:1px;}
    .column_12 {width:100%;}
    .column_11 {width:91.66666667%;}
    .column_10 {width:83.33333333%;}
    .column_9 {width:75%;}
    .column_8 {width:66.66666667%;}
    .column_7 {width:58.33333333%;}
    .column_6 {width:50%;}
    .column_5 {width:41.66666667%;}
    .column_4 {width:33.33333333%;}
    .column_3 {width:25%;}
    .column_2 {width:16.66666667%;}
    .column_1 {width:8.33333333%;}

    .clear {clear:both;}

    /*# Search #*/
/* Base Z-INDEX 900 */
.search_trigger
    {
        text-align:center;
        cursor:pointer;
    }

    .search_trigger:before
    {
        font-family:'FontAwesome';

        content:'\f002';
    }

    #search_wrapper
    {
        display:block;
        position:absolute;
        top:5rem;
        left:0;

        background:#000000;
        -webkit-transition:-webkit-transform 500ms ease;
        -moz-transition:-moz-transform 500ms ease;
        -ms-transition:-ms-transform 500ms ease;
        -o-transition:-o-transform 500ms ease;
        transition:transform 500ms ease;

        -ms-transform: translate(0, -100%);
        -webkit-transform: translate3d(0, -100%, 0);
        -moz-transform: translate3d(0, -100%, 0);
        -ms-transform: translate3d(0, -100%, 0);
        -o-transform: translate3d(0, -100%, 0);
        transform: translate3d(0, -100%, 0);

        -webkit-backface-visibility:hidden;

        z-index: 950;
    }

    #search_wrapper.search_expand
    {
        -ms-transform: translate(0, 0);
        -webkit-transform: translate3d(0, 0, 0);
        -moz-transform: translate3d(0, 0, 0);
        -ms-transform: translate3d(0, 0, 0);
        -o-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }

    #search_container
    {
        display:block;
        padding:6rem 0;
        position:relative;
    }

    #search_container .search_form_row
    {
        display:block;
        width:24rem;
        max-width:80%;
        clear:both;
        padding:1rem 0;
        margin:0 auto;

        font-size:1.4em;
    }

    #search_submit_container.search_form_row
    {
        padding-top:2.5rem;
    }

    #search_container .search_form_row label
    {
        display:block;
        width:100%;
        padding:0.5em 0;

        border:0.2rem solid transparent;

        color:#ffffff;
        line-height:1.2;
    }

    #search_container .search_form_row input[type="text"]
    {
        display:block;
        width:100%;
    }

    #search_submit
    {
        display:block;
        width:100%;
    }

    #search_submit span:before
    {
        padding:0 0.5em 0 0;

        font-family:'FontAwesome';

        content:'\f002';
    }

    #search_wrapper_close
    {
        display:block;
        width:3rem;
        height:3rem;
        position:absolute;
        top:1rem;
        left:0;

        text-align:center;
        cursor:pointer;
    }

    #search_wrapper_close:before
    {
        display:inline-block;

        color:#ffffff;
        font-family:'FontAwesome';
        font-size:2.4rem;
        line-height:3rem;

        content:'\f00d';
    }

    /*# Header #*/
    /* Base Z-INDEX 300 */
    /*# Header/Top #*/
    /* Base Z-INDEX 1000 - Same as Off Canvas */
    #top_wrapper
    {
        min-height:5rem;
        position:relative;
        padding:0.5rem;

        background:#222222 url(../image/bg_top_wrapper.jpg) top center repeat-x;
        background:-webkit-linear-gradient(top, #222222, #000000);
        background:-moz-linear-gradient(top, #222222, #000000);
        background:-ms-linear-gradient(top, #222222, #000000);
        background:-o-linear-gradient(top, #222222, #000000);
        background:linear-gradient(top, #222222, #000000);

        z-index:1000;
    }

    #top_wrapper_logo
    {
        display:block;
        width:100%;
        height: 100%;
        position:absolute;
        top:0;
        left:0;

        text-align:center;

        z-index:1010;
    }

    #top_wrapper_logo > a
    {
        display:inline-block;
        height:100%;
        padding: 3px;
    }

    #top_wrapper_logo img
    {
        display:block;
        max-height:100%;
    }

    #top_wrapper_off_canvas_trigger
    {
        display:block;
        width:4rem;
        height:4rem;
        position:relative;
        float:left;

        border-radius:0.3rem;

        z-index:1020;
    }

    #top_wrapper_off_canvas_trigger:hover
    {
        background-color:rgba(255,255,255,0.1);
    }

    #top_wrapper_search_trigger
    {
        display:block;
        width:4rem;
        height:4rem;
        position:relative;
        float:right;

        border-radius:0.3rem;

        z-index:1030;
    }

    #top_wrapper_search_trigger:before
    {
        display:inline-block;

        color:#ffffff;
        font-size:2.4rem;
        line-height:4rem;
    }

    #top_wrapper_search_trigger:hover
    {
        background-color:rgba(255,255,255,0.1);
    }

    /*# Header/Banner #*/
    #banner_wrapper
    {
        display:block;
        height:14rem;
        position:relative;

        background:url(../image/bg_banner_wrapper.jpg) center center no-repeat;
        background-size:cover;
    }

    #banner_container
    {
        display:block;
        max-height:100%;
        padding:1.5em 0 0 0;
        position:relative;

        color:#ffffff;
        text-align:center;

        overflow:hidden;

        z-index:320;
    }

    #banner_title
    {
        padding:0 0 0.5em 0;

        font-size:3em;
        font-weight:bold;
    }

    #banner_slogan
    {
        font-size:1.4em;
        font-weight:bold;
    }

    #banner_mask
    {
        display:block;
        width:100%;
        height:100%;
        position:absolute;
        top:0;
        left:0;

        background:rgba(120,120,120,0.4);

        pointer-events:none;

        z-index:310;
    }
</style>
[[*page_content:container_name=`container_off_canvas`]]
[[+script]]
</body>
</html>