<?php
/**
 * shadowsocks-panel
 * Add: 2016/03/27 00:12
 * Author: Sendya <18x@loacg.com>
 */


namespace Helper;

use Helper\Setting;
use Core\Database as DB;
use Model\Cron;
use Model\Mail;

class CronTab
{

    private $now;
    private $cronId;

    function __construct()
    {
        $this->now = time();
    }

    public function run()
    {
        if (!defined('ENABLE_CRON')) {
            return;
        }

        $cron = $this->getNextRun();
        if ($cron) {
            $cron->run();
            $this->setNextRun($cron->getStep());
        }

        if (!Option::get('mail_queue')) {
            return;
        }
        $mailer = Mailer::getInstance();
        $mailer->toQueue(false, true); // set to queue.
        $mailQueue = Mail::getQueueList();
        if (count($mailQueue) >0) {
            foreach ($mailQueue as $key=>$mail) {
                $mail->delete();
                $mailer->send($mail);
            }
        } else {
            Option::set('mail_queue', 0);
        }
        return;
    }

    private function getNextRun()
    {
        $next = Cron::getNextRun();
        if (!$next) {
            return false;
        }

        $this->cronId = $next->id;
        $cl = ucfirst($this->cronId);
        $cl = "\\Helper\\Cron\\{$cl}";
        $obj = new $cl;
        return $obj;
    }

    private function setNextRun($step)
    {
        Cron::setNextRun($this->cronId, $step);
    }

    private function start($name)
    {


    }

    private function stop()
    {

    }

    private function add()
    {

    }

    private function remove()
    {

    }

    private function disable()
    {

    }

    private function restart()
    {

    }
}