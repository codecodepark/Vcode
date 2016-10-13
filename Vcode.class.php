<?php  

/**
 * file:vcode.class.php
 *验证码类
 ×email：1157229743@qq.com
 */
class Vcode{
	private $image;				//图像句柄
	private $width;				//宽度，默认32×验证码个数
	private $height;			//高度，默认50
	private $codeNum;			//验证码个数
	//验证码库
	private $allcode='abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
	private $code;			//当前验证码
	private $font;			//字体路径
	private $textcolor;		//验证码字体颜色
	private $bgcolor;		//验证码背景颜色
	private $fontsize;		//验证码字体大小，默认16+验证码个数
	private $session;		//是否写入$_SESSION['vcode']，默认true

	public function __construct(){
		if (func_num_args()!=1) {
			$config['height']=50;
			$config['num']=4;
			$config['width']=32;
			$config['font']='./font/1.ttf';
			$config['session']=true;
		}else{
			$arg=func_get_arg(0);
			if (is_array($arg)) {
				$config['height']=isset($arg['height'])?$arg['height']:50;
				$config['font']=isset($arg['font'])?$arg['font']:'./font/1.ttf';
				$config['num']=isset($arg['num'])?$arg['num']:4;
				$config['width']=isset($arg['width'])?$arg['width']/$config['num']:32;
				$config['session']=(isset($arg['session'])&&is_bool($arg['session']))?$arg['session']:true;
			}else{
				exit('the type of argument must be array');
			}
		}
		$this->width=$config['num']*$config['width'];
		$this->height=$config['height'];
		$this->codeNum=$config['num'];
		$this->font=$config['font'];
		$this->session=$config['session'];
		
		$this->textcolor=array(
			'r'=>mt_rand(0,100),
			'g'=>mt_rand(0,100),
			'b'=>mt_rand(0,100),
			);
		
		$this->bgcolor=$this->getBgColor();
		
		$this->fontsize=16+$this->codeNum;

		$this->createCode();

		$this->saveSession();
	}

	// 将当前验证码写入session
	private function saveSession(){
		if ($this->session) {
			if (!isset($_SESSION)) {
				session_start();
			}
			$_SESSION['vcode']=$this->getCode();
		}
	}

	// 获取背景颜色（获取最浅的和字体颜色一样的颜色）
	private function getBgColor(){
		$offset=0;
		foreach ($this->textcolor as $key => $value) {
			if ($value>$offset) {
				$offset=$value;
			}
		}
		$offset=255-$offset;
		return array(
			'r'=>$this->textcolor['r']+$offset,
			'g'=>$this->textcolor['g']+$offset,
			'b'=>$this->textcolor['b']+$offset,
			);
	}

	// 创建背景
	private function createBg(){
		$color=imagecolorallocate($this->image, $this->bgcolor['r'], $this->bgcolor['g'], $this->bgcolor['b']);
		imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $color);
	}

	// 从验证码库中随机获取字符作为验证码
	private function createCode(){
		$len=strlen($this->allcode)-1;
		$this->code='';
		for ($i=0; $i < $this->codeNum; $i++) { 
			$this->code.=$this->allcode[mt_rand(0,$len)];
		}
	}

	//在画布上画出验证码
	private function createText(){
		$color=imagecolorallocate($this->image, $this->textcolor['r'], $this->textcolor['g'], $this->textcolor['b']);
		for ($i=0; $i <$this->codeNum ; $i++) { 
			imagettftext($this->image,$this->fontsize, mt_rand(-45,45), ($this->width/$this->codeNum*$i)+mt_rand(6,10), ($this->height+$this->fontsize)/2+mt_rand(-2,2)	,$color, $this->font, $this->code[$i]);
		}
	}

	// 在画布上画出曲线
	private function createLineThrought(){
		$color=imagecolorallocate($this->image, $this->textcolor['r'], $this->textcolor['g'], $this->textcolor['b']);

		$y=$this->height+mt_rand(-10,10);
		for ($i=0; $i < 4; $i++) { 
			imagearc($this->image, $this->width/2, $y+$i, $this->width+70, $this->height, 200+mt_rand(-20,20), 300+mt_rand(-20,20), $color);	
		}
	}

	// 在画布上随机产生字符作为干扰
	private function createDisturb(){
		$num=$this->codeNum*4;
		for ($i=0; $i < 15; $i++) { 
			$color=imagecolorallocate($this->image,mt_rand(150,255), mt_rand(150,255), mt_rand(150,200));
			imagestring($this->image, 3, mt_rand(0,$this->width), mt_rand(0,$this->height), $this->allcode[mt_rand(0,strlen($this->allcode)-1)], $color);
		}
	}

	// 完成画验证码的全过程
	private function createVcode(){
		$this->image=imagecreatetruecolor($this->width,$this->height);
		$this->createBg();
		$this->createDisturb();
		$this->createText();
		$this->createLineThrought();
	}

	// 对外接口，用来生成验证码
	public function entry(){
		header('Content-Type:image/png');

		$this->createVcode();

		imagepng($this->image);
		imagedestroy($this->image);
	}

	// 对外接口，用来获取验证码的值
	public function getCode(){
		return $this->code;
	}

}

?>