<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * 这些 yescialis Model 未定义 $fillable（沿用原 Dcat Admin 写法），
 * 导致 Filament 默认的 fill()/create() 因 mass assignment 守卫而失败。
 * 此 trait 用 forceFill 绕过守卫，使后台的创建/编辑可正常保存。
 */
trait ForceFillRecord
{
    protected function handleRecordCreation(array $data): Model
    {
        $model = new ($this->getModel())();
        $model->forceFill($data);
        $model->save();

        return $model;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill($data);
        $record->save();

        return $record;
    }
}
