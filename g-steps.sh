#!/bin/bash


dot -Tpng  -o /tmp/g-1.png /tmp/g1.dot
dot -Tpng  -o /tmp/g-2.png /tmp/g2.dot 
dot -Tpng  -o /tmp/g-3.png /tmp/g3.dot  
eog /tmp/g-?.png
