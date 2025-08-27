<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$plugin = \Invoize\InvoizePlugin::getInstance();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"
    style="-webkit-font-smoothing: antialiased;">

<head>
    <title></title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta content="light" name="color-scheme">
    <meta content="light" name="supported-color-schemes">
</head>

<body style="word-spacing:normal;background-color:#FAFAFA;margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">Dede, your weekly summary is here. See how monthlyreview performed.</div>
    <div style="background-color:#FAFAFA;">
        <div style="background: #B7C0D2; height: 5px; width: 100%; max-width: 660px; margin: 0 auto"></div>
        <!-- header --><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
        <div style="margin:0px auto;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0;padding-top:40px;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td align="left" class="desktop-p-0" style="font-size:0px;padding:10px 25px;padding-top:0;padding-bottom:0;word-break:break-word;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width:120px;">
                                                                <a href="https://wpsora.com" target="_blank">
                                                                    <img alt="WPsora Invoize" height="auto" src="<?php 
echo esc_url( $plugin->getPluginUrl( 'public/invoize-logo.png' ) );
?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:70%;font-size:13px;vertical-align:middle;" width="120">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]></td></tr></table><![endif]--><!-- content --><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
        <div style="margin:0px auto;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0 0 0;padding-top:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td align="left" class="desktop-pl-0" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                    <h1 class="heading-title" style="font-size:22px;font-weight:600;margin-top:10px;margin-bottom:0;line-height:1.4;">
                                                        Monthly report for <?php 
echo esc_html( $businessName );
?>
                                                    </h1>
                                                    <h4 class="heading-subtitle text-subdued" style="font-size:16px;font-weight:normal;margin-top:0;margin-bottom:0;line-height:24px;color:#9DA1AF;">
                                                        <?php 
echo esc_html( gmdate( 'F' ) . ' ' . gmdate( 'Y' ) );
?>
                                                    </h4>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                <tbody>
                    <tr>
                        <td align="left" class="desktop-pl-0" style="font-size:0px;padding:10px 5px;word-break:break-word;">
                            <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:15px;line-height:1;text-align:left;color:#0E153A;">
                                <?php 
$sentences = ['<span style="line-height: 19.6px;">Awesome! You\'ve <strong>Surpassed</strong> Last Month\'s Transactions—Keep Up the Great Work ! 🎉</span>', '<span style="line-height: 19.6px;">Fantastic! You\'ve <strong>Exceeded</strong> Last Month\'s Transactions—Keep Up the Momentum! 🎉</span>', '<span style="line-height: 19.6px;">Great Job! You\'ve <strong>Outdone</strong> Last Month\'s Transactions—Let\'s Keep the Progress Going! 🚀</span>'];
if ( $invoiceSummaryPercentage < 0 ) {
    $sentences = ['<span style="line-height: 19.6px;">You\'re <strong>Falling Behind</strong> Last Month\'s Transactions—Let\'s Get Back on Track! 💪</span>', '<span style="line-height: 19.6px;">It looks like <strong>Transactions Are Down</strong> Compared to Last Month—There\'s Room to Improve! 📈</span>', '<span style="line-height: 19.6px;">Unfortunately, <strong>Transaction Count Dropped</strong> from Last Month—Don\'t Worry, You Can Turn It Around! 🔄</span>'];
}
?>
                                <?php 
echo wp_kses( $sentences[array_rand( $sentences )], ['span', 'strong'] );
?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]></td></tr></table><![endif]--><!-- overview --><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="card-top-outlook" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
        <div class="card-top" style="background:white;border-radius:8px 8px 0 0;border:1px solid #F3F3F3;border-bottom:0;margin:0px auto;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td align="left" class="card-header" style="font-size:0px;padding:10px 25px;word-break:break-word;border-bottom:1px solid #E5E7EF;">
                                                <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;">
                                                    <p class="card-title" style="line-height:1.4;margin:4px 0;font-size:14px;font-weight:500;">Invoice Overview</p>
                                                    <p style="margin:0;color:#9DA1AF;font-weight:bold;">
                                                        Total Transaction: <?php 
echo esc_html( $transactionsThisMonth );
?>
                                                        <span style="color: <?php 
echo esc_attr( ( $invoiceSummaryPercentage < 0 ? '#e74c3c' : '' ) );
?>">(<?php 
echo ( $invoiceSummaryPercentage < 0 ? 'Decrease' : 'Increase' );
?> <?php 
echo esc_html( abs( $invoiceSummaryPercentage ) );
?>% from last month)</span>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="card-content-outlook" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
        <div class="card-content" style="margin:0px auto;max-width: 600px;background:white;border-radius:0 0 8px 8px;border:1px solid #F3F3F3;border-top:0;border-bottom-width:3px;margin-bottom:20px !important;padding:14px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:middle;">
                                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:294px;" ><![endif]-->
                                <div class=" mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <table width="100%" border="0" style="border-color: #dadada; border-collapse: collapse; font-family: Arial, sans-serif;">
                                                            <tbody>
                                                                <!-- <tr style="background-color:#ECF0F1;">
                                                                    <th style="padding: 10px;width:30%">Currency</th>
                                                                    <th style="padding: 10px">Value</th>
                                                                    <th style="padding: 10px">Growth</th>
                                                                </tr> -->
                                                                <?php 
foreach ( $invoices as $currency => $data ) {
    ?>
                                                                    <tr>
                                                                        <td style="width:45%;text-align:left;font-weight:bold;padding-bottom:10px;">
                                                                            <?php 
    echo esc_html( $currency );
    ?>
                                                                        </td>
                                                                        <td style="text-align:left;padding-bottom:10px;">
                                                                            <?php 
    echo esc_html( $data['symbol'] . ' ' . number_format_i18n( $data['totalNominal'] ) );
    ?>
                                                                        </td>
                                                                        <td style="text-align:center;vertical-align:middle;padding-bottom:10px;">
                                                                            <?php 
    if ( is_null( $data['nominalPercentage'] ) ) {
        ?>
                                                                                -
                                                                            <?php 
    } else {
        ?>
                                                                                <table align="right">
                                                                                    <tr>
                                                                                        <td style="padding:5px;vertical-align:middle;">
                                                                                            <span style="color: <?php 
        echo esc_attr( ( $data['nominalPercentage'] < 0 ? '#e74c3c' : '#2ecc71' ) );
        ?>;vertical-align:middle;">
                                                                                                <?php 
        echo esc_html( abs( $data['nominalPercentage'] ) );
        ?>%
                                                                                            </span>
                                                                                        </td>
                                                                                        <td style="padding:5px;vertical-align:middle;">
                                                                                            <img src="<?php 
        echo esc_url( $plugin->getPluginAssetUrl( ( $data['nominalPercentage'] < 0 ? 'decrease' : 'increase' ) ) );
        ?>.png" alt="Increase" class="change-icon" width="20px" height="20px">
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            <?php 
    }
    ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php 
}
?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]></td></tr></table><![endif]--><!-- monitors table --><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
        <div style="margin:0px auto;max-width:600px;">
            <div class="card-top" style="background:white;border-radius:8px 8px 0 0;border:1px solid #F3F3F3;border-bottom:0;margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                    <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" class="card-header" style="font-size:0px;padding:10px 25px;word-break:break-word;border-bottom:1px solid #E5E7EF;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <p class="card-title" style="line-height:1.4;margin:4px 0;font-size:14px;font-weight:500;">Recurring Overview</p>
                                                        <?php 
if ( !is_null( $totalRecurringPercentage ) ) {
    ?>
                                                            <p style="margin:0;color:#9DA1AF;font-weight:bold;">
                                                                Total Recurring: <?php 
    echo esc_html( $totalRecurringThisMonth );
    ?>
                                                                <span style="color: <?php 
    echo esc_attr( ( $totalRecurringPercentage < 0 ? '#e74c3c' : '' ) );
    ?>">(<?php 
    echo esc_html( ( $totalRecurringPercentage < 0 ? 'Decrease' : 'Increase' ) );
    ?> <?php 
    echo esc_html( abs( $totalRecurringPercentage ) );
    ?>% from last month)</span>
                                                            </p>
                                                        <?php 
}
?>

                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-content" style="margin:0px auto;max-width: 600px;background:white;border-radius:0 0 8px 8px;border:1px solid #F3F3F3;border-top:0;border-bottom-width:3px;margin-bottom:20px !important;padding:14px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:middle;">
                                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:294px;" ><![endif]-->
                                <div class=" mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <table width="100%" border="0" style="border-color: #dadada; border-collapse: collapse; font-family: Arial, sans-serif;">
                                                            <tbody>
                                                                <?php 
?>
                                                                    <tr>
                                                                        <td align="center" valign="top" style="padding: 20px; background-color: #f4f4f4;">
                                                                            <!-- Main content table -->
                                                                            <table cellpadding="0" cellspacing="0" border="0" width="600" style="width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding: 20px;">
                                                                                            <!-- Skeleton loading content -->
                                                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="opacity: 0.5;">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td width="45%" style="padding-bottom: 10px;">
                                                                                                            <div style="width: 70%; height: 16px; background-color: #e0e0e0; border-radius: 4px;">&nbsp;</div>
                                                                                                        </td>
                                                                                                        <td width="55%" style="padding-bottom: 10px;">
                                                                                                            <div style="width: 50%; height: 16px; background-color: #e0e0e0; border-radius: 4px;">&nbsp;</div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <!-- Repeat this row structure 5 times -->
                                                                                                    <tr>
                                                                                                        <td width="45%" style="padding: 10px 0;">
                                                                                                            <div style="width: 60%; height: 14px; background-color: #e0e0e0; border-radius: 4px; margin-bottom: 8px;">&nbsp;</div>
                                                                                                            <div style="width: 40%; height: 14px; background-color: #e0e0e0; border-radius: 4px;">&nbsp;</div>
                                                                                                        </td>
                                                                                                        <td width="55%" style="padding: 10px 0;">
                                                                                                            <div style="width: 80%; height: 14px; background-color: #e0e0e0; border-radius: 4px; margin-bottom: 8px;">&nbsp;</div>
                                                                                                            <div style="width: 30%; height: 14px; background-color: #e0e0e0; border-radius: 4px;">&nbsp;</div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <!-- Repeat the above row 4 more times -->
                                                                                                </tbody>
                                                                                            </table>

                                                                                            <!-- Pro version overlay -->
                                                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top: 20px;">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td align="center" style="background-color: #f8f8f8; padding: 20px; border-radius: 8px;">
                                                                                                            <p style="margin: 0 0 10px 0; font-weight: bold; color: #333; font-size: 16px;">
                                                                                                                This feature is available in the pro version
                                                                                                            </p>
                                                                                                            <p style="margin: 0;">
                                                                                                                <a href="https://wpsora.com/" target="_blank" style="color: #007bff; text-decoration: none; font-weight: bold;">Upgrade now</a> to get access to this feature.
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                <?php 
?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin:0px auto;max-width:600px;">
            <div class="card-top" style="margin:0px auto;max-width: 600px;background:white;border-radius:8px 8px 0 0;border:1px solid #F3F3F3;border-bottom:0;margin:0px auto;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                    <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" class="card-header" style="font-size:0px;padding:10px 25px;word-break:break-word;border-bottom:1px solid #E5E7EF;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <p class="card-title" style="line-height:1.4;margin:4px 0;font-size:14px;font-weight:500;">Upcoming Invoices</p>
                                                        <p style="margin: 0;color:#9DA1AF">
                                                            Upcoming invoices is a list of invoices that are due in the next 7 days.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-content" style="margin:0px auto;max-width: 600px;background:white;border-radius:0 0 8px 8px;border:1px solid #F3F3F3;border-top:0;border-bottom-width:3px;margin-bottom:20px !important;padding:14px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:middle;">
                                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:294px;" ><![endif]-->
                                <div class=" mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <table width="100%" border="0" style="border-color: #dadada; border-collapse: collapse; font-family: Arial, sans-serif;">
                                                            <tbody>
                                                                <?php 
if ( !count( $upcomingExpired ) ) {
    ?>
                                                                    <tr>
                                                                        <td style="width:45%;text-align:left;color:#777;    ">
                                                                            No upcoming invoices
                                                                        </td>
                                                                    </tr>
                                                                <?php 
} else {
    ?>
                                                                    <?php 
    foreach ( $upcomingExpired as $ue ) {
        ?>
                                                                        <tr>
                                                                            <td style="width:50%;text-align:left;font-weight: bold;padding-bottom:10px;">
                                                                                <?php 
        echo esc_html( $ue->getInvoiceNumber() );
        ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php 
        echo esc_html( $ue->getDueDate( true ) );
        ?>
                                                                            </td>
                                                                            <td style="text-align:right;padding-bottom:10px;"> <?php 
        echo esc_html( $ue->getCurrency()['symbol'] . number_format_i18n( $ue->getTotal() ) );
        ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php 
    }
    ?>
                                                                <?php 
}
?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:600px;">
            <div class="card-top" style="background:white;border-radius:8px 8px 0 0;border:1px solid #F3F3F3;border-bottom:0;margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                    <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" class="card-header" style="font-size:0px;padding:10px 25px;word-break:break-word;border-bottom:1px solid #E5E7EF;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <p class="card-title" style="line-height:1.4;margin:4px 0;font-size:14px;font-weight:500;">Overdue Invoices</p>
                                                        <p style="margin:0;color: #9DA1AF;">
                                                            Overdue invoice is an invoice that has passed its due date and has not been paid.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-content" style="margin:0px auto;max-width: 600px;background:white;border-radius:0 0 8px 8px;border:1px solid #F3F3F3;border-top:0;border-bottom-width:3px;margin-bottom:20px !important;padding:14px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:middle;">
                                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:294px;" ><![endif]-->
                                <div class=" mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div style="font-family:Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:13px;line-height:1;text-align:left;color:#0E153A;">
                                                        <table width="100%" border="0" style="border-color: #dadada; border-collapse: collapse; font-family: Arial, sans-serif;font-size:14px;">
                                                            <tbody>
                                                                <?php 
if ( !count( $expiredThisMonth ) ) {
    ?>
                                                                    <tr>
                                                                        <td style="width:45%;text-align:left;color:#777;font-size:16px;">
                                                                            No overdue invoices
                                                                        </td>
                                                                    </tr>
                                                                <?php 
} else {
    ?>
                                                                    <?php 
    foreach ( $expiredThisMonth as $e ) {
        ?>
                                                                        <tr>
                                                                            <td style="width:50%;text-align:left;padding-bottom:10px;">
                                                                                <?php 
        echo esc_html( $e->getInvoiceNumber() . ' ' . $e->getClient()['name'] );
        ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php 
        echo esc_html( $e->getDueDate( true ) );
        ?>
                                                                            </td>
                                                                            <td style="text-align:right;padding-bottom:10px;">
                                                                                <?php 
        echo esc_html( $e->getCurrency()['symbol'] . number_format_i18n( $e->getTotal() ) );
        ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php 
    }
    ?>
                                                                <?php 
}
?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px; padding-bottom:20px;text-align:center;">
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="width:600px;" ><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;">
                                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:middle;width:294px;" ><![endif]-->
                                <div class="mj-column-per-49 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:middle;width:49%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:middle;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" class="desktop-pl-0" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span style="font-family: Inter, -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', sans-serif;font-size:14px;margin-right:7px;">
                                                                        Invoize Created by
                                                                    </span>
                                                                </td>
                                                                <td style="width:120px;">
                                                                    <a href="https://wpsora.com" target="_blank">
                                                                        <img alt="WPSora Invoize" height="auto" src="<?php 
echo esc_url( $plugin->getPluginUrl( 'public/wpsora-logo.png' ) );
?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:70%;font-size:13px;vertical-align:middle;" width="120">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]></td></tr></table><![endif]-->
    </div>

</body>

</html>