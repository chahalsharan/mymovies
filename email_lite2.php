<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$emailTitle}</title>
    <style type="text/css">
        /* Based on The MailChimp Reset INLINE: Yes. */  
        /* Client-specific Styles */
        #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
        body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;} 
        /* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/ 
        .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */  
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
        /* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */ 
        #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
        /* End reset */

        /* Some sensible defaults for images
        Bring inline: Yes. */
        img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;} 
        a img {border:none;} 
        .image_fix {display:block;}

        /* Yahoo paragraph fix
        Bring inline: Yes. */
        p {margin: 1em 0;}

        /* Hotmail header color reset
        Bring inline: Yes. */
        h1, h2, h3, h4, h5, h6 {color: black !important;}

        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        /* Outlook 07, 10 Padding issue fix
        Bring inline: No.*/
        table td {border-collapse: collapse;}

        /* Remove spacing around Outlook 07, 10 tables
        Bring inline: Yes */
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

        /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
        Bring inline: Yes. */
        a {color: orange;}


        /***************************************************
        ****************************************************
        MOBILE TARGETING
        ****************************************************
        ***************************************************/
        @media only screen and (max-device-width: 480px) {
            /* Part one of controlling phone number linking for mobile. */
            a[href^="tel"], a[href^="sms"] {
                        text-decoration: none;
                        color: blue; /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: orange !important;
                        pointer-events: auto;
                        cursor: default;
                    }

        }

        /* More Specific Targeting */

        @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
        /* You guessed it, ipad (tablets, smaller screens, etc) */
            /* repeating for the ipad */
            a[href^="tel"], a[href^="sms"] {
                        text-decoration: none;
                        color: blue; /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: orange !important;
                        pointer-events: auto;
                        cursor: default;
                    }
        }

        @media only screen and (-webkit-min-device-pixel-ratio: 2) {
        /* Put your iPhone 4g styles in here */ 
        }

        /* Android targeting */
        @media only screen and (-webkit-device-pixel-ratio:.75){
        /* Put CSS for low density (ldpi) Android layouts in here */
        }
        @media only screen and (-webkit-device-pixel-ratio:1){
        /* Put CSS for medium density (mdpi) Android layouts in here */
        }
        @media only screen and (-webkit-device-pixel-ratio:1.5){
        /* Put CSS for high density (hdpi) Android layouts in here */
        }
        /* end Android targeting */

    </style>

    <!-- Targeting Windows Mobile -->
    <!--[if IEMobile 7]>
    <style type="text/css">
    
    </style>
    <![endif]-->   

    <!-- ***********************************************
    ****************************************************
    END MOBILE TARGETING
    ****************************************************
    ************************************************ -->

    <!--[if gte mso 9]>
        <style>
        /* Target Outlook 2007 and 2010 */
        </style>
    <![endif]-->
</head>
<body>
    <div style="background-color:#e8e8e8">
        <center>
        <table align="center" style="width:90%;max-width:600px;margin:20px auto;border-radius:7px;border-spacing:0;border-collapse:collapse">
            <tbody style="border-spacing:0;border-collapse:collapse">
            <tr style="height:23px;overflow:hidden">
                <td style="height:23px;overflow:hidden;background:#2ab8e7;border-radius:6px 6px 0 0;padding:0;margin:0">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:auto;padding:20px 60px 15px;background:#fff;border-bottom:3px solid #dedede">
                    <h1 style="margin:0;font-size:30px;font-family:'Trebuchet MS';line-height:1.1em">
                        <!--<img src="{$siteUrl}myMovies.jpg" alt="MyMovies">-->
                        <img src="http://i57.tinypic.com/qzewiq.jpg" />
                    </h1>
                </td>
            </tr>
            <tr>
                <td style="background:#fff;font-size:16px;font-family:'Open Sans',arial,sans-serif;padding:15px 60px;color:#7d7878">
                    <p>Great news! You've successfully signed up at <a href="{$siteUrl}letMeWatchThis.php">MyMovies</a></p>
                    <p>Please <a href="{$confLink}" style="color:#2ab8e7;font-weight:bold" target="_blank">CLICK HERE</a> to activate your account.</p>
                    <p>Thank You,<br> 
                    MyMovies.com<br>
                    <a href="mailto:webspheresolutions@gmail.com" style="color:#7d7878;text-decoration:none" target="_blank">mymovies@gmail.com</a></p>
                </td>
            </tr>        
            <tr>
                <td style="background:#bdbdbd;padding:10px 60px;font-size:12px;font-family:'Open Sans',arial,sans-serif;color:#000;border-radius:0 0 6px 6px">
                    © 2014 MyMovies, All rights reserved.  |  <a href="tel:x-xxx-xxx-xxxx" value="+xxxxxxxx" target="_blank">x-xxx-xxx-xxxx</a> | <a style="color:#000;text-decoration:none" href="{$siteUrl}letMeWatchThis.php' ?>" target="_blank">Support Center</a>
                </td>
            </tr>
            </tbody>
        </table>
        </center>
    </div>
</body>
</html>