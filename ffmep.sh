#!/bin/bash


if expr $1 : "[A-Za-z0-9\_]*\.ogv" 
	then :
	else ffmpeg2theora $1 -o $1.ogv
fi
#ffmpeg2theora $1 -o $1.ogv
