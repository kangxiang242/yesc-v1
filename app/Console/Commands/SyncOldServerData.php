<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOldServerData extends Command
{
    protected $signature = 'sync:old-server
        {--from= : 起始日期 (Y-m-d)，默认本月1号}
        {--to= : 结束日期 (Y-m-d)，默认今天}
        {--dry-run : 只预览，不写入}
        {--only= : 只同步指定类型: orders,messages,all}';

    protected $description = '从旧生产服务器 45.148.120.210 同步本月订单和留言数据';

    private const OLD_SSH_HOST = '45.148.120.210';
    private const SSH_KEY = '/Users/a123/workspace/wwwroot/hk-server-keys/deploy_key';
    private const SSH_USER = 'root';
    private const OLD_DB_HOST = '127.0.0.1';
    private const OLD_DB_PORT = '51316';
    private const OLD_DB_USER = 'root';
    private const OLD_DB_PASS = '3ns7jtwh';
    private const OLD_DB_NAME = 'yescialis';

    public function handle(): int
    {
        $from = $this->option('from') ?: date('Y-m-01');
        $to = $this->option('to') ?: date('Y-m-d');
        $only = $this->option('only') ?: 'all';
        $dryRun = $this->option('dry-run');

        $this->info("同步范围: {$from} ~ {$to}");
        $this->newLine();

        $synced = ['orders' => 0, 'messages' => 0];

        if ($only === 'all' || $only === 'orders') {
            $count = $this->syncOrders($from, $to, $dryRun);
            $synced['orders'] = $count;
        }

        if ($only === 'all' || $only === 'messages') {
            $count = $this->syncMessages($from, $to, $dryRun);
            $synced['messages'] = $count;
        }

        $this->newLine();
        $this->info(sprintf(
            '同步完成。订单: %d 笔, 留言: %d 条%s',
            $synced['orders'],
            $synced['messages'],
            $dryRun ? ' (DRY-RUN)' : ''
        ));

        return self::SUCCESS;
    }

    /**
     * 通过 SSH 执行 SQL 查询，返回结果数组
     */
    private function queryOldDb(string $sql): array
    {
        $escapedSql = str_replace(['"', '$', '`', '\\'], ['\"', '\$', '\`', '\\\\'], $sql);
        $sshCmd = sprintf(
            'ssh -o StrictHostKeyChecking=no -i %s %s@%s ' .
            '"mysql --default-character-set=utf8mb4 -h%s -P%s -u%s -p%s %s -B -N -e \"%s\" 2>/dev/null"',
            escapeshellarg(self::SSH_KEY),
            self::SSH_USER,
            escapeshellarg(self::OLD_SSH_HOST),
            self::OLD_DB_HOST,
            self::OLD_DB_PORT,
            self::OLD_DB_USER,
            self::OLD_DB_PASS,
            self::OLD_DB_NAME,
            $escapedSql
        );

        $output = shell_exec($sshCmd);
        if ($output === null || $output === '') {
            return [];
        }

        $lines = explode("\n", trim($output));
        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $rows[] = str_getcsv($line, "\t");
        }
        return $rows;
    }

    /**
     * 以 JSON 行格式查询（处理 JSON 类型字段）
     */
    private function queryOldDbJson(string $sql): array
    {
        $escapedSql = str_replace(['"', '$', '`', '\\'], ['\"', '\$', '\`', '\\\\'], $sql);
        $sshCmd = sprintf(
            'ssh -o StrictHostKeyChecking=no -i %s %s@%s ' .
            '"mysql --default-character-set=utf8mb4 -h%s -P%s -u%s -p%s %s -B -N -e \"%s\" 2>/dev/null"',
            escapeshellarg(self::SSH_KEY),
            self::SSH_USER,
            escapeshellarg(self::OLD_SSH_HOST),
            self::OLD_DB_HOST,
            self::OLD_DB_PORT,
            self::OLD_DB_USER,
            self::OLD_DB_PASS,
            self::OLD_DB_NAME,
            $escapedSql
        );

        $output = shell_exec($sshCmd);
        if ($output === null || trim($output) === '') {
            return [];
        }

        $lines = explode("\n", trim($output));
        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $rows[] = json_decode($line, true);
        }
        return array_filter($rows);
    }

    /**
     * 同步订单
     */
    private function syncOrders(string $from, string $to, bool $dryRun): int
    {
        $this->info('--- 同步订单 ---');

        // 获取本地已有订单号
        $localNos = Order::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->pluck('no')
            ->toArray();
        $localNoSet = array_flip($localNos);
        $this->line("本地本月已有订单号: " . count($localNos));

        // 查询旧库订单（JSON格式输出，处理shop_data）
        $sql = sprintf(
            "SELECT JSON_OBJECT("
            . "'no', o.no, "
            . "'inside_no', o.inside_no, "
            . "'total_price', o.total_price, "
            . "'product_price', o.product_price, "
            . "'freight', o.freight, "
            . "'delivery_type', o.delivery_type, "
            . "'payment_type', o.payment_type, "
            . "'name', o.name, "
            . "'phone', o.phone, "
            . "'email', o.email, "
            . "'country', o.country, "
            . "'province', o.province, "
            . "'city', o.city, "
            . "'county', o.county, "
            . "'street', o.street, "
            . "'address', o.address, "
            . "'delivery_time', o.delivery_time, "
            . "'status', o.status, "
            . "'remarks', o.remarks, "
            . "'admin_remarks', o.admin_remarks, "
            . "'ip', o.ip, "
            . "'ipcountry', o.ipcountry, "
            . "'user_agent', o.user_agent, "
            . "'shop_no', o.shop_no, "
            . "'shop_name', o.shop_name, "
            . "'shop_type', o.shop_type, "
            . "'shop_data', o.shop_data, "
            . "'created_at', o.created_at, "
            . "'updated_at', o.updated_at"
            . ") "
            . "FROM orders o "
            . "WHERE o.created_at >= '%s 00:00:00' AND o.created_at <= '%s 23:59:59'",
            $from, $to
        );

        $oldOrders = $this->queryOldDbJson($sql);
        $this->line("旧库本月订单: " . count($oldOrders));

        if (empty($oldOrders)) {
            $this->warn('无待同步订单');
            return 0;
        }

        // 同步订单
        $synced = 0;
        foreach ($oldOrders as $row) {
            $no = $row['no'] ?? '';
            if (isset($localNoSet[$no])) {
                $this->line("  跳过已有订单: {$no}");
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] 将同步订单: {$no} - {$row['name']}");
                $synced++;
                continue;
            }

            try {
                DB::beginTransaction();

                // 查询旧订单的 order_products
                $prodSql = sprintf(
                    "SELECT JSON_OBJECT("
                    . "'product_id', op.product_id, "
                    . "'product_name', op.product_name, "
                    . "'product_img', op.product_img, "
                    . "'number', op.number, "
                    . "'unit_price', op.unit_price, "
                    . "'total_price', op.total_price, "
                    . "'product', op.product, "
                    . "'created_at', op.created_at, "
                    . "'updated_at', op.updated_at"
                    . ") "
                    . "FROM order_products op "
                    . "JOIN orders o ON o.id = op.order_id "
                    . "WHERE o.no = '%s'",
                    addslashes($no)
                );
                $oldProducts = $this->queryOldDbJson($prodSql);

                // 创建订单（新库多了 secret / is_test / release_token 列，用默认值）
                // 注：created_at/updated_at 不在 fillable 中，需单独设置
                $orderData = [
                    'no' => $no,
                    'inside_no' => $row['inside_no'] ?? '',
                    'total_price' => $row['total_price'] ?? 0,
                    'product_price' => $row['product_price'] ?? 0,
                    'freight' => $row['freight'] ?? 0,
                    'delivery_type' => $row['delivery_type'] ?? 0,
                    'payment_type' => $row['payment_type'] ?? 0,
                    'name' => $row['name'] ?? '',
                    'phone' => $row['phone'] ?? '',
                    'email' => $row['email'] ?? '',
                    'country' => $row['country'] ?? '中国',
                    'province' => $row['province'] ?? '台灣',
                    'city' => $row['city'] ?? '',
                    'county' => $row['county'] ?? '',
                    'street' => $row['street'] ?? null,
                    'address' => $row['address'] ?? '',
                    'delivery_time' => $row['delivery_time'] ?? null,
                    'status' => $row['status'] ?? 0,
                    'remarks' => $row['remarks'] ?? null,
                    'admin_remarks' => $row['admin_remarks'] ?? null,
                    'ip' => $row['ip'] ?? '',
                    'ipcountry' => $row['ipcountry'] ?? null,
                    'user_agent' => $row['user_agent'] ?? null,
                    'shop_no' => $row['shop_no'] ?? null,
                    'shop_name' => $row['shop_name'] ?? null,
                    'shop_type' => $row['shop_type'] ?? 0,
                    'shop_data' => $row['shop_data'] ?? null,
                ];
                $order = Order::create($orderData);
                $order->timestamps = false;
                $order->created_at = $row['created_at'] ?? null;
                $order->updated_at = $row['updated_at'] ?? null;
                $order->save();
                $order->timestamps = true;

                // 同步 order_products
                foreach ($oldProducts as $op) {
                    $opData = [
                        'order_id' => $order->id,
                        'product_id' => $op['product_id'] ?? 0,
                        'product_name' => $op['product_name'] ?? '',
                        'product_img' => $op['product_img'] ?? null,
                        'number' => $op['number'] ?? 1,
                        'unit_price' => $op['unit_price'] ?? 0,
                        'total_price' => $op['total_price'] ?? 0,
                        'product' => $op['product'] ?? '{}',
                    ];
                    $opModel = OrderProduct::create($opData);
                    $opModel->timestamps = false;
                    $opModel->created_at = $op['created_at'] ?? null;
                    $opModel->updated_at = $op['updated_at'] ?? null;
                    $opModel->save();
                    $opModel->timestamps = true;
                }

                DB::commit();
                $this->info("  ✅ 同步订单: {$no} - {$row['name']} (" . count($oldProducts) . " 项商品)");
                $synced++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("  ❌ 同步订单 {$no} 失败: " . $e->getMessage());
            }
        }

        return $synced;
    }

    /**
     * 同步留言
     */
    private function syncMessages(string $from, string $to, bool $dryRun): int
    {
        $this->info('--- 同步留言 ---');

        // 查询旧库留言
        $sql = sprintf(
            "SELECT JSON_OBJECT("
            . "'name', m.name, "
            . "'email', m.email, "
            . "'phone', m.phone, "
            . "'content', m.content, "
            . "'sex', m.sex, "
            . "'ip', m.ip, "
            . "'user_agent', m.user_agent, "
            . "'type', m.type, "
            . "'created_at', m.created_at, "
            . "'updated_at', m.updated_at"
            . ") "
            . "FROM messages m "
            . "WHERE m.created_at >= '%s 00:00:00' AND m.created_at <= '%s 23:59:59'",
            $from, $to
        );

        $oldMessages = $this->queryOldDbJson($sql);
        $this->line("旧库本月留言: " . count($oldMessages));

        if (empty($oldMessages)) {
            $this->warn('无待同步留言');
            return 0;
        }

        $synced = 0;
        foreach ($oldMessages as $row) {
            // 避免重复：根据内容 + 创建时间判断
            $exists = Message::where('content', $row['content'] ?? '')
                ->where('created_at', $row['created_at'] ?? '')
                ->exists();

            if ($exists) {
                $this->line("  跳过已有留言: {$row['name']} @ {$row['created_at']}");
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] 将同步留言: {$row['name']} - " . mb_substr($row['content'] ?? '', 0, 30));
                $synced++;
                continue;
            }

            try {
                Message::create([
                    'name' => $row['name'] ?? '',
                    'email' => $row['email'] ?? '',
                    'phone' => $row['phone'] ?? null,
                    'content' => $row['content'] ?? '',
                    'sex' => $row['sex'] ?? 0,
                    'ip' => $row['ip'] ?? '',
                    'user_agent' => $row['user_agent'] ?? null,
                    'type' => $row['type'] ?? 0,
                    'created_at' => $row['created_at'] ?? null,
                    'updated_at' => $row['updated_at'] ?? null,
                ]);
                $this->info("  ✅ 同步留言: {$row['name']} - " . mb_substr($row['content'] ?? '', 0, 30));
                $synced++;
            } catch (\Throwable $e) {
                $this->error("  ❌ 同步留言失败: " . $e->getMessage());
            }
        }

        return $synced;
    }
}
