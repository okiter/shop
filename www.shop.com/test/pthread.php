<?php
header('Content-Type: text/html;charset=utf-8');

    $start  = microtime(true);


    //>>1.第一个业务逻辑
    $myThread = new MyThread($step);
    $myThread->start();//必须调用start方法启动线程,而不是调用run方法...


    //>>2.第二个业务逻辑
    $myThread1 = new MyThread1();
    $myThread1->start();

    $myThread->join();
    $myThread1->join();

    $end = microtime(true);
    echo $end - $start;


//>>1.需要做的是, 将第一个业务代码放在新开启的一个线程上执行...

    /**
     * 1.必须创建一个Thread的子类才能够创建一个新的线程..
     * 2.需要在线程中运行的代码必须写在run方法中.. 在run方法中写业务代码
     * 3.必须new出该线程的对象, 然后调用start方法来启动线程
     */
    class MyThread extends Thread{
        public function run()
        {
            //>>1.第一个业务逻辑
            echo '第一个业务逻辑';
            sleep(1);
        }
    }


    class MyThread1 extends Thread{
        public function run()
        {
           echo '第二个业务逻辑';
            sleep(1);
        }
    }