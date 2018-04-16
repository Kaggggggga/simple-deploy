#!/usr/bin/env bash
set -o errexit
set -o pipefail

dir=$1
docker_registry=$3
image=$2
#dir=$(dirname $(realpath $0))

cd $dir
# build for testing
docker build --no-cache -t $image:test -f Dockerfile.test .
docker run -it $image:test  /usr/bin/php /app/vendor/bin/phpunit

# build and push for deploy
docker build --no-cache -t $image:latest -f Dockerfile .
docker image tag $image $docker_registry/$image
docker push $docker_registry/$image

