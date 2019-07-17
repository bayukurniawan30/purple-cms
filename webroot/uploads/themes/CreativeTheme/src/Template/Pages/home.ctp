<?php
    $replaceFunction = $this->Purple->getAllFuncInHtml($homepage);
    if ($replaceFunction == false) {
        echo $homepage;
    }
    else {
        $i = 1;
        foreach ($replaceFunction as $data):
            $functionName = trim(str_replace('function|', '', $data));
            if ($i == 1) {
                $html = str_replace('{{function|'.$functionName.'}}', $themeFunction->$functionName(), $homepage);
            }
            else {
                $html = str_replace('{{function|'.$functionName.'}}', $themeFunction->$functionName(), $html);
            }
            $i++;
        endforeach;

        echo $html;
    }
?>