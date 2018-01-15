<?php

interface SolrQueryContext {


  public function escapeTerm($term);
}

class DefaultSolrQueryContext  implements  SolrQueryContext {

  public function escapeTerm($term){
    return $term;
  }

}

class SolariumQueryContext  implements  SolrQueryContext {
  private $query;
  private $helper;
  public function __construct($query) {
    $this->query = $query;
    $this->helper = $query->getHelper();
  }

  public function escapeTerm($term){
    $t =  $this->helper->escapePhrase($term);
    return $t;
  }

}

interface SolrQueryToken {
  /**
   * @param SolrQueryContext $context
   * @return SolrQueryContext
   */
  public  function createQuery($context);
}

class StringToken implements SolrQueryToken {

  private $token;

  public function __construct($token) {
    $this->token =$token;
  }


  /**
   * @param SolrQueryContext $context
   * @return string
   */
  public function createQuery($context) {
    return $this->token;
  }

}

class MatchToken implements  SolrQueryToken {

  private $key;
  private $term;

  public function __construct($key,$term) {
    $this->key = $key;
    $this->term = $term;
  }

  /**
   * @param SolrQueryContext $context
   * @return string
   */
  public function createQuery($context) {
    return $this->key . ':' . $context->escapeTerm($this->term);
  }


  public static function create($key,$term){
    return new MatchToken($key,$term);
  }

}


class LogicalBlock implements  SolrQueryToken  {

  private $operator;
  private $tokens = array();
  private $addExternalBrackets  = false;

  public function __construct($operator, $addExternalBrackets = true) {
    $this->operator  = $operator;
    $this->addExternalBrackets = $addExternalBrackets;
  }

  /**
   * @param SolrQueryToken $token
   */
  public function addToken($token){
    $this->tokens[] = $token;
  }


  public function addTokenMatch($key,$value){
    $this->tokens[] =SolrFilter::createTokenMatch($key,$value);
  }
  public function addTokenString($token){
    $this->tokens[] =SolrFilter::createTokenString($token);
  }


  public function addBlockAND($addExternalBrackets = true){
    $block = SolrFilter::createBlockAND($addExternalBrackets);
    $this->addToken($block);
    return $block;
  }

  public function addBlockOR($addExternalBrackets = true){
    $block = SolrFilter::createBlockOR($addExternalBrackets);
    $this->addToken($block);
    return $block;
  }


  /**
   * @param SolrQueryContext $context
   * @return string
   */
  public function createQuery($context) {
    $addBrackets = $this->addExternalBrackets;
    $my_op = ' ' . $this->operator . ' ';
    $rep = $addBrackets ? '( ': '';
    $op = '';
    foreach ($this->tokens as $token){
      $rep .= ($op . $token->createQuery($context));
      $op = $my_op;
    }
    if ($addBrackets) {
      $rep .= ' )';
    }
    return $rep;
  }
}



class logicalBlockAND  extends LogicalBlock implements  SolrQueryToken  {
  public function __construct($addExternalBrackets = true) {
    parent::__construct('AND', $addExternalBrackets);
  }
}
class logicalBlockOR  extends LogicalBlock implements  SolrQueryToken  {
  public function __construct($addExternalBrackets = true) {
    parent::__construct('OR', $addExternalBrackets);
  }
}


class SolrFilter  extends LogicalBlock implements  SolrQueryToken  {

  const TYPE_AND = 0;
  const TYPE_OR = 1;

  public function __construct($type = 0) {
    if ($type == 0) {
      parent::__construct('AND',false);
    } else if ($type == 1) {
      parent::__construct('OR',false);
    } else {
      trigger_error("UNKNOWN SolrFilter type " . $type);
    }
  }


  public static function createFilter($type =0){
    return new SolrFilter($type);
  }


  public static function createBlockAND($addExternalBrackets = true){
    return new LogicalBlockAND($addExternalBrackets);
  }

  public static function createBlockOR($addExternalBrackets = true){
    return new LogicalBlockOR($addExternalBrackets);
  }


  public static function createTokenString($token){
    return new StringToken($token);
  }

  public static function createTokenMatch($key,$token){
    return new MatchToken($key,$token);
  }


  /**
   * @param SolrQueryContext $context
   * @return string
   */
  public function createQuery($context) {
    return parent::createQuery($context);
  }


  /**
   * @return string
   */
  public function __toString() {
    return $this->createQuery(new DefaultSolrQueryContext());
  }
}





