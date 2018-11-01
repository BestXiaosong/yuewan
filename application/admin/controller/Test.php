<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 11:28
 */

namespace app\admin\controller;


use app\common\model\Base;
use CreateRed\Client;
use think\Controller;
use think\Exception;
use think\Loader;

//���ô�������DTO
class OptionDTO
{

    //����ܽ��
    public $totalMoney;

    //�������
    public $num;

    //��Χ��ʼ
    public $rangeStart;

    //��Χ����
    public $rangeEnd;

    //���ɺ������
    public $builderStrategy;

    //������ʣ�����
    public $randFormatType; //Can_Left����������,������ʣ�ࣻNo_Left��������ʣ��

    public static function create($totalMoney, $num, $rangeStart, $rangEnd,
                                  $builderStrategy, $randFormatType = 'No_Left')
    {
        $self = new self();
        $self->num = $num;
        $self->rangeStart = $rangeStart;
        $self->rangeEnd = $rangEnd;
        $self->totalMoney = $totalMoney;
        $self->builderStrategy = $builderStrategy;
        $self->randFormatType = $randFormatType;
        return $self;
    }

}

//����������ӿ�
interface IBuilderStrategy
{
    //�������
    public function create();

    //��������
    public function setOption(OptionDTO $option);

    //�Ƿ�������ɺ��
    public function isCanBuilder();

    //���ɺ������
    public function fx($x);
}

//�̶��ȶ�������
class EqualPackageStrategy implements IBuilderStrategy
{
    //����������
    public $oneMoney;

    //����
    public $num;

    public function __construct($option = null)
    {
        if ($option instanceof OptionDTO) {
            $this->setOption($option);
        }
    }

    public function setOption(OptionDTO $option)
    {
        $this->oneMoney = $option->rangeStart;
        $this->num = $option->num;
    }

    public function create()
    {

        $data = array();
        if (false == $this->isCanBuilder()) {
            return $data;
        }

        $data = array();
        if (false == is_int($this->num) || $this->num <= 0) {
            return $data;
        }
        for ($i = 1; $i <= $this->num; $i++) {
            $data[$i] = $this->fx($i);
        }
        return $data;
    }

    /**
     * �ȶ����ķ�����һ��ֱ��
     *
     * @param mixed $x
     * @access public
     * @return void
     */
    public function fx($x)
    {
        return $this->oneMoney;
    }

    /**
     * �Ƿ��̶ܹ����
     *
     * @access public
     * @return void
     */
    public function isCanBuilder()
    {
        if (false == is_int($this->num) || $this->num <= 0) {
            return false;
        }

        if (false == is_numeric($this->oneMoney) || $this->oneMoney <= 0) {
            return false;
        }

        //�������С��1��
        if ($this->oneMoney < 0.01) {
            return false;
        }

        return true;

    }


}

//����������(������)
class RandTrianglePackageStrategy implements IBuilderStrategy
{
    //�ܶ�
    public $totalMoney;

    //�������
    public $num;

    //��������Сֵ
    public $minMoney;

    //���������ֵ
    public $maxMoney;

    //�����ݷ�ʽ��NO_LEFT: ����ܶ� = Ԥ���ܶCAN_LEFT: ����ܶ� <= Ԥ���ܶ�
    public $formatType;

    //Ԥ��ʣ����
    public $leftMoney;


    public function __construct($option = null)
    {
        if ($option instanceof OptionDTO) {
            $this->setOption($option);
        }
    }

    public function setOption(OptionDTO $option)
    {
        $this->totalMoney = $option->totalMoney;
        $this->num = $option->num;
        $this->formatType = $option->randFormatType;
        $this->minMoney = $option->rangeStart;
        $this->maxMoney = $option->rangeEnd;
        $this->leftMoney = $this->totalMoney;
    }

    /**
     * ����������
     *
     * @access public
     * @return void
     */
    public function create()
    {

        $data = array();
        if (false == $this->isCanBuilder()) {
            return $data;
        }

        $leftMoney = $this->leftMoney;
        for ($i = 1; $i <= $this->num; $i++) {
            $data[$i] = $this->fx($i);
            $leftMoney = $leftMoney - $data[$i];
        }

        //������
        list($okLeftMoney, $okData) = $this->format($leftMoney, $data);

        //�������
        shuffle($okData);
        $this->leftMoney = $okLeftMoney;

        return $okData;
    }

    /**
     * �Ƿ��ܹ���������
     *
     * @access public
     * @return void
     */
    public function isCanBuilder()
    {
        if (false == is_int($this->num) || $this->num <= 0) {
            return false;
        }

        if (false == is_numeric($this->totalMoney) || $this->totalMoney <= 0) {
            return false;
        }

        //��ֵ
        $avgMoney = $this->totalMoney / 1.0 / $this->num;

        //��ֵС����Сֵ
        if ($avgMoney < $this->minMoney) {
            return false;
        }

        return true;

    }

    /**
     * ��ȡʣ����
     *
     * @access public
     * @return void
     */
    public function getLeftMoney()
    {
        return $this->leftMoney;
    }

    /**
     * ���������ɺ��������Ǻ�����[(1,0.01),($num/2,$avgMoney),($num,0.01)]
     *
     * @param mixed $x ,1 <= $x <= $this->num;
     * @access public
     * @return void
     */
    public function fx($x)
    {

        if (false == $this->isCanBuilder()) {
            return 0;
        }

        if ($x < 1 || $x > $this->num) {
            return 0;
        }

        $x1 = 1;
        $y1 = $this->minMoney;

        //�ҵķ�ֵ
        $y2 = $this->maxMoney;

        //�м��
        $x2 = ceil($this->num / 1.0 / 2);

        //����
        $x3 = $this->num;
        $y3 = $this->minMoney;

        //��x1,x2,x3����1��ʱ��(����)
        if ($x1 == $x2 && $x2 == $x3) {
            return $y2;
        }

        // '/_\'������״�����Է���
        //'/'����
        if ($x1 != $x2 && $x >= $x1 && $x <= $x2) {

            $y = 1.0 * ($x - $x1) / ($x2 - $x1) * ($y2 - $y1) + $y1;
            return number_format($y, 2, '.', '');
        }

        //'\'��״
        if ($x2 != $x3 && $x >= $x2 && $x <= $x3) {

            $y = 1.0 * ($x - $x2) / ($x3 - $x2) * ($y3 - $y2) + $y2;
            return number_format($y, 2, '.', '');
        }

        return 0;


    }

    /**
     * ��ʽ���޺������
     *
     * @param mixed $leftMoney
     * @param array $data
     * @access public
     * @return void
     */
    private function format($leftMoney, array $data)
    {

        //���ܷ�������
        if (false == $this->isCanBuilder()) {
            return array($leftMoney, $data);
        }

        //���ʣ����0
        if (0 == $leftMoney) {
            return array($leftMoney, $data);
        }

        //����Ϊ��
        if (count($data) < 1) {
            return array($leftMoney, $data);
        }

        //����ǿ�����ʣ�࣬����$leftMoney > 0
        if ('Can_Left' == $this->formatType
            && $leftMoney > 0
        ) {
            return array($leftMoney, $data);
        }


        //�ҵķ�ֵ
        $myMax = $this->maxMoney;

        // ���������Ǯ�����Լӵ�С��������Ӳ���ȥ��������һ����
        while ($leftMoney > 0) {
            $found = 0;
            foreach ($data as $key => $val) {
                //����ѭ���Ż�
                if ($leftMoney <= 0) {
                    break;
                }

                //Ԥ��
                $afterLeftMoney = (double)$leftMoney - 0.01;
                $afterVal = (double)$val + 0.01;
                if ($afterLeftMoney >= 0 && $afterVal <= $myMax) {
                    $found = 1;
                    $data[$key] = number_format($afterVal, 2, '.', '');
                    $leftMoney = $afterLeftMoney;
                    //����
                    $leftMoney = number_format($leftMoney, 2, '.', '');
                }
            }

            //���û�п��Լӵĺ������Ҫ����,������ѭ��
            if ($found == 0) {
                break;
            }
        }
        //���$leftMoney < 0 ,˵�����ɵĺ������Ԥ���ˣ���Ҫ���ٲ��ֺ�����
        while ($leftMoney < 0) {
            $found = 0;
            foreach ($data as $key => $val) {
                if ($leftMoney >= 0) {
                    break;
                }
                //Ԥ��

                $afterLeftMoney = (double)$leftMoney + 0.01;
                $afterVal = (double)$val - 0.01;
                if ($afterLeftMoney <= 0 && $afterVal >= $this->minMoney) {
                    $found = 1;
                    $data[$key] = number_format($afterVal, 2, '.', '');
                    $leftMoney = $afterLeftMoney;
                    $leftMoney = number_format($leftMoney, 2, '.', '');
                }
            }

            //���һ�����ٵĺ����û�еĻ�����Ҫ������������ѭ��
            if ($found == 0) {
                break;
            }
        }
        return array($leftMoney, $data);
    }

}

//ά�����ԵĻ�����
class RedPackageBuilder
{

    // ʵ��
    protected static $_instance = null;

    /**
     * Singleton instance����ȡ�Լ���ʵ����
     *
     * @return MemcacheOperate
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * ��ȡ���ԡ�ʹ�÷��䡿
     *
     * @param string $type ����
     * @return void
     */
    public function getBuilderStrategy($type)
    {
//        $class = $type.'PackageStrategy';
//        var_dump($class);die;
        if ($type == 'Equal') {
            $class = 'app\admin\controller\EqualPackageStrategy';
        } else {
            $class = 'app\admin\controller\RandTrianglePackageStrategy';

        }


        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \Exception("{$class} �಻���ڣ�");
        }
    }

    public function getRedPackageByDTO(OptionDTO $optionDTO)
    {
        //��ȡ����
        $builderStrategy = $this->getBuilderStrategy($optionDTO->builderStrategy);

        //���ò���
        $builderStrategy->setOption($optionDTO);

        return $builderStrategy->create();
    }

}

class Test extends Base
{
    public static function main($argv)
    {
        //�̶����
        $dto = OptionDTO::create(1000, 10, 100, 100, 'Equal');
        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
        //print_r($data);

        //������[������]
        $dto = OptionDTO::create(5, 10, 0.01, 0.99, 'RandTriangle');
        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
//        print_r($data);

        //������[��������]
        $dto = OptionDTO::create(5, 10, 0.01, 0.99, 'RandTriangle', 'Can_Left');
        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
        print_r($data);

    }


    public function rand()
    {
//        Loader::import('\CretedRed\CreateRed\Client', EXTEND_PATH);
//        \CreateRed\Client::equal(100,10);
        $res = Client::rand(100,10,1,15);
        dump($res);
    }

    public function test(){
        $ids =[['id'=>1],['id'=>2]];
        $res =  json_encode($ids);
        echo $res;
        dump(json_decode($res,1));
        $arr =array();
        foreach($ids as $k=>$va){
            $arr[] = $va['id'];
        }
        var_dump($arr);
        var_dump([1,2]);
    }
}




