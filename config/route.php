<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use app\controller\admin\LoginController;
use app\controller\admin\gateway\GatewayController;
use app\controller\admin\gateway\MarqueController;
use app\controller\admin\label\LabelController;
use app\controller\admin\device\DeviceTemplateController;
use app\controller\admin\device\DeviceController;
use app\controller\admin\alarm\TemplateTriggerController;
use app\controller\admin\alarm\IndependenceTriggerController;
use app\controller\admin\data\LogController;
use app\controller\admin\data\StatisticsController;
use app\controller\admin\product\ProductController;
use app\controller\admin\monitor\cloud\AuthorizeController;
use \app\controller\admin\system\ConfigController;
use app\controller\admin\monitor\MonitorController;
use app\controller\admin\user\AdminController;
use app\controller\admin\data\OverviewController;
use Webman\Route;

Route::group('/admin', function () {
    Route::any('/login', [LoginController::class, 'login']);

    //需要登陆的
    Route::group('', function () {


        Route::group('/user', function () {
            //用户管理
            Route::any('/getAdminList', [AdminController::class, 'getAdminList']);
            Route::any('/saveAdmin', [AdminController::class, 'saveAdmin']);
            Route::any('/getAdminInfo', [AdminController::class, 'getAdminInfo']);
            Route::any('/delAdmin', [AdminController::class, 'delAdmin']);

        });
        Route::group('/alarm', function () {
            //模版触发器
            Route::any('/getTemplateTriggerList', [TemplateTriggerController::class, 'getTemplateTriggerList']);
            Route::any('/getTemplateTriggerInfo', [TemplateTriggerController::class, 'getTemplateTriggerInfo']);
            Route::any('/saveTemplateTrigger', [TemplateTriggerController::class, 'saveTemplateTrigger']);
            Route::any('/saveTemplateTriggerStatus', [TemplateTriggerController::class, 'saveTemplateTriggerStatus']);
            Route::any('/delTemplateTrigger', [TemplateTriggerController::class, 'delTemplateTrigger']);
            //独立触发器
            Route::any('/getIndependenceTriggerList', [IndependenceTriggerController::class, 'getIndependenceTriggerList']);
            Route::any('/getIndependenceTriggerInfo', [IndependenceTriggerController::class, 'getIndependenceTriggerInfo']);
            Route::any('/saveIndependenceTrigger', [IndependenceTriggerController::class, 'saveIndependenceTrigger']);
            Route::any('/saveIndependenceTriggerStatus', [IndependenceTriggerController::class, 'saveIndependenceTriggerStatus']);
            Route::any('/delIndependenceTrigger', [IndependenceTriggerController::class, 'delIndependenceTrigger']);
        });
        Route::group('/device', function () {
            //设备
            Route::any('/getDeviceList', [DeviceController::class, 'getDeviceList']);
            Route::any('/getDeviceInfo', [DeviceController::class, 'getDeviceInfo']);
            Route::any('/saveDevice', [DeviceController::class, 'saveDevice']);
            Route::any('/delDevice', [DeviceController::class, 'delDevice']);
            Route::any('/getVariable', [DeviceController::class, 'getVariable']);
            Route::any('/getDeviceData', [DeviceController::class, 'getDeviceData']);
            Route::any('/setDeviceData', [DeviceController::class, 'setDeviceData']);
            Route::any('/getVariableLog', [DeviceController::class, 'getVariableLog']);
            Route::any('/getStatisticalChart', [DeviceController::class, 'getStatisticalChart']);

            //设备模版
            Route::any('/getDeviceTemplateList', [DeviceTemplateController::class, 'getDeviceTemplateList']);
            Route::any('/getDeviceTemplateInfo', [DeviceTemplateController::class, 'getDeviceTemplateInfo']);
            Route::any('/saveDeviceTemplate', [DeviceTemplateController::class, 'saveDeviceTemplate']);
            Route::any('/delDeviceTemplate', [DeviceTemplateController::class, 'delDeviceTemplate']);

        });
        Route::group('/gateway', function () {
            //网关
            Route::any('/getGatewayList', [GatewayController::class, 'getGatewayList']);
            Route::any('/saveGateway', [GatewayController::class, 'saveGateway']);
            Route::any('/getGatewayInfo', [GatewayController::class, 'getGatewayInfo']);
            Route::any('/delGateway', [GatewayController::class, 'delGateway']);
            Route::any('/getMac', [GatewayController::class, 'getMac']);
            Route::any('/sendAtInstruction', [GatewayController::class, 'sendAtInstruction']);
            Route::any('/getGatewayLog', [GatewayController::class, 'getGatewayLog']);
            //网关型号
            Route::any('/getMarqueList', [MarqueController::class, 'getMarqueList']);
            Route::any('/getMarqueInfo', [MarqueController::class, 'getMarqueInfo']);
            Route::any('/saveMarque', [MarqueController::class, 'saveMarque']);
            Route::any('/delMarque', [MarqueController::class, 'delMarque']);
            Route::any('/getInstruct', [MarqueController::class, 'getInstruct']);
        });
        Route::group('/label', function () {//标签
            //分类
            Route::any('/getLabelCategoryList', [LabelController::class, 'getLabelCategoryList']);
            Route::any('/getLabelCategoryInfo', [LabelController::class, 'getLabelCategoryInfo']);
            Route::any('/saveLabelCategory', [LabelController::class, 'saveLabelCategory']);
            Route::any('/delLabelCategory', [LabelController::class, 'delLabelCategory']);
            //标签
            Route::any('/getLabelList', [LabelController::class, 'getLabelList']);
            Route::any('/getLabelInfo', [LabelController::class, 'getLabelInfo']);
            Route::any('/saveLabel', [LabelController::class, 'saveLabel']);
            Route::any('/delLabel', [LabelController::class, 'delLabel']);
        });
        Route::group('/data', function () {//数据中心
            //日志
            Route::any('/gatewayOnlineLog', [LogController::class, 'gatewayOnlineLog']);
            Route::any('/deviceOnlineLog', [LogController::class, 'deviceOnlineLog']);
            Route::any('/warningLog', [LogController::class, 'warningLog']);
            Route::any('/dealWarningLog', [LogController::class, 'dealWarningLog']);
            Route::any('/delWarningLog', [LogController::class, 'delWarningLog']);
            Route::any('/linkageLog', [LogController::class, 'linkageLog']);
            Route::any('/delLinkageLog', [LogController::class, 'delLinkageLog']);

            //统计
            Route::any('/deviceStatusStatistics', [StatisticsController::class, 'deviceStatusStatistics']);
            Route::any('/deviceLabelStatistics', [StatisticsController::class, 'deviceLabelStatistics']);
            Route::any('/gatewayStatusStatistics', [StatisticsController::class, 'gatewayStatusStatistics']);
            Route::any('/getDeviceWarningRecord', [StatisticsController::class, 'getDeviceWarningRecord']);
            Route::any('/newDeviceStatistics', [StatisticsController::class, 'newDeviceStatistics']);
            Route::any('/newGatewayStatistics', [StatisticsController::class, 'newGatewayStatistics']);
            //总览
            Route::any('/generalSituation', [OverviewController::class, 'generalSituation']);

        });
        Route::group('/product', function () {//产品库
            //产品库
            Route::any('/getProductList', [ProductController::class, 'getProductList']);
            Route::any('/getProductInfo', [ProductController::class, 'getProductInfo']);
            Route::any('/updateStatus', [ProductController::class, 'updateStatus']);
            Route::any('/saveProduct', [ProductController::class, 'saveProduct']);
            Route::any('/delProduct', [ProductController::class, 'delProduct']);
        });
        Route::group('/system', function () {//系统配置
            Route::any('/getConfig', [ConfigController::class, 'getConfig']);
            Route::any('/setConfig', [ConfigController::class, 'setConfig']);
        });

        Route::group('/authorize', function () {//云端授权
            Route::any('/kunDianAuthorize', [AuthorizeController::class, 'kunDianAuthorize']);
            Route::any('/kunDianSmsLoginCode', [AuthorizeController::class, 'kunDianSmsLoginCode']);
        });

        Route::group('/monitor', function () {//监控
            Route::any('/addMonitor', [MonitorController::class, 'addMonitor']);
            Route::any('/updateMonitor', [MonitorController::class, 'updateMonitor']);
            Route::any('/getMonitorList', [MonitorController::class, 'getMonitorList']);
            Route::any('/getMonitorInfo', [MonitorController::class, 'getMonitorInfo']);
            Route::any('/delMonitor', [MonitorController::class, 'delMonitor']);
            Route::any('/getDeviceChannels', [MonitorController::class, 'getDeviceChannels']);
            Route::any('/snap', [MonitorController::class, 'snap']);
            Route::any('/snapshots', [MonitorController::class, 'snapshots']);
            Route::any('/delSnapshots', [MonitorController::class, 'delSnapshots']);
            Route::any('/deviceStart', [MonitorController::class, 'deviceStart']);
            Route::any('/deviceStop', [MonitorController::class, 'deviceStop']);
            Route::any('/deviceControl', [MonitorController::class, 'deviceControl']);
            Route::any('/devicePresets', [MonitorController::class, 'devicePresets']);
            Route::any('/devicePresetsList', [MonitorController::class, 'devicePresetsList']);
            Route::any('/startRecording', [MonitorController::class, 'startRecording']);
            Route::any('/stopRecording', [MonitorController::class, 'stopRecording']);
            Route::any('/getRecordingList', [MonitorController::class, 'getRecordingList']);
            Route::any('/delRecording', [MonitorController::class, 'delRecording']);
            Route::any('/updateDevice', [MonitorController::class, 'updateDevice']);
            Route::any('/namespacesInfo', [MonitorController::class, 'namespacesInfo']);
            Route::any('/saveMonitorAuto', [MonitorController::class, 'saveMonitorAuto']);
            Route::any('/getMonitorAuto', [MonitorController::class, 'getMonitorAuto']);
        });
    })->middleware([
        \app\middleware\AuthToken::class,
    ]);

    // 安装路由
    Route::any('/install', [\app\controller\InstallController::class, 'index']);
    Route::any('/install/index', [\app\controller\InstallController::class, 'index']);
    Route::any('/install/checkInstall', [\app\controller\InstallController::class, 'checkInstall']);
    Route::any('/install/checkEnv', [\app\controller\InstallController::class, 'checkEnv']);
    Route::any('/install/testDbConnection', [\app\controller\InstallController::class, 'testDbConnection']);
    Route::any('/install/install', [\app\controller\InstallController::class, 'install']);
    Route::any('/install/Installed', [\app\controller\InstallController::class, 'Installed']);
})->middleware([
    \app\middleware\AccessControl::class,
]);





