#!/usr/bin/env php
<?php
include_once "./lib/phpQuery.php";

date_default_timezone_set('Asia/Taipei');

$intStartDay = strtotime('20131224');
$imtLimitDay = strtotime('20140323');

while ($intStartDay <= $imtLimitDay)
{
    $intDay   = date('Ymd', $intStartDay);

    $strQuote = getDailyQuote($intDay);

    if($strQuote)
    {
        echo $strQuote;
    }

    sleep(1);

    $intStartDay = $intStartDay + (24*60*60);
}



function getDailyQuote($day)
{
    $url = "www.appledaily.com.tw/index/dailyquote/date/".$day;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //curl_setopt($ch, CURLOPT_USERAGENT, "");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $get_page = curl_exec($ch);
    curl_close($ch);
    //echo $get_page;


    $php_dom    = phpQuery::newDocumentHTML($get_page) ;
    $dom['h1']  = ".dphs>p" ;
    $dom['h2']  = ".dphs>h1" ;

    $dat['h1'] = $php_dom->find($dom['h1'])->eq(0)->text() ;
    $dat['h1'] = trim($dat['h1']) ;

    $dat['h2'] = $php_dom->find($dom['h2'])->eq(0)->text() ;
    $dat['h2'] = trim($dat['h2']) ;
    $dat['h2'] = substr($dat['h2'],0,-8) ;
    //$dat['h2'] = str_replace(' ',' - ',$dat['h2']) ;

    $dat['time'] = $day ;

    $isVaild = true ;

    if($dat['h1'] == '每日一句停刊一天')
    {
        $isVaild = false ;
    }

    if($isVaild)
    {
        $strOutput = sprintf($strDefaultSQL 
                            ,$dat['time'] 
                            ,addslashes($dat['h1'])
                            ,addslashes($dat['h2']));

        return $strOutput ;
    }
    else
    {
        return false ;
    }

}

