#!/bin/bash
# Author: Blake, Kuo-Lien Huang and Steven Shiau <steven _at_ nchc org tw>
# License: GPL
# Description: send commands to drbl clients
# modified for Restonux by : Nicolas Adba

# Load DRBL setting and functions
DRBL_SCRIPT_PATH="${DRBL_SCRIPT_PATH:-/opt/drbl/}"

. $DRBL_SCRIPT_PATH/sbin/drbl-conf-functions

# some userful function
usage() {
  echo "Usage: `basename $0` Options [command]"
  echo "  Options:"
   language_help_prompt_by_idx_no
  echo "  -u, --user USER   execute command on all drbl clients as username USER"
  echo "  -n, --no-ping     do NOT ping machine to check if hosts is alive before running command"
  echo "  -b, --batch       batch mode, same with -n/--no-ping"
  echo "  -h, --hosts IP_LIST  Instead of all DRBL clients, assign the clients by IP address, like: -h \"192.168.0.1 192.168.0.2\" NOTE!!! You must put \" \" before and after the IP_LIST!"
  echo " Examples:"
  echo " To poweroff all DRBL clients"
  echo " `basename $0` -u root /sbin/poweroff"
  echo " To reboot all Cygwin clients, use"
  echo " `basename $0` -u Administrator /usr/bin/reboot -f now"
}

# main
[ $# -eq 0 ] && usage && exit 1

# Parse command
while [ $# -gt 0 ]; do
  case "$1" in
    -h|--hosts)
	shift
        if [ -z "$(echo $1 |grep ^-.)" ]; then
          # skip the -xx option, in case 
	  IP_LIST="$1"
	  shift
        fi
	;;
    -u|--user)
	shift
        if [ -z "$(echo $1 |grep ^-.)" ]; then
          # skip the -xx option, in case 
	  specified_user="$1"
	  shift
        fi
	;;
    -n|--no-ping|-b|--batch)
	shift; ping="no" ;;
    -*)	echo "${0}: ${1}: invalid option" >&2
	usage >& 2
	exit 2 ;;
    *)	break ;;
  esac
done

# get the command
CMD=$1


# execute the command
for IP in $IP_LIST; do
  # skip those IP not listed in the $IP_LIST

  # check if $IP is myself
  me="$(LC_ALL=C ifconfig | grep "$IP" | cut -d: -f2 | cut -d' ' -f1)"
  if [ "$me" = "" -o "$me" != "$IP" ]; then
    echo "Running command \"$@\" in $specified_user@$IP..."
    if [ "$ping" = "no" ]; then
      ssh -o StrictHostKeyChecking=no $specified_user@$IP $@ &
    else
      # ping before run command
      if ping -c 2 $IP > /dev/null 2>&1; then
          [ "$BOOTUP" = "color" ] && $SETCOLOR_SUCCESS
          echo "$IP is alive... Run command now..."
          ssh -o StrictHostKeyChecking=no $specified_user@$IP $@ &
          [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
      else
          [ "$BOOTUP" = "color" ] && $SETCOLOR_FAILURE
          echo "$IP is unreachable... Abort command." 
          [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
      fi
    fi
  fi
done
exit 0
