<?php
namespace API;

abstract class ApiTpl
{
    protected $data;
    //master开头，表示这是接口的function
    /*
     * 获取信息，比如本api的说明或调用说明
     */
    abstract public function baseGetInfo();

    /*
     * 处理中心
     */
    abstract public function baseOption();

    /*
     * 设置data
     */
    public function baseSetData($data)
    {
        $this->data = $data;
    }

    /*
     * 获取data
     */
    public function baseGetData()
    {
        return array( 'ack' => 1, 'msg' => $this->data );
    }
}
