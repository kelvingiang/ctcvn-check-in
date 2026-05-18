<?php //Template Name: Check In Waiting ?>
<?php get_template_part('templates/template', 'header') ?>
<div>
    <div class=" col-lg-12 waiting-time ">
        <label ID="waiting_txt"><?php echo get_option('Waiting_text'); ?></label>
    </div>

</div>
<?php get_template_part('templates/template', 'footer') ?>

<style>
.waiting-time{
    background-image: url('<?php echo PART_IMAGES . 'bg.jpg' ?>');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 80vh;
    text-align: center;
    padding-top: 100px;
}   
#waiting_txt {
        text-shadow: 2px 2px 8px #000000;
        padding-top: 8%;
        font-weight: bold;
        letter-spacing: 10px;
        -webkit-animation-name: example;
        /* Safari 4.0 - 8.0 */
        -webkit-animation-duration: 5s;
        /* Safari 4.0 - 8.0 */
        -webkit-animation-iteration-count: infinite;
        /* Safari 4.0 - 8.0 */
        animation-name: example;
        animation-duration: 10s;
        animation-iteration-count: infinite;
        

    }


    /* Safari 4.0 - 8.0 */
    @-webkit-keyframes example {
        0% {
            color: #a94502;
            font-size: 6rem
        }

        50% {
            color: #057cfc;
            font-size: 8rem
        }

        100% {
            color: #a94502;
            font-size: 6rem
        }
    }

    /* Standard syntax */
    /* @keyframes example {
        0% {
            color: #333;
            font-size: 60px
        }

        20% {
            color: #333;
            font-size: 60px
        }

        40% {
            color: #FC9105;
            font-size: 72px
        }

        41% {
            color: #FC9105;
            font-size: 70px
        }

        42% {
            color: #FC9105;
            font-size: 71px
        }

        50% {
            color: #FC9105;
            font-size: 70px
        }

        70% {
            color: #FC9105;
            font-size: 70px
        }

        100% {
            color: #333;
            font-size: 60px;
        }
    } */
</style>