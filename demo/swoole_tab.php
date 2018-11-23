<?php
header("Content-type: text/html; charset=utf-8"); 
$table = new swoole_table(1024);
$table->column('fd', swoole_table::TYPE_INT);
$table->create();

$ws = new swoole_websocket_server("0.0.0.0", 9501);
$ws->table = $table;
$ws->on('open', function ($ws, $request) {  

	//当前在线人员
	$ws->table->set($request->fd, array('fd' => $request->fd));//获取客户端id插入table
	// foreach ($ws->table as $u) 
	// {
	// 	var_dump($u); //输出整个table
	// }           
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
	global $msg;
	$msg = json_decode($frame->data,true);

	foreach ($ws->table as $u)
	{
		if($msg['act'] == 'login')
		{
			$ws->push($u['fd'], '<font color="red">'.$msg['real_name'].'</font>登录了' );//消息广播给所有客户端
			//redis_online_user($frame->fd,$msg['real_name']);
		}
		else
		{
			if($frame->fd == $u['fd'])
			{
				$ws->push($u['fd'], '<div style="text-align:right;padding-right:10px;"><font color="green">你说:</font>'.$msg['message'].'</div>' );//消息广播给所有客户端
			}
		else
		{
			$ws->push($u['fd'], '<font color="red">'.$msg['real_name'].'说:</font>'.$msg['message'] );//消息广播给所有客户端
		}

		}
	}       
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
	echo "client-{$fd} is closed\n"; 
	$ws->table->del($fd);//从table中删除断开的id
	//redis_offline_user($fd);
});

$ws->start();
