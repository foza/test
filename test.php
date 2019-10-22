<?php

namespace Application\Controller;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\ResultSet\ResultSet;


final class init
{
    /**
     * @access private
     * @var \Zend\Db\Adapter\Adapter
     */
    private $adapter;

    /**
     * @access public
     * @return void
     * @uses $adapter
     */
    function __construct()
    {
        try {
            $this->adapter = new \Zend\Db\Adapter\Adapter(array(
                'driver' => 'Mysqli',
                'database' => 'university',
                'username' => 'root',
                'password' => ''
            ));
        } catch (Exception $ex) {
        }

        $this->create();
        $this->fill();
    }

    /**
     * @access private
     * @return void
     * @uses $adapter
     */
    private function create()
    {
        $queryCreateTable = "CREATE TABLE IF NOT EXISTS `test` (`id` INT  NOT NULL AUTO_INCREMENT,`script_name` VARCHAR(25) NOT NULL,`script_time` INT NOT NULL,`end_time` INT NOT NULL,`result` SET('normal','illegal','failed','success') NOT NULL, PRIMARY KEY(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
        try {
            $this->adapter->query($queryCreateTable, Adapter::QUERY_MODE_EXECUTE);
        } catch (Exception $ex) {
        }
    }

    /**
     * @access private
     * @return void
     * @uses $adapter
     */
    private function fill()
    {
        for ($i = 0; $i < 1000; ++$i) {
            $result = array_rand(array("norm" => "1", "illegal" => "2", "failed" => "3", "success" => "4"), 1);
            $script_name = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(25 / strlen($x)))), 1, 25);
            $queryFill = "INSERT INTO `test` (`script_name`,`script_time`,`end_time`,`result`) VALUES ('" . $script_name . "'," . rand(1, 1000) . "," . rand(1, 1000) . ",'" . $result . "');";
            try {
                $this->adapter->query($queryFill, Adapter::QUERY_MODE_EXECUTE);
            } catch (Exception $ex) {
            }
        }
    }

    /**
     * @param string
     * @access public
     * @return Array()
     * @uses $adapter
     */
    public function get($result)
    {
        if ($result === "norm" || $result === "success") {
            $queryGet = "SELECT * FROM `test` WHERE `result`='" . $result . "'";
            try {
                $preResult = $this->adapter->query($queryGet, Adapter::QUERY_MODE_EXECUTE);
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($preResult);
                return $resultSet->toArray();

            } catch (Exception $ex) {
                return Array();
            }
        } else {
            throw new \Exception("Параметр может быть только «norm» или «success»!");
        }
    }
}

?>