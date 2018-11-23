## Php Swoole 聊天室demo

#### 注意此demo需要swoole扩展扩展

	header("Content-type: text/html; charset=utf-8"); 
	$table = new swoole_table(1024);
	$table->column('fd', swoole_table::TYPE_INT);
	$table->create();

	$ws = new swoole_websocket_server("0.0.0.0", 9501);
	
	
	
	
	需要把swoole_tab.php 在后台挂载 php swoole_tab.php