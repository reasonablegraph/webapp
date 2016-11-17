#!/bin/bash

#HOST=laravel.local
. ./host.conf

if [ $# -eq 0 ]
then
	curl -v "http://$HOST/prepo/graphviz" > /tmp/g.dot
else 
	curl -v "http://$HOST/prepo/graphviz?i=$1" > /tmp/g.dot
fi

dot -Tpng  -o /tmp/g.png /tmp/g.dot 
eog /tmp/g.png
