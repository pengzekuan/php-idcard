<?php
namespace Identity;

class IDCard {
  public $idCard;

  public function __construct($idCard) {
    $this->idCard = $this->fixIdCard((string)$idCard);
    if (!preg_match('/^\d{15}$|^\d{17}$|^\d{17}(\d|x|X)$/', $this->idCard)) {
      throw new Exception('invalid identify.');
    }
  }

  private function fixIdCard($idCard) {
    $len = strlen($idCard);
    if ($len === 15) {
      // add to 17
      $idCard = substr($idCard, 0, 6).'19'.substr($idCard, 6);
    }
    if ($len === 17) {
      $idCard .= $this->getVerifyCode($idCard);
    }
    return $idCard;
  }

  private function getVerifyCode($idCard = null) {
    $idCard = $idCard ? $idCard : $this->idCard;
    $len = strlen($idCard);
    if ($len < 17) {
      return false;
    }
    $indexes = [];
    for($i = 18; $i > 0; $i--) {
      $indexes[] = $i;
    }
    // 加权因子
    $factor = array_map(function($num) {
      return pow(2, $num - 1) % 11;
    }, $indexes);

    $sum = 0;
    for($i = 0; $i < $len - 1; $i++) {
      $sum += $idCard[$i] * $factor[$i]; 
    }

    $checkNum = (12 - $sum % 11) % 11;

    if ($checkNum === 10) {
      $checkNum = 'X';
    }

    return $checkNum;
  }

  public function valid() {
    if (strlen($this->idCard) !== 18) {
      return false;
    }
    if (strtoupper(substr($this->idCard, -1)) !== strtoupper($this->getVerifyCode($this->idCard))) {
      return false;
    }
    return true;
  }

  public function getYear() {
    return substr($this->idCard, 6, 4);
  }

  public function getMonth() {
    return substr($this->idCard, 10, 2);
  }

  public function getDay() {
    return substr($this->idCard, 12, 2);
  }

  public function getSex() { // 1 male 0 female
    return substr($this->idCard, 16, 1) % 2;
  }
}
