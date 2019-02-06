<?php
//header('Content-Type: application/xml; charset=utf-8');
//VakıfBank Sanal Pos
//Bedrettin KEÇECİ
error_reporting(E_ALL);
ini_set("display_errors", 1);
class Payment{
	var $mpiServiceUrl=	"https://3dsecure.vakifbank.com.tr/MPIAPI/MPI_Enrollment.aspx";
	var $trueAddress=	"https://id.inescongress.com/TempSuccessUrl.php";
	var $falseAddress=	"https://id.inescongress.com/TempSuccessUrl.php";
	var $merchantId = ''; //üye işyeri id
	var $merchantPassword = ''; // üye işyeri şifre
	var $cardNumber;
	var $expiredDate;
	var $cardCode;
	var $cardType = 100;
	var $price;
	var $installment = NULL;
	var $currency = 840;
	var $paymentId;
	var $token;
	var $result;


	public  function setCardNumber ($par) {
		$this->cardNumber = $par;
	}

	public  function setExpiredDate ($par) {
		$this->expiredDate = $par;
	}

	public  function setCardCode ($par) {
		$this->cardCode = $par;
	}

	public  function setCardType ($par) {
		$this->cardType = $par;
	}

	public  function setPrice ($par) {
		$this->price = $par;
	}

	public  function setInstallment ($par) {
		$this->installment = $par;
	}

	public  function setCurrency ($par) {
		$this->currency = $par;
	}

	public  function setPaymentId ($par) {
		$this->paymentId = $par;
	}

	public  function setToken ($par) {
		$this->token = $par;
	}

	public  function setTrueAddress ($par) {
		$this->trueAddress = $par;
	}

	public  function setFalseAddress ($par) {
		$this->falseAddress = $par;
	}

	public function init() {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$this->mpiServiceUrl);
		curl_setopt($ch,CURLOPT_POST,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type"=>"application/x-www-form-urlencoded"));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"Pan=$this->cardNumber&ExpiryDate=$this->expiredDate&PurchaseAmount=$this->price&Currency=$this->currency&BrandName=$this->cardType&VerifyEnrollmentRequestId=$this->paymentId&SessionInfo=$this->token&MerchantId=$this->merchantId&MerchantPassword=$this->merchantPassword&SuccessUrl=$this->trueAddress&FailureUrl=$this->falseAddress&InstallmentCount=$this->installment");
		$resultXml = curl_exec($ch);
		curl_close($ch);

		$resultDocument = new DOMDocument();
		$resultDocument->loadXML($resultXml);

		$statusNode = $resultDocument->getElementsByTagName("Status")->item(0);
		$status = "";
		if( $statusNode != null )
			$status = $statusNode->nodeValue;


		$PAReqNode = $resultDocument->getElementsByTagName("PaReq")->item(0);
		$PaReq = "";
		if( $PAReqNode != null )
			$PaReq = $PAReqNode->nodeValue;
		$ACSUrlNode = $resultDocument->getElementsByTagName("ACSUrl")->item(0);
		$ACSUrl = "";
		if( $ACSUrlNode != null )
			$ACSUrl = $ACSUrlNode->nodeValue;
		$TermUrlNode = $resultDocument->getElementsByTagName("TermUrl")->item(0);
		$TermUrl = "";
		if( $TermUrlNode != null )
			$TermUrl = $TermUrlNode->nodeValue;
		$MDNode = $resultDocument->getElementsByTagName("MD")->item(0);
		$MD = "";
		if( $MDNode != null )
			$MD = $MDNode->nodeValue;
		$messageErrorCodeNode = $resultDocument->getElementsByTagName("MessageErrorCode")->item(0);
		$messageErrorCode = "";
		if( $messageErrorCodeNode != null )
			$messageErrorCode = $messageErrorCodeNode->nodeValue;

			$result = new \StdClass();
			$result->Status=$status;
			$result->PaReq=$PaReq;
			$result->ACSUrl=$ACSUrl;
			$result->TermUrl=$TermUrl;
			$result->MerchantData=$MD;
			$result->MessageErrorCode=$messageErrorCode;
			$this->result=$result;
	}

	public function response() {
		return $this->result;
	}

}

?>
