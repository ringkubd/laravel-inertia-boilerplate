<?php

namespace Anwar\ConfigEditor;

use Illuminate\Config\Repository;

class ConfigEditor extends Repository
{
    private $tr;
    private $singleArray = [];

    public function getConfig($fileName)
    {
        return config($fileName);
    }

    public function getConfigFormAsTable($fileName, Array $classes=[]){
        $configArray = config($fileName, []);
        $this->generateTd($configArray);
        $tableClass = $classes['table'] ?? "table table-bordered";
        $table = "<form @submit.prevent='submit'><table class='{$tableClass}'>";
        $table .= $this->tr;
        $table .= "<tr><td colspan='2' class='flex justify-content-end justify-end items-center'><input type='submit' class='btn btn-success' /></td></tr>";
        $table .= "</table></form>";
        return $table;
    }

    /**
     * @param $config
     * @param $parentKey
     * @return $this
     */

    private function generateTd($config, $parentKey=""){
        $tr = "";
        foreach ($config as $k => $v){
            if (is_array($v)) {
                if ($parentKey != "") {
                    $k = $parentKey.'.'.$k;
                }
                $this->generateTd($v, $k);
                continue;
            }else{
                $abc = $parentKey != "" ? $parentKey.'.' : "";
                $tr .= "<tr>";
                $tr .= "<th>{$abc}{$k}</th>";
                $tr .= "<td><input type='text' class='form-control' name='{$abc}{$k}' ref='input' value='{$v}' /> </td>";
                $tr .= "<tr>";
            }
        }
        $this->tr .= $tr;
        return $this;
    }

    /**
     * @param $fileName
     * @param $parentKey
     * @return void
     */

    public function getConfigAsSingleArray($fileName, Array $classes=[]){
        $configArray = config($fileName, []);
        $array = $this->generateArray($configArray);
        return $this->singleArray;
    }

    private function generateArray($config, $parentKey=""){
        $singleArray = [];
        foreach ($config as $k => $v){
            if (is_array($v)) {
                if ($parentKey != "") {
                    $k = $parentKey.'.'.$k;
                }
                $this->generateTd($v, $k);
                continue;
            }else{
                $abc = $parentKey != "" ? $parentKey.'.' : "";
                $singleArray["{$abc}{$k}"] = $v;
            }
        }
        $this->singleArray = array_merge($this->singleArray,$singleArray);
        return $this;
    }
}
