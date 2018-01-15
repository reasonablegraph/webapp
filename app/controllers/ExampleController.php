<?php

class ExampleController extends BaseController  {



  public function ex1() {


    //$view = View::make('greeting')->nest('child', 'child.view', $data);

    $data=array(
      'name'=>'World',
      'v1'=>'KOKO',
      'child2'=>'c2',
    );

    $c1_data=array(
      'v1'=>'LALA',
    );


    $childs=array(
      array('child1', 'examples.child.inc1',$c1_data),
      array('child2', 'examples.child.inc2',$c1_data),
    );

    $children = array();
    $view = View::make('examples/ex1');
    foreach ($childs as $c){
      $children[] = $c[0];
        $view->nest($c[0],$c[1],$c[2]);
    }

    $data['children']  =$children;


    //return $view->with($data);
    $html = $view->with($data)->render();
    return $html;







  }



}