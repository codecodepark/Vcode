# VerificationCode
验证码类
$vcode=new Vcode();

实例化时可传入一个数组作为参数
$config=array(
	’height‘=>50,	//高度，默认50px
	'width'=>500, 	//宽度，默认宽为32×验证码个数,推荐让程序自动生成
	’num‘=>4,		//验证码个数，默认4
	’font‘=>’./font/1.ttf‘,		//验证码字体文件路径，默认’./font/1.ttf‘
	$config['session']=true	//是否将验证码写入$_SESSION['vcode'],默认true
);
$vcode=new Vcode($config);

对外接口：

void Vcode::entry() 		生成验证码图片：
eg：$vcode->entry();

string Vcode::getCode() 	获取验证码：
eg:$vcode->getCode();