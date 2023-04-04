<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Collection;

/**
 * 基础控制器
 */
abstract class ApiBaseController extends \Illuminate\Routing\Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected array $result = [
        "code" => 0,
        "message" => "操作成功",
        "data" => [],
    ];

    public function __invoke()
    {
        return $this->index();
    }

    /**
     * 控制器默认入口
     * @return array
     */
    public function index(): array
    {
        //参数检测
        $this->check();
        if ($this->result["code"] !== 0) {
            return $this->result;
        }
        //主体逻辑
        $serviceData = $this->service();
        if (!empty($serviceData['data'])) {
            $serviceData['data'] = $this->formateResult($serviceData['data']);
        }
        return $serviceData;
    }

    /**
     * 查询构造器
     * @return array
     */
    protected function querySearchConstructor(&$queryContruct, $searchMap)
    {
        foreach ($searchMap as $requestKey => $searchRule) {
            $requestKeyVal = \request($requestKey);
            if ($requestKeyVal) {
                switch ($searchRule[1]) {
                    case "=":
                        $queryContruct->where($searchRule[0], $requestKeyVal);
                        break;
                    case "in":
                        $queryContruct->whereIn($searchRule[0], $requestKeyVal);
                        break;
                    case "rlike":
                        $queryContruct->where($searchRule[0], 'like', $requestKeyVal . '%');
                        break;
                    case "like":
                        $queryContruct->where($searchRule[0], 'like', '%' . $requestKeyVal . '%');
                        break;
                    case "raw":
                        $num = empty($searchRule[2]) ? 1 : intval($searchRule[2]);
                        $rawArr = [];
                        for ($i = 0; $i < $num; $i++) {
                            $rawArr[] = $requestKeyVal;
                        }
                        $queryContruct->whereRaw($searchRule[0], $rawArr);
                        break;
                }
            }
        }
    }

    //参数检测
    protected abstract function check();

    //主体逻辑
    protected abstract function service();

    /**
     * 返回数据格式化
     * @param array $data 需要格式化的返回数据
     * @return array 已格式化的数据
     */
    private function formateResult($data)
    {
        $result = [];
        is_object($data) && $data = (new Collection($data))->toArray();
        if (\is_array($data)) {
            foreach ($data as $key => $value) {
                is_object($value) && $value = (new Collection($value))->toArray();
                if (\is_array($value)) {
                    $value = empty($value) ? [] : $this->formateResult($value);
                } else if (empty($value) && $value !== 0 && $value !== "0") {
                    $value = '';
                }
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
