<?php 
namespace system;
class Model{
	private $connect;
	private $username;
	private $pwd;
	private $host;
	protected $tableName;
	private $dbname;
	private $field='';
	private $fields='';
	private $limit;
	private $order;
	private $where;
	private $fieldAll=[];
	private $primary='id';
	public function __construct()
	{
		$config=require PATH."/config/db.php";
		$arr=$config['db'];
		foreach ($arr as $key => $val) {
			$this->$key=$val;
		}
		$this->connect=new \PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8",$this->username,$this->pwd);
		$this->tableName=$this->setTablename();
		$this->getFields();
	 
	}

	public function setTablename()
	{
		return strtolower(ltrim(strrchr(get_class($this),'\\'),'\\'));
	}

	public function getFields()
	{
		$path=RUNTIME.'/'.'fields';
		is_dir($path) || mkdir($path,0777,true);
		$pathname=$path.'/'.$this->tableName.'.frm';
		$sql="desc ".$this->tableName;
		if (file_exists($pathname)) {
			$this->fieldAll=unserialize(file_get_contents($pathname));
		}else{
			$this->fieldAll=$this->exec($sql,[],'select');
			file_put_contents($pathname,serialize($this->fieldAll));
		}
		foreach ($this->fieldAll as $key => $value) {
			$this->fields.=$value['Field'].',';
			if ($value['Key']=='PRI') {
			$this->primary=$value['Field'];
			}
		}
		$this->fields=rtrim($this->fields,',');
	}


	public function __call($method,$params){
		if(substr($method,0,5) == "getBy"){
			$field = strtolower(substr($method,5)); // 得到字段名
			// 当前字段是否在表中存在
			if(strpos($this->fields,$field)!== false){
				if(count($params)>0){
					$value = $params[0];
				}
				// 进行查询
				$this->where([$field=>$value]);
				return $this->select();
			}
		}
		throw(new \Exception("对不起,方法不存在")); 
	}


	public function getPrimary(){
		return $this->primary;
	}


	public function insert($arr){
		if (is_array($arr) && count($arr)>0) {
			$sql="insert into ".$this->tableName." set ";
			foreach ($arr as $key => $val) {
				$values[]=$key."='".$val."'";
			}
			$sql.=implode(',',$values);
		}
		if (is_string($arr)) {
			$sql=$arr;
		}
		
		return $this->exec($sql,[],'insert');
	}


	public function table($tableName=''){
		if($tableName){
			$this->tableName = $tableName;
		}
		return $this;
	}

	
	public function delete(){
		$sql="delete ";
		$sql.=$this->merge('delete');
		return $this->exec($sql,[],'delete');
	}

	public function merge($qwer='select')
	{
		$sql=$qwer;
		if($qwer !== 'delete'){
			if($this->field){
				$sql.= ' '.$this->field;
			}else{
				$sql.= ' '. $this->fields;
			}
		}
		if ($this->tableName) {
			$sql.=" from ".$this->tableName;
		}
		if ($this->where) {
			$sql.=" where ".$this->where;
		}
		if ($this->order) {
			$sql.=" order by ".$this->order;
		}
		if ($this->limit) {
			$sql.=" limit ".$this->limit;
		}
		return $sql;

	}

	public function where($arr)
	{
		$comple=isset($arr['comple'])?" ".$arr['comple']." ":' and ';
		if (is_array($arr) && count($arr)>0) {
			foreach ($arr as $key => $value) {
				if ($key!='comple') {
					$where[]=($key==="?" || strpos($key,':')===0) ? $key."=".$value : $key."='".$value."'";
				}
			}
			$this->where=implode($comple,$where);
		}
		if (is_string($arr)) {
			$this->where=$arr;
		}
		return $this;
	}

	public function orderBy($arr)
	{
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
			$this->order=$val.$key;
			}
		}
		if (is_string($arr)) {
			$this->order=$arr;
		}
		return $this;
	}

	public function limit($num,$offset=0){
		$this->limit = $offset.','.$num;
		return $this;
	}

	public function exec($sql,$arr=[],$qwer='select')
	{
		if (is_array($arr) && count($arr)>0) {
			$stm=$this->connect->prepare($sql);
			foreach ($arr as $key => $val) {
				$stm->bindValue($key,$val);
			}
			$stm->execute();
			return $stm;
		}
		if ($qwer=='select') {
			return $this->connect->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		}

		return $this->connect->exec($sql);
	}

	public function save($arr){
		if (is_array($arr) && count($arr)>0) {
			$sql='update '.$this->tableName.' set ';
			foreach ($arr as $key => $val) {
				$sql.=$key." = '".$val."'";
			}
			if ($this->where) {
				$sql.= " where ".$this->where;
			}
		}
		if (is_string($arr)) {
			$sql=$arr;
		}
		return $this->exec($sql,[],'update');
	}


	public function select($arr=[]){
		$sql=$this->merge();
		$res=$this->exec($sql,$arr,'select');
		return $res;
	}


}													


 ?>