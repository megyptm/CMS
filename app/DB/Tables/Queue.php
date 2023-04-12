<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-27
 * Time: 12:26 PM
 */

namespace App\DB\Tables;


use Maatify\DB\DbConnector;

class Queue extends DbConnector
{
    protected string $tableName = 'queue';
    private int $time;

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __destruct()
    {
        $this->Stop();
    }

    public function Email():void
    {
        $this->id = 1;
        $this->Start();
    }

    public function SmsPhone(): void
    {
        $this->id = 2;
        $this->Start();
    }

    private function Stop(){
        $this->time = time()-(60*60*24);
        $this->QueueAction();
    }

    private function Start(){
        if($this->CurrentQueue() < time()-30){
            $this->time = time();
            $this->QueueAction();
        }else{
            sleep(10);
            $this->Start();
        }
    }

    private function QueueAction(){
        $this->Edit(['timestamp'=>$this->time], '`id` = ?', [$this->id]);
    }
    private function CurrentQueue(): int
    {
        return (int)$this->ColThisTable('timestamp', '`id` = ?', [$this->id]);
    }
}