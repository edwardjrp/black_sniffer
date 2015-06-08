#! /bin/bash

#Created by: Edward Rodriguez
#Date: 2013-05-01
#Time: 14:44 AM

function usage() {
    echo ""
    echo "---=======BlackSniffer Module Version 1.0.0.1---======="
    echo "Usage: $0 srcip"
    echo "Example: $0 127.0.0.1"
    echo ""
}

if [ -n "$1" ] ; then
    PID=$(pgrep -f "host $1")
    if [ -n "$PID" ] ; then
        #Stoping PID gracefully with a keyboard interruption signal
        kill -2 $PID
        exit 0
    else
        #Not PID exists with given ip
        exit 0
    fi
else
    usage
fi
