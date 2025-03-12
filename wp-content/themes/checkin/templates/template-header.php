<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width">
    <link type="image/x-icon" href="/favicon.ico" rel="icon"> <!-- icon show on web title -->
    <link type="image/x-icon" href="/favicon.ico" rel="shortcut icon" />

    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <!-- B--- phan cho bootstrap -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- E --- phan bootstrap ------------->
    <!--[if lt IE 9]>
        <script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
        <![endif]-->
    <!-- them jquery tu google    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
    <meta name="geo.region" content="VN" />
    <meta name="geo.position" content="10.725377;106.720064" />
    <meta name="ICBM" content="10.725377, 106.720064" />
    <?php wp_head(); ?>
</head>
<?php
require_once DIR_MODEL . 'model-check-in-event-function.php';
$model_event = new Model_Check_In_Event_Function();
$active_event = $model_event->getActiveItem();
?>

<body>
    <div class="check-in-space">
        <div class="header">
            <div class="logo">
                <img src="<?php echo PART_IMAGES . 'logoctcvn.png' ?>" alt="ctcvn_logo" title="ctcvn_logo" />
                <div>
                    <h2>越南台灣商會聯合總</h2>
                    <h3>THE COUNCIL OF TAIWANESE CHAMBERS OF COMMERCE IN VIETNAM </h3>
                </div>
            </div>
            <div class="title">
                <h1><?php echo $active_event['title']; ?></h1>
            </div>
        </div>