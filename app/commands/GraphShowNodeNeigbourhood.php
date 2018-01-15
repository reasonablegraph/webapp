<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GraphShowNodeNeigbourhood extends Command {

  protected $name = 'graph:show-node-neigbourhood';
  protected $description = 'graph show node neigbourhood';

  public function __construct() {
    parent::__construct();
  }

  protected function getArguments() {
    return array(
      array('id', InputArgument::REQUIRED, 'The node item id.'),
    );
  }

  protected function getOptions() {
    return array(
      array('inferred', null, InputOption::VALUE_OPTIONAL, 'true/false', 'true'),
    );
  }

  public function fire() {

    $inferred_flag = $this->option('inferred');
    $inferred_flag = ($inferred_flag == 'true') ? true : false;

    $id_arg = (int) filter_var($this->argument('id'),FILTER_VALIDATE_INT);
    $graph = GGraphIO::loadGraph(null, $inferred_flag, false);
    $root = $graph->getVertexByPersisteceId($id_arg);
    if (empty($root)){
      trigger_error("NODE WITH ID: $id_arg NOT EXISTS");
    }
    $root->setAttribute(GGraphUtil::DUMPGRAPHVIZ, true);
    $root->setAttribute('GRAPHVIZ_ROOT', true);

    $neighbourhood = null;
    $neighbourhood_all = $root->getAttribute("neighbourhood");
    if (! empty($neighbourhood_all) && isset($neighbourhood_all['def'])){
      $neighbourhood = $neighbourhood_all['def'];
    } else {
      $neighbourhood = array();
    }


    $nc = 1;
    foreach ($neighbourhood as $nid){
      $nc +=1;
      $node = $graph->getVertexByPersisteceId($nid);
      if (!empty($node)){
        $node->setAttribute(GGraphUtil::DUMPGRAPHVIZ, true);
      }
    }

    $glabel="neighbourhood for: "  . $id_arg .  " nodes count: " . $nc;
    GGraphUtil::dumpGraphviz($graph,null, $inferred_flag, false, true, $glabel, null, true);

  }

}
