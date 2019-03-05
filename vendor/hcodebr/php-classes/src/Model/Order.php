<?php 

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model{
	const ERROR = "Order-Error";
	const SUCCESS = "Order-Success";


	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_orders_save (:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)", array(
			":idorder"=>$this->getidorder(),
			":idcart"=>$this->getidcart(),
			":iduser"=>$this->getiduser(),
			":idstatus"=>$this->getidstatus(),
			":idaddress"=>$this->getidaddress(),
			":vltotal"=>$this->getvltotal()	
		));
		if (count($results) > 0) {
			$this->setData($results[0]);
		}

	}

	public function get($idorder){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_orders a JOIN tb_ordersstatus b USING(idstatus) JOIN tb_carts c USING(idcart) JOIN tb_users d ON d.iduser = a.iduser JOIN tb_addresses e USING(idaddress) JOIN tb_persons f ON f.idperson = d.idperson WHERE a.idorder = :idorder", array(
			":idorder"=>$idorder
		));

		if (count($results) > 0) {
			$this->setData($results[0]);
		}

	}

	public static function listAll(){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_orders a JOIN tb_ordersstatus b USING(idstatus) JOIN tb_carts c USING(idcart) JOIN tb_users d ON d.iduser = a.iduser JOIN tb_addresses e USING(idaddress) JOIN tb_persons f ON f.idperson = d.idperson ORDER BY a.dtregister DESC");

		return $results;

	}
	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", array(
			":idorder"=>$this->getidorder()
		));

	}

	public function getCart():Cart{

		$cart = new Cart();

		$cart->get((int)$this->getidorder());

		return $cart;

	}

	public static function setMsgError($msg){

	    $_SESSION[Order::ERROR] = (string)$msg;

	}   

	public static function getMsgError(){


	    $msg =  (isset($_SESSION[Order::ERROR])) && $_SESSION[Order::ERROR] ? $_SESSION[Order::ERROR] : "";

	    Order::clearMsgError();

	    return $msg;

	}

	public static function clearMsgError(){

	    $_SESSION[Order::ERROR] = NULL;

	}

	public static function setMsgSuccess($msg){

        $_SESSION[Order::SUCCESS] = (string)$msg;

    }   

    public static function getMsgSuccess(){


        $msg =  (isset($_SESSION[Order::SUCCESS])) && $_SESSION[Order::SUCCESS] ? $_SESSION[Order::SUCCESS] : "";

        Order::clearMsgSuccess();

        return $msg;
    
    }

    public static function clearMsgSuccess(){

        $_SESSION[Order::SUCCESS] = NULL;

    }

     public static function getPage($page = 1, $itemsPerPage = 10){

        $start= ($page-1)*$itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT
            sql_calc_found_rows
            * FROM tb_orders a 
            JOIN tb_ordersstatus b USING(idstatus) 
            JOIN tb_carts c USING(idcart) 
            JOIN tb_users d ON d.iduser = a.iduser 
            JOIN tb_addresses e USING(idaddress) 
            JOIN tb_persons f ON f.idperson = d.idperson 
            ORDER BY a.dtregister DESC
            LIMIT $start, $itemsPerPage;
            ");

        $resultTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal;");

        return array(
            'data'=>$results,
            'total'=>(int)$resultTotal[0]["nrtotal"],
            'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
        );

    }

    public static function getPageSearch($search, $page = 1, $itemsPerPage = 10){

        $start= ($page-1)*$itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT
            sql_calc_found_rows
            * FROM tb_orders a 
            JOIN tb_ordersstatus b USING(idstatus) 
            JOIN tb_carts c USING(idcart) 
            JOIN tb_users d ON d.iduser = a.iduser 
            JOIN tb_addresses e USING(idaddress) 
            JOIN tb_persons f ON f.idperson = d.idperson 
            WHERE a.idorder = :id OR f.desperson LIKE :search 
            ORDER BY a.dtregister DESC
            LIMIT $start, $itemsPerPage;
            ", array(
                ":search"=>'%'.$search.'%',
                ":id"=>$search
            ));

        $resultTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal;");

        return array(
            'data'=>$results,
            'total'=>(int)$resultTotal[0]["nrtotal"],
            'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
        );

    }


}


 ?>