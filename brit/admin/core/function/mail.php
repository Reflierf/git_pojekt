<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

function html_mail($to, $subject, $body) {
    $date_now = date("Y-m-d");
    $text = $body;
    $message = '
    <!DOCTYPE HTML>
    <html>
    <head>
    	<meta http-equiv="content-type" content="text/html" />
        <meta charset="utf-8"/>
    	<meta name="author" content="Freiler Béla" />
        
        <style>
            * {
                margin: 0;
                border: 0;
                padding: 0;
            }
            
            a, a:link {
                color: #fbeebb;
                text-decoration: none;
            }
            
            table {
                width: 700px;
                margin: 0 auto;
                text-align: center;
                background: #000;
            }
            
            .email_top_margo {
                height: 20px;
            }
            
            .midle_margin {
                width: 20px;
            }
            
            .center_cell {
                width: 660px;
                height: 50px;
            }
            
            .body {
                font-size: 14pt;
            }
            
            .message_table, .footer_table {
                width: 660px;
            }
            
            .line1, .line2 {
                    background: #7a0026;
                    height: 4px;
            }
            
            /*.line1 {
                
                top: -3px;
            }
            
            .line2 {
                top: 3px;
            }*/
            
            .footer_table {
                margin-top: 5px;
            }
            
            .footer_table .x18{
                width: 82px;
            }
            
            .footer_table .warning_text {
                font-size: 10px;
            }
            
        </style>
        
    </head>
    
    <body>
    
    <table cellpadding="0" cellspacing="0" border="0" align="center">
        <tr><!-- felső margó -->
            <td class="email_top_margo" colspan="3" cellpadding="0" cellspacing="0" border="0"></td>
        </tr>
        <tr><!-- Üzenet terület -->
            <td class="midle_margin"></td>
            <td class="center_cell">
                <table class="message_table"> <!--  style="border: 1px solid #fff;" -->
                    <tr>
                        <td align="center">
                            <a href="http://localhost/brit/index.php">
                                <img src="http://localhost/brit/img/logo_racy_2.png" width="660" border="0" style="display:block" alt="Logo"/>
                            </a>
                        </td>
                    </tr>
                     <tr>
                        <td class="line1" width="660" style="position: relative; top: 3px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #fff; margin: 5px auto 10px; font-size: 20pt; font-weight: bold; font-style: italic;">'.$subject.'</p>
                        </td>
                    </tr>
                    <tr>
                        
                        <td class="body" style="color: #fffdea; margin: 10px 0;">'.$text.'</td>
                        
                    </tr>
                    <tr>
                        <td class="line2" width="660" style="position: relative; top: 3px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table class="footer_table" style="color: #fff;">
                                <tr>
                                    <td class="x18"><img src="http://localhost/brit/img/18.jpg" /></td>
                                    <td class="warning_text"colspan="2">
                                    <p>
                                        Figyelem!
                                    </p>
                                    <p>Felnőtt tartalom, megtekintés csak tizennyolc éven felülieknek.</p>
                                    <p>&copf; 2017 Ref, Minden jog fentartva! A hivatkozott valamennyi védjegy a megfelelő tulajdonosok tulajdonát képezi.</p>
                                    <p>Ha már nem szeretne <i>Britannys Bod Fan Site</i> hírleveleket vagy a reklámozott termékekkel kapcsolatos információkat kérni, 
                                    kérjük, válaszoljon erre az e-mailre az e-mail szövegének "Leiratkozás" feliratával. 
                                    Alternatív megoldásként <a href="#">kattintson ide</a> a beállítások kezeléséhez.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="midle_margin"></td>
        </tr>
        <tr><!-- alsó margó -->
            <td class="email_top_margo" colspan="3" cellpadding="0" cellspacing="0" border="0" align="center"></td>
        </tr>
    </table>
    
    </body>
    </html>
    ';
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    
    // Additional headers
    //$headers .= 'To: Mary <ref@freemail.hu>, Kelly <ref68@upcmail.hu>, Bela Pornerastic <terb98@gmail.com>' . "\r\n";
    $headers .= 'From: Britannys Bod Fan Site <terb98@gmail.com>' . "\r\n";
    
    // Mail it
    mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers);
    //echo 'elküldve.';
}

?>