#!/usr/bin/env bash
set -o errexit
set -o pipefail

dir=$1
service=$2
cd $dir
# deploy
docker stack deploy -c docker-compose.yml $service