<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<?php
$styling = '
    @media screen and (min-width: 768px) {
        .invoize-title {
            font-size: 32px !important;
        }
    }
';
wp_add_inline_style('invoize-email-header', $styling);
?>

<body style="
    font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', 'sans-serif';
    box-sizing: border-box;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 20px;
    padding-bottom: 20px;
    margin: 0px;
    color: black;">

    <div class="content-wrapper" style="
        background-color: white;
        box-sizing: border-box;
        border: 2px solid black;
        border-radius: 12px;
        box-shadow: 6px 6px;
        width: 500px;
        height: 80%;">

        <?php if ($showHeader) : ?>
            <div class="plugin-name" style="
                width: 100%;
                box-sizing: border-box;
                padding: 16px 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;">

                <div style="
                    display: flex;
                    display: -webkit-flex;
                    align-items: center;
                    flex-wrap: nowrap;
                    column-gap: 6px;
                    ">
                    <img src="<?php echo esc_url($businessLogo) ?? '' ?>" alt="Invoize Logo" title="Invoize logo" style="
                        display: block;
                        height: auto;
                        max-width: 5vw;
                    " width="100px" height="50px" />
                    <div style="
                        color:#64748b;
                        font-size: 10px;
                        ">
                        <?php if (!empty($businessName)) : ?>
                            <div style="font-weight: 600;"><?php echo esc_html($businessName) ?></div>
                        <?php endif ?>
                        <?php if (!empty($businessEmail)) : ?>
                            <div><?php echo esc_html($businessEmail) ?></div>
                        <?php endif ?>
                        <?php if (!empty($businessPhone)) : ?>
                            <div><?php echo esc_html($businessPhone) ?></div>
                        <?php endif ?>
                        <?php if (!empty($businessWeb)) : ?>
                            <a href="<?php echo esc_url($businessWeb) ?>" target="_blank"><?php echo esc_url($businessWeb) ?></a>
                        <?php endif ?>
                    </div>
                </div>

                <div class="invoize-title" style="
                    width: 100%;
                    text-align: right;
                    font-size: 30px;
                    font-weight: 600;">
                    <?php if ($isReceipt) : ?>
                        RECEIPT
                    <?php else : ?>
                        INVOICE
                    <?php endif ?>
                </div>
            </div>

            <div class=" divider-1" style="
                border-bottom: 1px solid black;
                width: 85%;
                margin: 0 auto;">
            </div>
        <?php endif ?>

        <div class="content" style="
            margin: 24px 28px 96px; 
            font-size: 14px;">