<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

namespace app\controller;

use support\Request;
use think\facade\Db;
use Workerman\Coroutine;
use Workerman\Timer;


class InstallController
{
    // 显示安装页面
    public function index()
    {
        return view('install/index', []);
    }

    public function Installed()
    {
        return view('install/installed', []);
    }

    // 检查是否已安装
    public function checkInstall()
    {
        // 检查是否存在Install.txt文件
        if (file_exists(base_path() . '/Install.txt')) {
            return json([
                'code' => 0,
                'message' => '系统已安装'
            ]);
        }
        return json([
            'code' => 1,
            'message' => '系统未安装'
        ]);
    }
    
    // 检查环境
    public function checkEnv()
    {
        $env = [];
        
        // 检查PHP版本
        $env['php_version'] = [
            'status' => version_compare(PHP_VERSION, '8.1', '>='),
            'current' => PHP_VERSION,
            'required' => '>= 8.1'
        ];
        
        // 检查扩展
        $extensions = ['curl', 'pdo', 'pdo_mysql', 'mbstring', 'redis', 'bcmath'];
        foreach ($extensions as $ext) {
            $env['extension_' . $ext] = [
                'status' => extension_loaded($ext),
                'name' => $ext
            ];
        }
        
        // 检查目录权限
        $directories = [
            base_path() . '/Install.txt' => 'w',
            base_path() . 'runtime' => 'wrx'
        ];
        
        foreach ($directories as $dir => $perm) {
            if (file_exists($dir)) {
                $env['perm_' . md5($dir)] = [
                    'status' => is_writable($dir) && is_readable($dir),
                    'path' => $dir,
                    'required' => $perm
                ];
            } else {
                $env['perm_' . md5($dir)] = [
                    'status' => is_writable(dirname($dir)),
                    'path' => $dir,
                    'required' => $perm
                ];
            }
        }
        
        // 检查所有项是否通过
        $allPass = true;
        foreach ($env as $item) {
            if (!$item['status']) {
                $allPass = false;
                break;
            }
        }
        
        return json([
            'code' => $allPass ? 0 : 1,
            'data' => $env
        ]);
    }
    
    // 测试数据库连接
    public function testDbConnection(Request $request)
    {
        try {
            // 获取请求数据并确保是数组
            $data = $request->post();
            if (!is_array($data)) {
                return json([
                    'code' => 1,
                    'message' => '无效的请求数据格式'
                ]);
            }
            
            // 验证必要参数
            $requiredFields = ['hostname', 'hostport', 'database', 'username', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return json([
                        'code' => 1,
                        'message' => '缺少必要参数: ' . $field
                    ]);
                }
            }
            
            // 提取并清理数据库连接参数
            $hostname = (string)trim($data['hostname']);
            $port     = (int)$data['hostport'];
            $database = (string)trim($data['database']);
            $username = (string)trim($data['username']);
            $password = (string)trim($data['password']);
            
            // 使用原生PDO直接连接，避免ORM层可能的数组到字符串转换问题
            $dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            // 尝试建立连接
            $pdo = new \PDO($dsn, $username, $password, $options);
            
            // 执行测试查询
            $stmt = $pdo->query('SELECT 1');
            $result = $stmt->fetchColumn();
            
            return json([
                'code' => 0,
                'message' => '数据库连接成功'
            ]);
        } catch (\PDOException $e) {
            // 特定处理PDO异常
            return json([
                'code' => 1,
                'message' => '数据库连接失败: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        } catch (\Error $e) {
            // 处理PHP错误
            return json([
                'code' => 1,
                'message' => '系统错误: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        } catch (\Exception $e) {
            // 处理其他异常
            return json([
                'code' => 1,
                'message' => '连接异常: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        }
    }
    
    /**
     * 清理错误消息，处理可能的数组类型
     */
    private function sanitizeErrorMessage($message)
    {
        if (is_array($message)) {
            return json_encode($message, JSON_UNESCAPED_UNICODE);
        }
        return (string)$message;
    }
    
    // 执行安装
    public function install(Request $request)
    {
        try {
            // 获取请求数据并确保是数组
            $data = $request->post();
            if (!is_array($data)) {
                return json([
                    'code' => 1,
                    'message' => '无效的请求数据格式'
                ]);
            }
            
            // 1. 验证必要参数
            $requiredFields = ['hostname', 'username', 'database', 'password', 'admin_name', 'admin_password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return json([
                        'code' => 1,
                        'message' => '缺少必要参数: ' . $field
                    ]);
                }
            }
            
            // 2. 提取并清理数据库连接参数
            $hostname = (string)trim($data['hostname']);
            $port     = (int)($data['hostport'] ?? 3306);
            $database = (string)trim($data['database']);
            $username = (string)trim($data['username']);
            $dbPassword = (string)($data['password'] ?? '');
            // 使用专门的admin_password字段
            $adminPassword = (string)trim($data['admin_password']);
            
            // 确保管理员密码不为空
            if (empty($adminPassword)) {
                return json([
                    'code' => 1,
                    'message' => '管理员密码不能为空'
                ]);
            }
            

            
            // 4. 导入SQL文件
            $sqlFile = base_path() . '/iot.sql';
            if (!file_exists($sqlFile)) {
                throw new \Exception('SQL文件不存在：'.$sqlFile);
            }
            
            // 添加连接前的参数验证
            if (empty($username)) {
                throw new \Exception('数据库用户名不能为空');
            }
            
            // 使用原生PDO连接数据库
            $dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            // 添加连接调试信息
            try {
                $pdo = new \PDO($dsn, $username, $dbPassword, $options);
                // 连接成功后执行简单查询验证
                $pdo->query('SELECT 1');
            } catch (\PDOException $e) {
                // 提供更详细的数据库连接错误信息
                throw new \PDOException(
                    '数据库连接失败: ' . $e->getMessage() . 
                    '。请检查主机名、端口、数据库名、用户名和密码是否正确，以及MySQL服务是否正常运行。',
                    $e->getCode(),
                    $e
                );
            }
            
            // 读取并执行SQL文件
            $sqlContent = file_get_contents($sqlFile);
            $sqlArray = $this->splitSql($sqlContent);
            
            // 直接执行SQL，不使用事务
            try {
                foreach ($sqlArray as $sql) {
                    $sql = trim($sql);
                    if ($sql) {
                        $pdo->exec($sql);
                    }
                }
                
                // 5. 更新管理员账号 - 使用正确的admin_password字段
                $adminName = (string)trim($data['admin_name']);
                $adminPhone = (string)($data['admin_phone'] ?? '');
                $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
                
                // 检查admin表是否存在
                $tableExists = $pdo->query("SHOW TABLES LIKE 'kd_admin'")->rowCount() > 0;
                if (!$tableExists) {
                    throw new \Exception('管理员表不存在，请检查SQL文件是否正确导入');
                }
                
                // 检查是否有管理员记录
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM kd_admin WHERE id = 1");
                $stmt->execute();
                $adminExists = $stmt->fetchColumn() > 0;
                
                if ($adminExists) {
                    // 更新现有管理员
                    $stmt = $pdo->prepare("UPDATE kd_admin SET name = :name, phone = :phone, password = :password WHERE id = 1");
                } else {
                    // 插入新管理员
                    $stmt = $pdo->prepare("INSERT INTO kd_admin (id, name, phone, password) VALUES (1, :name, :phone, :password)");
                }
                
                $stmt->execute([
                    ':name' => $adminName,
                    ':phone' => $adminPhone,
                    ':password' => $passwordHash
                ]);

                // 创建Install.txt文件用于标记安装完成
                $installFilePath = base_path() . '/Install.txt';
                
                // 检查文件写入权限
                if (!is_writable(dirname($installFilePath))) {
                    throw new \Exception('Install.txt文件目录没有写入权限');
                }
                
                // 创建Install.txt文件内容，包含数据库配置信息
                $installContent = <<<EOT
1
EOT;
                file_put_contents($installFilePath, $installContent);
                
                // 同时保存.env配置（保持系统正常运行）
                Coroutine::create(function() use ($hostname,$database,$username,$dbPassword,$port){
                    Timer::sleep(3);
                    $REDIS_HOST=getenv('REDIS_HOST')?? "127.0.0.1" ;
                    $REDIS_PWD=getenv('REDIS_PWD')?? "";
                    $REDIS_PORT=getenv('REDIS_PORT')?? 6379;
                    $REDIS_SELECT=getenv('REDIS_SELECT')?? 0;
                    $envContent = <<<EOT
REDIS_HOST = {$REDIS_HOST}
REDIS_PORT = {$REDIS_PWD}
REDIS_PWD = {$REDIS_PORT}
REDIS_SELECT = {$REDIS_SELECT}

TYPE = mysql
HOSTNAME = {$hostname}
DATABASE = {$database}
USERNAME = {$username}
PASSWORD = {$dbPassword}
HOSTPORT = {$port}
CHARSET = utf8
PREFIX = kd_

#证书
ssl_cert = 
ssl_key = 

#第三方域名
PASS_DOMAIN = https://cloud.test.farmkd.com
EOT;

                    $envPath = base_path() . '/.env';
                    if (is_writable(dirname($envPath))) {
                        file_put_contents($envPath, $envContent);
                    }
                });
                return json([
                    'code' => 0,
                    'message' => '安装成功',
                    'data' => [
                        'url' => '/admin/login'
                    ]
                ]);
            } catch (\Exception $e) {
                return json([
                    'code' => 1,
                    'message' => $this->sanitizeErrorMessage($e->getMessage())
                ]);
            }
        } catch (\PDOException $e) {
            return json([
                'code' => 1,
                'message' => '数据库操作失败: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        } catch (\Error $e) {
            return json([
                'code' => 1,
                'message' => '系统错误: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'message' => '安装失败: ' . $this->sanitizeErrorMessage($e->getMessage())
            ]);
        }
    }
    
    // 分割SQL语句
    private function splitSql($sql)
    {
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $ret = [];
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        foreach ($queriesarray as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $ret[$num] = $query . ';';
                $num++;
            }
        }
        return $ret;
    }
}
