<?php 
namespace system;
class Pagination{

	public $count='';//总条数
	public $length='';//每页显示行数
	public $sum_page='';//总页数
	public $p='p';	//接收的当前页
	public $now_page=1;//当前页
	public $url="";//地址
	public $rollPage=5;//分页栏显示几个
	protected $param;
	public function __construct($count,$pageSize='',$param=array())
	{
		$this->count=$count;
		if ($pageSize>0) $this->length=intval($pageSize);
		$this->sum_page=ceil($count/$this->length);
		$this->param=empty($param) ? $_GET : intval($_GET[$this->p]);
		$this->now_page=isset($_GET[$this->p]) ? $_GET[$this->p] : 1;
		$this->now_page=$this->now_page>0 ? $this->now_page : 1;
		if($this->now_page && $this->now_page>$this->sum_page)$this->now_page>$sum_page;

	}

	public function limit()
	{
		return (($this->now_page-1) * $this->length);
	}

	public function getUrl()
	{
		 $info=parse_url($_SERVER['REQUEST_URI']);
		 $this->param[$this->p] = "[PAGE]";

		 $where=[];
		 foreach ($this->param as $k => $v) {
		 	$where[]=$k.'='.urlencode($v);
		 }
		 return $info['path'].'?'.implode('&',$where);
	}
	

	public function url($pageNum)
	{
		return str_replace(urlencode("[PAGE]"), $pageNum, $this->url);
	}


	// 生成分页字符串
	public function show(){
		if($this->sum_page == 0 ) return '';
		$this->url = $this->getUrl();
		$now_cool_page = $this->rollPage/2;
		$page_str = "<a href='%s'>%s</a>"; // 显示的模板

		// 上一页
		$up_row = $this->now_page - 1;
		$up_page = $up_row > 0 ? sprintf($page_str,$this->url($up_row),'上一页') : '';

		// 下一页
		$down_row = $this->now_page + 1;
		$down_page = $down_row <= $this->sum_page ? sprintf($page_str,$this->url($down_row),'下一页') : '';

		// 首页
		$first = '';
		if($this->sum_page > $this->rollPage && ($this->now_page - $now_cool_page) >=1){
			$first = sprintf($page_str,$this->url(1),'首页');
		}

		// 尾页 
		$end = '';
		if($this->sum_page > $this->rollPage && ($this->now_page + $now_cool_page) < $this->sum_page){
			$end = sprintf($page_str,$this->url($this->sum_page),'尾页');
		}

		$pageStr = '';
		// 数字页码的生成 $this->nowPage - $this->now_cool_page + $i 
		for($i=1;$i<=$this->rollPage;$i++){
			if($this->now_page - $now_cool_page <=0 ){
				$page = $i;
			}elseif($this->now_page + $now_cool_page -1 > $this->sum_page){
				$page = $this->sum_page - $this->rollPage + $i;
			}else{
				$page = $this->now_page - ceil($now_cool_page) + $i;
			}

			if($page!=$this->now_page){
				$pageStr.= sprintf($page_str,$this->url($page),$page);
			}else{
				$pageStr.= "<span>{$page}</span>";
			}
		}

		$pageStr = $first.$up_page.$pageStr.$down_page.$end;
		return $pageStr;
	}



} ?>