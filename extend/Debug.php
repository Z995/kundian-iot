<?php


namespace extend;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Debug
 */
class Debug
{
    //输出html
    public static function toHtml($data)
    {
        ob_start();
        VarDumper::setHandler(function ($var) {
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();
            $dumper->dump($cloner->cloneVar($var));
        });
        VarDumper::dump($data);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
