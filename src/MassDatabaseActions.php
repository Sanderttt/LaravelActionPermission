<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 7-11-2016
 * Time: 13:16
 */

namespace RobinVanDijk\LaravelActionPermission;


use Illuminate\Support\Facades\DB;

class MassDatabaseActions
{
    public function run($model, $records, $create_fields, $update_fields, $time)
    {
        $this->table = $model->getTable();

        $this->setRecords($records);

        $this->setCreateFields($create_fields);

        $this->setUpdateFields($update_fields);

        $this->setQuery();

        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare($this->query);

        $res = $stmt->execute(array_flatten($this->data));

        if ($res) {
            if ($model->where('updated_at', '<', $time)->delete()) {
                return true;
            }
        }

        return false;

    }

    private function setRecords($records)
    {
        $this->records = $records;
        $this->data = array_flatten($records);
        $this->record_count = count($records);
    }

    private function setCreateFields($create_fields)
    {
        $this->insert_keys = implode(', ', $create_fields);

        $this->insert_string = rtrim(
            str_repeat('('
                . rtrim(str_repeat('?,', count($create_fields)), ',') .
                '),', $this->record_count),
            ','
        );
    }

    private function setUpdateFields($update_fields)
    {
        $this->update_keys = implode(',',
            (array_map(function ($field) {
                return $field . ' = VALUES(' . $field . ')';
            }, $update_fields))
        );
    }

    private function setQuery()
    {
        $this->query = 'INSERT INTO ' . $this->table . '
          (' . $this->insert_keys . ') 
          VALUES ' . $this->insert_string . '
          ON DUPLICATE KEY UPDATE ' . $this->update_keys;
    }
}