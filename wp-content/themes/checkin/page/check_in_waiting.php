<?php get_template_part('templates/template', 'header') ?>
<div>
    <div class=" col-lg-12" style="text-align: center; height:300px; line-height: 300px;">
        <label ID="waiting_txt"><?php echo get_option('Waiting_text'); ?></label>
    </div>

</div>
<?php get_template_part('templates/template', 'footer') ?>

<style>
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
        animation-duration: 5s;
        animation-iteration-count: infinite;

    }


    /* Safari 4.0 - 8.0 */
    @-webkit-keyframes example {
        0% {
            color: #fff;
            font-size: 8rem
        }



        50% {
            color: #057cfc;
            font-size: 10rem
        }



        100% {
            color: #fff;
            font-size: 8rem
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