<?php
/*
 * @Author: your name
 * @Date: 2022-02-06 08:41:47
 * @LastEditTime: 2023-04-19 14:18:39
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @Description: 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 * @FilePath: \tp6\think\app\common\Excels.php
 */

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
