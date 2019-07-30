<?php
//$site = 'http://reviews24.net';
$site = \URL::to('/');

$shadow = $site.'/images/shadow.png';
$bg = $site.'/images/bg.png';
$imgopen_url = $site.'/emailopen/'.$ei_key.'/'.$lead_key;

?>
<table width="614" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#656565;background: #FDFDFD;border: 1px solid #D6D5D5;-webkit-border-radius: 8px;-moz-border-radius: 8px;border-radius: 8px;-webkit-box-shadow: 0px -1px 5px #DDD;-moz-box-shadow: 0px -1px 3px #DDD;box-shadow: 0px -1px 5px #DDD;">
    <tbody>
    <tr>
        <td style="border-radius: 8px 8px 0 0;
				color: #fff;
				background: -moz-linear-gradient(top, rgba(252,252,252,1) 0%, rgba(241,241,241,1) 67%, rgba(239,239,239,1) 93%, rgba(238,238,238,1) 99%, rgba(235,235,235,0.93) 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(252,252,252,1)), color-stop(67%,rgba(241,241,241,1)), color-stop(93%,rgba(239,239,239,1)), color-stop(99%,rgba(238,238,238,1)), color-stop(100%,rgba(235,235,235,0.93))); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top, rgba(252,252,252,1) 0%,rgba(241,241,241,1) 67%,rgba(239,239,239,1) 93%,rgba(238,238,238,1) 99%,rgba(235,235,235,0.93) 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top, rgba(252,252,252,1) 0%,rgba(241,241,241,1) 67%,rgba(239,239,239,1) 93%,rgba(238,238,238,1) 99%,rgba(235,235,235,0.93) 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top, rgba(252,252,252,1) 0%,rgba(241,241,241,1) 67%,rgba(239,239,239,1) 93%,rgba(238,238,238,1) 99%,rgba(235,235,235,0.93) 100%); /* IE10+ */
				background: linear-gradient(to bottom, rgba(252,252,252,1) 0%,rgba(241,241,241,1) 67%,rgba(239,239,239,1) 93%,rgba(238,238,238,1) 99%,rgba(235,235,235,0.93) 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfcfc', endColorstr='#edebebeb',GradientType=0 ); /* IE6-9 */
				position: relative;
			">
            <a href="{{$site}}" target="_blank" style="display: inline-block; padding: 10px 15px; font-size: 20px; color: #337ab7;">
                <?php if(isset($company_logo) && $company_logo !== '') { ?>
                    <img src="<?=$company_logo;?>" style="width: 90px; max-width: 100%; height: auto;" />
                <?php } else { ?>
                    <strong>LeadsApp</strong>
                <?php } ?>
            </a>
            <img src="{{$imgopen_url}}" width="1" height="1" alt=""/>
        </td>
    </tr>
    <tr>
        <td style="line-height: 25px; height: 25px; background:#eee url(<?=$shadow;?>) repeat-x;">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding:0 18px 10px; background: #eee url(<?=$bg;?>) repeat;">
            <table width="554" border="0" align="center" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="border:1px solid #d3dde2;background:#fff;padding:15px;-moz-border-radius: 8px;-webkit-border-radius: 8px; border-radius: 8px;">
                        <table width="524" border="0" cellspacing="0" cellpadding="0" style="font-family:arial,pmingliu,helvetica,sans-serif;font-size:12px;color:#000">
                            <tbody>
                            <tr><td>
                                    <div style="clear: both; font-family:Arial,Helvetica,sans-serif;">
                                        {!! $content !!}
                                    </div>
                                </td></tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="
					font-size:11px;
					line-height:16px;
					padding:5px;
					text-align:center;
					border-radius: 0 0 8px 8px;
					background-color: #161615;
					border-top: 3px solid #383A3D;
					color: #fff;"
        >

        </td>
    </tr>
    </tbody>
</table>
