#!/bin/bash

if [ "$1" == "" ]
then
	echo "Syntax:  codesign.sh <file(s)>"
	exit -1
fi

OS=`uname -a | cut -c1-6`
if [ "$OS" == "Darwin" ]
then
	if [ "${APPLICATION_CERT}" == "" ]
	then
		echo "Please define environment variable APPLICATION_CERT with your developer ID for codesign to work"
	else
		echo /usr/bin/codesign --force --sign "${APPLICATION_CERT}" -o runtime --timestamp=none $@
		/usr/bin/codesign --force --sign "${APPLICATION_CERT}" -o runtime --timestamp=none $@
	fi
fi

