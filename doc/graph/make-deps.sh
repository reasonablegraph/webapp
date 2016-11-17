#!/bin/bash

BP=/opt/ins/dev/laravel/app/lib/graph/
DOT_FILE=class-diagram-graph.dot
PNG_FILE=class-diagram-graph.png
SRC_FILES="$BP/GEdge.php $BP/GVertex.php $BP/GProperty.php $BP/GNode.php"

echo "digraph finite_state_machine {" > $DOT_FILE
echo "rankdir=LR;" >> $DOT_FILE


#cat $BP/GEdge.php $BP/GVertex.php $BP/GProperty.php \
#| grep extends  \
#| sed s/,//g \
#| awk '{ print "\42" $2 "\42 -> \42" $4 "\42;" }' \
#>> $DOT_FILE



cat $SRC_FILES \
| grep "//DOT1:"  \
| sed s/\\/\\/DOT1:// \
>> $DOT_FILE

cat $SRC_FILES \
| grep "//DOT2:"  \
| sed s/\\/\\/DOT2:// \
>> $DOT_FILE

echo "}" >> $DOT_FILE;


cat $DOT_FILE;
dot -Tpng  -o $PNG_FILE $DOT_FILE
eog $PNG_FILE

