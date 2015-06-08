#! /bin/bash

#Created by: Edward Rodriguez
#Date: 2013-04-30
#Time: 11:35 AM

function usage() {
    echo ""
    echo "---=======BlackSniffer Module Version 1.0.0.1---======="
    echo "Usage: $0 srcip /path/to/logfile.bsc"
    echo "Example: $0 127.0.0.1 /tmp/logs/18092222222_20130430.bsc"
    echo ""
}

if [ -n "$1" ] ; then
    if [ -n "$2" ] ; then
        if ps -ef | grep -v grep | grep  "host $1" ; then
            #Sniffing for the incoming ip is already in place
            exit 0
        else
            #Start sniffing protocol for incoming ip
            /usr/sbin/tcpdump -s 65534 -ni eth0 host "$1" -C 100 -w "$2" &
        fi
    else
        usage
    fi
else
    usage
fi
