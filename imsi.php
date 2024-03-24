<?php

$contents = 'aaa <img src="aaa.gif"> ddddd

dddd

ddd
<img src="aaabbbbbbbbb.glf">
ddd
';

preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $contents, $matches);

print_r($matches);