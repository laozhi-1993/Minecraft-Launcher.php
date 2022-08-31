<?php class Launcher
{
	public $root = __DIR__;
	public $java;
	public $cmd;
	public $versions;
	public $versions_jar;
	public $versions_json;
	
	
	public function __construct($java,$versions,$xmn,$xmx)
	{
		$this->java          = $java;
		$this->versions      = $versions;
		$this->versions_jar  = "{$this->root}\\.minecraft\\versions\\{$this->versions}\\{$this->versions}.jar";
		$this->versions_json = "{$this->root}\\.minecraft\\versions\\{$this->versions}\\{$this->versions}.json";
		$this->cmd           = "{$java} -Xmn{$xmn}m -Xmx{$xmx}m";
		$this->cmd          .= ' -XX:+UnlockExperimentalVMOptions';
		$this->cmd          .= ' -XX:G1NewSizePercent=20';
		$this->cmd          .= ' -XX:G1ReservePercent=20';
		$this->cmd          .= ' -XX:MaxGCPauseMillis=50';
		$this->cmd          .= ' -XX:G1HeapRegionSize=16m';
		$this->cmd          .= ' -XX:+UseG1GC';
		$this->cmd          .= ' -XX:-UseAdaptiveSizePolicy';
		$this->cmd          .= ' -XX:-OmitStackTraceInFastThrow';
		$this->cmd          .= ' -XX:-DontCompileHugeMethods';
		$this->json          = json_decode(file_get_contents($this->versions_json),true);
		
		
		$this->username         = 'laozhi';
		$this->uuid             = 'be2c077954673b69865a1633750d0eaa';
		$this->token            = 'be2c077954673b69865a1633750d0eaa';
		$this->width            = '854';
		$this->height           = '480';
		$this->launcher_name    = 'weiw-Launcher';
		$this->launcher_version = '1.0.0';
		$this->fullscreen       = false;
		$this->server           = '';
		$this->port             = '';
		$this->auth_route       = '';
		$this->auth_url         = '';
	}
	public function libraries($library_directory,$classpath_separator)
	{
		$libraries = Array();
		foreach($this->json['libraries'] as $value)
		{
			if(isset($value['downloads']['classifiers']))
			{
				//占位
			
			
			
			
			}
			elseif(isset($value['downloads']['artifact']))
			{
				if(isset($value['rules']))
				{
					if((isset($value['downloads']['artifact']['path']) && isset($value['rules'][0]['os']['name']) && $value['rules'][0]['os']['name'] == 'windows') or isset($value['rules'][1]))
					{
						$libraries[] = "{$library_directory}\\{$value['downloads']['artifact']['path']}";
					}
				}
				else
				{
					if(isset($value['downloads']['artifact']['path']))
					{
						$libraries[] = "{$library_directory}\\{$value['downloads']['artifact']['path']}";
					}
				}
			}
			else
			{
				if(isset($value['name']))
				{
					$sss = explode(':',$value['name']);
					$sss[0] = str_replace('.','\\',$sss[0]);
					if(isset($sss[3]))
					{
						$libraries[] = "{$library_directory}\\{$sss[0]}\\{$sss[1]}\\{$sss[2]}\\{$sss[1]}-{$sss[2]}-{$sss[3]}.jar";
					}
					else
					{
						$libraries[] = "{$library_directory}\\{$sss[0]}\\{$sss[1]}\\{$sss[2]}\\{$sss[1]}-{$sss[2]}.jar";
					}
				}
			}
		}
		
		
		$libraries[] = $this->versions_jar;
		return implode($classpath_separator,$libraries);
	}
	public function auth($route,$url)
	{
		if($route != '' && $url != '')
		{
			$prefetched = base64_encode(file_get_contents($url));
			$this->cmd .= " -javaagent:${root}\\{$route}={$url}";
			$this->cmd .= " -Dauthlibinjector.yggdrasil.prefetched={$prefetched}";
		}
	}
	public function game()
	{
		if(isset($this->json['minecraftArguments']))
		{
			$this->cmd .= ' -Djava.library.path=${natives_directory}';
			$this->cmd .= ' -Dminecraft.launcher.brand=${launcher_name}';
			$this->cmd .= ' -Dminecraft.launcher.version=${launcher_version}';
			$this->cmd .= ' -cp ${classpath}';
			$this->cmd .= ' '.$this->json['mainClass'];
			$this->cmd .= ' '.$this->json['minecraftArguments'];
		}
		else
		{
			foreach($this->json['arguments']['jvm'] as $value)
			{
				if(is_array($value))
				{
					//占位
				}
				else
				{
					$this->cmd .= ' '.$value;
				}
			}
		
		
			$this->cmd .= ' '.$this->json['mainClass'];			
		}
		
		if(isset($this->json['arguments']['game']))
		{
			foreach($this->json['arguments']['game'] as $value)
			{
				if(!is_array($value))
				{
					$this->cmd .= ' '.$value;
				}
			}
		}
		
		$this->cmd .= ' --width';
		$this->cmd .= ' ${resolution_width}';
		$this->cmd .= ' --height';
		$this->cmd .= ' ${resolution_height}';
		
		if($this->fullscreen)
		{
			$this->cmd .= ' --fullscreen';
		}
		
		if($this->server != '' && $this->port != '')
		{
			$this->cmd .= ' --server';
			$this->cmd .= ' '+this.server;
			$this->cmd .= ' --port';
			$this->cmd .= ' '+this.port;
		}
	}
	public function cmd()
	{
		$this->auth($this->auth_route,$this->auth_url);
		$this->game();
		
		
		$arguments = Array();
		$arguments['auth_player_name']    = $this->username;                                                                     //玩家名字
		$arguments['auth_uuid']           = $this->uuid;                                                                         //玩家UUID
		$arguments['auth_access_token']   = $this->token;                                                                        //玩家令牌
		$arguments['auth_session']        = $this->token;                                                                        //玩家令牌
		$arguments['root']                = $this->root;                                                                         //根目录
		$arguments['game_directory']      = "{$this->root}\\.minecraft";                                                         //游戏目录
		$arguments['assets_root']         = "{$this->root}\\.minecraft\\assets";                                                 //游戏资源目录
		$arguments['version_name']        = $this->versions;                                                                     //游戏版本
		$arguments['assets_index_name']   = $this->json['assetIndex']['id'];                                                     //游戏资源版本
		$arguments['user_properties']     = '{}';                                                                                //用户属性
		$arguments['user_type']           = 'mojang';                                                                            //用户类型
		$arguments['version_type']        = $this->launcher_name;                                                                //启动器名称
		$arguments['resolution_width']    = $this->width;                                                                        //游戏窗口宽度
		$arguments['resolution_height']   = $this->height;                                                                       //游戏窗口高度
		$arguments['natives_directory']   = "{$this->root}\\.minecraft\\versions\\{$this->versions}\\natives-windows-x86_64";    //natives目录路径
		$arguments['library_directory']   = "{$this->root}\\.minecraft\\libraries";                                              //library目录路径
		$arguments['launcher_name']       = $this->launcher_name;                                                                //启动器名字
		$arguments['launcher_version']    = $this->launcher_version;                                                             //启动器版本号
		$arguments['classpath_separator'] = ';';                                                                                 //游戏依赖库分隔符
		$arguments['classpath']           = $this->libraries($arguments['library_directory'],$arguments['classpath_separator']); //游戏依赖库
		$arguments['primary_jar_name']    = "{$this->versions}.jar";                                                             //主程序名



		foreach($arguments as $key => $value)
		{
			$this->cmd = str_ireplace("\${{$key}}",$value,$this->cmd);
		}
		
		
		
		
		
		
		file_put_contents('cmd.bat',$this->cmd);
		exec('cmd.bat',$out);
		return $out;
	}
}



$Launcher = new Launcher('C:\Users\laozhi\AppData\Roaming\.minecraft\cache\java\java-runtime-beta\windows-x64\java-runtime-beta\bin\java.exe','1.19',128,4096);
$Launcher ->fullscreen = true;
$Launcher ->cmd();